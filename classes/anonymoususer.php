<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                        *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                      *
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

class AnonymousUser extends User
{
    public function __construct()
    {
        $this->uid = 0;
    }

    public function groups()
    {
        if ($this->groups == null)
        {
            // By default, everybody can read the top-level group (0 - fkz - Frankiz.net)
            $root = Group::getTop();
            $this->groups = array($root->gid() => new PlFlagSet());

            // If connecting from a local, find associated groups
            if (IP::is_local())
            {
                $res = XDB::iterator('SELECT g.gid
                                         FROM rooms_ip AS ri
                                   INNER JOIN rooms_owners AS ro
                                           ON ro.rid = ri.rid
                                   INNER JOIN groups AS g
                                           ON g.gid = ro.owner_id
                                        WHERE ri.IP = {?}',
                                            IP::get());

                while ($array_group = $iter->next())
                    $this->groups[$array_group['gid']] = new PlFlagSet();
            }
        }

        return $this->groups;
    }

    public function loadMainFields()
    {
        $this->uid = 0;
        $this->perms = 'anonymous';
    }

    public static function makePerms($perms)
    {
        return new PlFlagSet();
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
