<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
 *  http://br.binets.fr/                                                   *
 *                                                                         *
 *  This program is free software; you can redistribute it and/or modify   *
 *  it under the terms of the GNU General Public License as published by   *
 *  the Free Software Foundation; either version 2 of the License, or      *
 *  (at your option) any later version.                                    *
 *                                                                         *
 *  This program is distributed in the hope that it will be useful,        *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 *  GNU General Public License for more details.                           *
 *                                                                         *
 *  You should have received a copy of the GNU General Public License      *
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/

/**
 * Classify IP addresses into categories
 */
class IPAddress
{
    const POLYTECHIQUE = 0x01; // IP from Polytechnique
    const INTERNAL     = 0x02; // "internal" zone (no proxies & DMZ, pits, salles infos)
    const STUDENT      = 0x04; // Student's room
    const PREMISE      = 0x08; // Binets premise
    const HAS_STUDENT  = 0x10; // IP in a zone which has access to student zone

    // string IP address
    private $ipAddr;

    // Origin of the IP address
    private $origin;

    public function __construct($ip) {
        $this->ipAddr = $ip;
        $this->origin = self::getOrigin($ip);
    }

    public function getAddr() {
        return $this->ipAddr;
    }

    /**
     * Get remote IP address, with reverse proxy support
     * @return IP
     */
    public static function getInstance()
    {
        global $globals;
        static $ipObject = null;

        // Cache
        if (!is_null($ipObject))
            return $ipObject;

        $ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');

        // Resolve reverse proxy configuration
        if (!empty($globals->core->remoteproxy)) {
            $remote_proxies = unflatten($globals->core->remoteproxy);
            $remote_proxies = array_map('trim', $remote_proxies);
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipList = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $ipList = array_map('trim', $ipList);
                for ($ipTest = $ip; $ipTest; $ipTest = array_pop($ipList)) {
                    if (!in_array($ipTest, $remote_proxies)) {
                        $ip = $ipTest;
                        break;
                    }
                }
            }
        }

        // Set cache and return an IP object
        return $ipObject = new IPAddress($ip);
    }

    /**
     * Return remote IP address
     * @return string IP
     */
    public static function get() {
        return self::getInstance()->getAddr();
    }

    /**
     * Get where the IP is from
     * @param string $ip IP
     * @return integer Binary combination of IPAddress:: constants
     *
     * TODO: add global cache
     */
    public static function getOrigin($ip)
    {
        // Localhost
        if ($ip == '127.0.0.1')
            return self::INTERNAL;

        // Not Polytechnique
        if (substr($ip, 0, 8) == '129.104.') {
            // Polytechnique
            $origin = self::POLYTECHIQUE;

            // Read 3rd number
            $digit4 = strpos($ip, '.', 9);
            $ip3 = (int)substr($ip, 8, $digit4 - 8);
            if (!$digit4 || !$ip3)
                return $origin;

            // Polytechnique's DMZ is external
            if ($ip3 == 30 || $ip3 == 247)
                return $origin;

            $origin |= self::INTERNAL;

            // 129.104.192.0/18 has access to student zone
            if ($ip3 >= 192)
                $origin |= self::HAS_STUDENT;

            // Query database to know wether this IP is a premise (=room for a group)
            // or a student room
            list($is_student, $is_premise) = XDB::fetchOneRow(
                'SELECT  EXISTS(SELECT  rid
                                  FROM  rooms_users AS ru
                                 WHERE  ru.rid = ips.room
                                ) AS stu,
                         EXISTS(SELECT  rid
                                  FROM  rooms_groups AS rg
                                 WHERE  rg.rid = ips.room
                                ) AS pre
                   FROM  ips
                  WHERE  ips.ip = {?}
                  LIMIT  1', $ip);

            if ($is_student)
                $origin |= self::STUDENT;
            if ($is_premise)
                $origin |= self::PREMISE;

            return $origin;
        }

        //School administration uses 172.16/16
        //Has access defined by POLYTECHNIQUE | INTERNAL
        if (substr($ip, 0, 7) == '172.16.') {
            return self::POLYTECHNIQUE | self::INTERNAL;
        }

        // External IP
        return 0;
    }

    /**
     * Test wether an IP is from X
     * @return boolean
     */
    public function is_polytechnique()
    {
        return (boolean)($this->origin & self::POLYTECHIQUE);
    }

    /**
     * Test wether an IP is X-internal
     * @return boolean
     */
    public function is_x_internal()
    {
        return ($this->origin & self::POLYTECHIQUE) && ($this->origin & self::INTERNAL);
    }

    /**
     * Test wether an IP is a student room
     * @return boolean
     */
    public function is_student()
    {
        return (boolean)($this->origin & self::STUDENT);
    }

    /**
     * Test wether an IP is a binet premise
     * @return boolean
     */
    public function is_premise()
    {
        return (boolean)($this->origin & self::PREMISE);
    }

    /**
     * Test wether this IP has acess to the X student IP zone
     * @return boolean
     */
    public function has_x_student()
    {
        return $this->is_x_internal() && ($this->origin & self::HAS_STUDENT);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
