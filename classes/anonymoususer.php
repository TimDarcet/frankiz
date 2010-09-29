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
        $this->minimodules = FrankizMiniModule::emptyLayout();
    }

    protected function loadGids()
    {
        // By default, everybody is a member of the top-level group
        $root = GroupsTreeInfo::get()->root();
        $this->gids[$root->gid()] = new PlFlagSet(Rights::MEMBER);

        // If connecting from a local, find associated groups
        if (IP::is_local())
        {
            $iter = XDB::iterator('SELECT  g.gid
                                     FROM  ips
                               INNER JOIN  rooms_owners AS ro
                                       ON  ro.rid = ips.rid
                               INNER JOIN  groups AS g
                                       ON  g.gid = ro.owner_id
                                    WHERE  ips.ip = {?}', IP::get());

            while ($array_group = $iter->next())
                $this->gids[$array_group['gid']] = new PlFlagSet(Rights::MEMBER);
        }
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