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

class Cluster
{
    const SPECIAL = 'special'; // special clusters (external, internal, ...)
    const LOBBY   = 'lobby';   // Default cluster for new members
    const MEMBER  = 'member';  // Belonging to one of thoses clusters means you're a validated member
    const WRITER  = 'writer';  // Can write and valid group's news/events
    const ADMIN   = 'admin';   // Can manage users and clusters

    protected $cid;
    protected $gid;
    protected $type;
    protected $name;

    public function __construct($raw)
    {
        $this->fillFromArray($raw);
    }

    protected function fillFromArray(array $values)
    {
        foreach ($values as $key => $value) {
            if (property_exists($this, $key) && !isset($this->$key)) {
                $this->$key = $value;
            }
        }
    }

    public function cid()
    {
        return $this->cid;
    }

    public function gid()
    {
        return $this->gid;
    }

    public function type()
    {
        return $this->type;
    }

    public function name()
    {
        return $this->name;
    }

    public static function checkConsistency($clusters, $groups)
    {
        $unconsistencies = array();
        foreach($clusters as $cluster)
        {
            if (!array_key_exists($cluster->gid(), $groups))
                $unconsistencies[] = $cluster;
        }
        return $unconsistencies;
    }

    public static function getSpecial($whichOne)
    {
        // # Temporary, we could think of other special clusters
        switch($whichOne) {
            case 'external':
                return new Cluster(array('cid' => -2, 'gid' => -1, 'type' => Cluster::SPECIAL, 'name' => 'external'));
                
            case 'internal':
                return new Cluster(array('cid' => -1, 'gid' => -1, 'type' => 'special', 'name' => 'internal'));
        }
        
        return false;
    }

    public static function inline($clusters)
    {
        return XDB::formatArray(array_keys($clusters));
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
