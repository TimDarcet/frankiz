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

class Group
{
    protected $gid;
    protected $type;
    protected $L;
    protected $R;
    protected $name;
    protected $label;
    protected $description;

    static protected $groups;
    static protected $topGroup = null;

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

    public function gid()
    {
        return $this->gid;
    }

    public function type($default)
    {
        return (is_null($this->type)) ? $default : $this->type;
    }

    public function L()
    {
        return $this->L;
    }

    public function R()
    {
        return $this->R;
    }

    public function name()
    {
        return $this->name;
    }

    public function label($default)
    {
        return (is_null($this->label)) ? $default : $this->label;
    }

    public function description($default)
    {
        return (is_null($this->description)) ? $default : $this->description;
    }

    public function addTo($parent)
    {
        $parent = self::get($parent);

        XDB::execute('LOCK TABLES groups WRITE');

        $parent->refresh();

        $this->L = $parent->R();
        $this->R = $this->L + 1;

        XDB::execute('UPDATE  groups
                         SET  R = R + 2
                       WHERE  R >= {?}', $parent->R());

        XDB::execute('UPDATE  groups
                         SET  L = L + 2
                       WHERE  L >= {?}', $parent->R());

        XDB::execute('INSERT INTO  groups
                              SET  type = {?}, L = {?}, R = {?},
                                   name = {?}, label = {?}, description = {?}',
                                $this->type('open'), $this->L, $this->R,
                                $this->name(), $this->label(''), $this->description(''));

        $gid = XDB::insertId();

        XDB::execute('UNLOCK TABLES');

        self::$groups[$this->gid()]  = $this;
        self::$groups[$this->name()] = $this;
    }

    public function remove()
    {
        XDB::execute('LOCK TABLES groups WRITE');

        $this->refresh();

        XDB::execute('DELETE FROM  groups
                            WHERE  gid = {?}', $this->gid);

        XDB::execute('UPDATE  groups
                         SET  L = L - 2
                       WHERE  L >= {?}', $this->L);

        XDB::execute('UPDATE  groups
                         SET  R = R - 2
                       WHERE  R >= {?}', $this->L);

        XDB::execute('UNLOCK TABLES');

        self::$groups[$this->gid()]  = null;
        self::$groups[$this->name()] = null;
    }

    public function refresh()
    {
        $res = XDB::query('SELECT  gid, type, L, R, name, label
                             FROM  groups
                            WHERE  gid = {?}', $this->gid());
        $this->fillFromArray($res->fetchOneAssoc());
    }

     /**
     * Try to find the gid associated with the paramater
     *
     * @param $g a Group, a gid or a gname
     */
    static function toGid($g)
    {
        if ($g instanceof Group) return $g->gid();
        if (self::isGid($g)) return $g;
        return nameToGid($name);
    }

    static function nameToGid($name)
    {
        foreach (self::$groups as $group)
            if ($group->name() == $g)
                return $group->gid();
        return false;
    }

    static function isGid($g)
    {
        return strval(intval($g)) == $g;
    }

    static function getTop()
    {
        if (self::$topGroup == null) {
            $res = XDB::query('SELECT  gid, type, L, R, name, label
                                 FROM  groups
                                WHERE  (R - L + 1) / 2 = (SELECT COUNT(*) FROM groups)');
            self::$topGroup = new Group($res->fetchOneAssoc());
            self::$groups[self::$topGroup->gid()]  = self::$topGroup;
            self::$groups[self::$topGroup->name()] = self::$topGroup;
        }
        return self::$topGroup;
    }

    static function get($g)
    {
        if (is_array($g) && count($g) == 0)
            return false;

        if (is_array($g) && count($g) == 1)
            $g = array_pop($g);

        if (is_array($g))
        {
            $results = array();
            $gidToBeFetched = array();
            $nameToBeFetched = array();
            foreach ($g as $gid_gname) {
                if (isset(self::$groups[$gid_gname])) {
                    $group = self::$groups[$gid_gname];
                    $results[$group->gid()]  = $group;
                    $results[$group->name()] = $group;
                } else {
                    if (self::isGid($gid_gname))
                        $gidToBeFetched[]  = $gid_gname;
                    else
                        $nameToBeFetched[] = $gid_gname;
                }
            }

            if (count($gidToBeFetched) == 0)
                $iter = XDB::iterator('SELECT  gid, type, name, label
                                         FROM  groups
                                        WHERE  name IN {?}', $nameToBeFetched);
            else if (count($nameToBeFetched) == 0)
                $iter = XDB::iterator('SELECT  gid, type, name, label
                                         FROM  groups
                                        WHERE  gid IN {?}', $gidToBeFetched);
            else
                $iter = XDB::iterator('SELECT  gid, type, name, label
                                         FROM  groups
                                        WHERE  ( gid IN {?} ) OR ( name IN {?} )',
                                            $gidToBeFetched, $nameToBeFetched);

            while ($array_group = $iter->next()) {
                $group = new Group($array_group);
                self::$groups[$group->gid()]  = $group;
                self::$groups[$group->name()] = $group;
                $results[$group->gid()]  = $group;
                $results[$group->name()] = $group;
            }
            return $results;
        }
        else
        {
            if ($g instanceof Group)
                return $g;

            if (isset(self::$groups[$g]))
                return self::$groups[$g];

            if (self::isGid($g)) {
                $res = XDB::query('SELECT  gid, type, name, label
                                     FROM  groups
                                    WHERE  gid = {?}', $g);
            } else {
                $res = XDB::query('SELECT  gid, type, name, label
                                     FROM  groups
                                    WHERE  name = {?}', $g);
            }
            $group = new Group($res->fetchOneRow());
            self::$groups[$group->gid()]  = $group;
            self::$groups[$group->name()] = $group;

            return $group;
        }
    }

    // Only for debug
    public static function groups()
    {
        return self::$groups;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
