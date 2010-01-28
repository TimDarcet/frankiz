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

class group
{
    const HIDDEN  = 'hidden';   // Hidden groups
    const FORCED  = 'forced';   // Forced groups (formations, ...)
    const CLUB    = 'club';     // Binets, ...
    const FREE    = 'free';     // Fun, psc, ...

    private static $groups        = null;
    private static $groups_layout = array(self::HIDDEN => array(), self::FORCED => array(), self::CLUB => array(), self::FREE => array());

    private static function _checkType($type = '')
    {
        if ($type != '' && is_string($type))
        {
            if($type == self::HIDDEN || $type == self::FORCED || $type == self::CLUB || $type == self::FREE) return $type;
        }
        return false;
    }

    private static function _sortByRank($ga, $gb)
    {
        if ($ga['rank'] == $gb['rank']) return 0;
        return ($ga['rank'] < $gb['rank']) ? -1 : 1;
    }

    // Load all the groups of the logged user if not already loaded
    private static function _load()
    {
        if (is_null(self::$groups))
        {
            self::$groups = array();
            $iter = XDB::iterator('SELECT g.gid gid, g.type type, g.name name, g.long_name long_name,
                                          ug.rank rank, ug.job job, ug.title title
                                     FROM users_groups AS ug
                               INNER JOIN groups AS g
                                       ON ug.gid = g.gid
                                    WHERE ug.uid = {?}
                                 ORDER BY g.type, ug.rank',
                                    S::user()->id());

            while ($group = $iter->next()) {
                $gid  = $group['gid'];
                $type = $group['type'];

                self::$groups[$gid] = array('gid' => $gid,
                                           'type' => $type,
                                           'name' => $group['name'],
                                      'long_name' => $group['long_name'],
                                           'rank' => $group['rank'],
                                            'job' => $group['job'],
                                          'title' => $group['title']);
            }
        }
    }

    // Returns the groups in the user-selected order for displaying
    public static function getLayout($type)
    {
        self::_load();
        $type = self::_checkType($type);

        if ($type) {
            $groups = array();
            foreach(self::$groups as $group)
            {
                if ($group['type'] == $type) $groups[] = $group;
            }

            usort($groups, array('self', "_sortByRank"));
            return $groups;
        } else {
            return false;
        }
    }

    public function __construct($gid)
    {
        // # What for ?
    }

    public static function checkMembershipById($gid)
    {
        self::_load();

        return array_key_exists($gid, self::$groups);
    }

    public static function checkMembershipByName($name)
    {
        self::_load();

        foreach(self::$groups as $group)
            if ($group['name'] == $name) return true;
        return false;
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
