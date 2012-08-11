<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                       *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                     *
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
class IP
{
    const POLYTECHIQUE = 0x01; // IP from Polytechnique
    const INTERNAL     = 0x02; // "internal" zone (no proxies & DMZ, pits, salles infos)
    const STUDENT      = 0x04; // Student's room
    const PREMISE      = 0x08; // Binets premise

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
        return $ipObject = new IP($ip);
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
     * @return integer Binary combination of IP:: constants
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

            // Polytechnique's DMZ is external
            if (substr($ip, 0, 11) == '129.104.30.' || substr($ip, 0, 12) == '129.104.247.')
                return $origin;

            $origin |= self::INTERNAL;
            // Query database to know wether this IP is a premise (=room for a group)
            // or a student room
            list($is_student, $is_premise) = XDB::fetchOneRow(
                'SELECT  EXISTS(SELECT  rid
                                  FROM  rooms_users AS ru
                                 WHERE  ru.rid = ips.rid
                                ) AS stu,
                         EXISTS(SELECT  rid
                                  FROM  rooms_groups AS rg
                                 WHERE  rg.rid = ips.rid
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
        return $this->is_x_internal() && ($this->origin & (self::STUDENT|self::PREMISE));
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
