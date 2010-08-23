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
    const GID  = 0x01;
    const NAME = 0X02;

    protected $gid;
    protected $type;
    protected $L;
    protected $R;
    protected $depth;
    protected $name;
    protected $label;
    protected $description;
    protected $children = null;

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

    public function depth()
    {
        return $this->depth;
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

    private function _children($groups)
    {
        $this->children = self::depthFilter($groups);

        foreach($this->children as $key => $child)
                $child->_children(self::boundariesFilter($groups, $child->L(), $child->R()));
    }

    /**
     * Get the childrens of the group
     *
     * @param $depth is the depth of the tree fetching
     */
    public function children($depth = 1)
    {
        if ($this->children == null)
        {
            $this->children = array();
            $res = XDB::query('SELECT  g.gid
                                 FROM  groups AS g
                           INNER JOIN  groups AS current ON current.gid = {?}
                                WHERE       g.L > current.L
                                       AND  g.R < current.R
                                       AND  g.depth <= current.depth + {?}',
                                  $this->gid(), $depth);
            $groups = self::get($res->fetchColumn(), self::GID);

            $this->_children($groups);
        }
        return $this->children;
    }

    public function addTo($parent)
    {
        $parent = self::get($parent);

        XDB::execute('LOCK TABLES groups WRITE');

        $parent->refresh();

        $this->L = $parent->R();
        $this->R = $this->L + 1;
        $this->depth = $parent->depth() + 1;

        XDB::execute('UPDATE  groups
                         SET  R = R + 2
                       WHERE  R >= {?}', $parent->R());

        XDB::execute('UPDATE  groups
                         SET  L = L + 2
                       WHERE  L >= {?}', $parent->R());

        XDB::execute('INSERT INTO  groups
                              SET  type = {?}, L = {?}, R = {?}, depth = {?}
                                   name = {?}, label = {?}, description = {?}',
                                $this->type('open'), $this->L, $this->R, $this->depth,
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
        $res = XDB::query('SELECT  gid, type, L, R, depth, name, label
                             FROM  groups
                            WHERE  gid = {?}', $this->gid());
        $this->fillFromArray($res->fetchOneAssoc());
    }

    public static function boundariesFilter($groups, $L, $R, $extract = true)
    {
        $filtered = array();
        foreach ($groups as $key => $group)
        {
            if (($group->L() > $L) && ($group->R() < $R)) {
                $filtered[$key] = $group;
                if ($extract)
                    unset($groups[$key]);
            }
        }
        return $filtered;
    }

    public static function depthFilter($groups, $depth = 'min', $extract = true)
    {
        if ($depth == 'min') {
            $minDepth = -1;
            foreach($groups as $key => $group)
                    if ($group->depth() < $minDepth || $minDepth == -1)
                        $minDepth = $group->depth();
            $depth = $minDepth;
        }

        $filtered = array();
        foreach ($groups as $key => $group)
        {
            if ($group->depth() == $depth) {
                $filtered[$key] = $group;
                if ($extract)
                    unset($groups[$key]);
            }
        }
        return $filtered;
    }

     /**
     * Try to find the gid associated with the paramater
     *
     * @param $g a Group, a gid or a gname
     */
    public static function toGid($g)
    {
        if ($g instanceof Group) return $g->gid();
        if (self::isGid($g)) return $g;
        return nameToGid($name);
    }

    public static function nameToGid($name)
    {
        foreach (self::$groups as $group)
            if ($group->name() == $g)
                return $group->gid();
        return false;
    }

    public static function isGid($g)
    {
        return strval(intval($g)) == $g;
    }

    public static function topLevel()
    {
        if (self::$topGroup == null) {
            $res = XDB::query('SELECT  gid, type, L, R, depth, name, label
                                 FROM  groups
                                WHERE  (R - L + 1) / 2 = (SELECT COUNT(*) FROM groups)');
            self::$topGroup = new Group($res->fetchOneAssoc());
            self::$groups[self::$topGroup->gid()]  = self::$topGroup;
            self::$groups[self::$topGroup->name()] = self::$topGroup;
        }
        return self::$topGroup;
    }

    protected static function feed($group)
    {
        self::$groups[$group->gid()]  = $group;
        self::$groups[$group->name()] = $group;
    }

    protected static function has($g)
    {
        return isset(self::$groups[$g]);
    }

    public static function get($g, $flag = self::GID)
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
                    if ($flag & self::GID)
                        $results[$group->gid()]  = $group;
                    if ($flag & self::NAME)
                        $results[$group->name()] = $group;
                } else {
                    if (self::isGid($gid_gname))
                        $gidToBeFetched[]  = $gid_gname;
                    else
                        $nameToBeFetched[] = $gid_gname;
                }
            }

            if (count($gidToBeFetched) == 0)
                $iter = XDB::iterator('SELECT  gid, type, L, R, depth, name, label
                                         FROM  groups
                                        WHERE  name IN {?}', $nameToBeFetched);
            else if (count($nameToBeFetched) == 0)
                $iter = XDB::iterator('SELECT  gid, type, L, R, depth, name, label
                                         FROM  groups
                                        WHERE  gid IN {?}', $gidToBeFetched);
            else
                $iter = XDB::iterator('SELECT  gid, type, L, R, depth, name, label
                                         FROM  groups
                                        WHERE  ( gid IN {?} ) OR ( name IN {?} )',
                                            $gidToBeFetched, $nameToBeFetched);

            while ($array_group = $iter->next()) {
                $group = new Group($array_group);
                self::feed($group);
                if ($flag & self::GID)
                    $results[$group->gid()]  = $group;
                if ($flag & self::NAME)
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
                $res = XDB::query('SELECT  gid, type, L, R, depth, name, label
                                     FROM  groups
                                    WHERE  gid = {?}', $g);
            } else {
                $res = XDB::query('SELECT  gid, type, L, R, depth, name, label
                                     FROM  groups
                                    WHERE  name = {?}', $g);
            }
            self::feed(new Group($res->fetchOneRow()));

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
