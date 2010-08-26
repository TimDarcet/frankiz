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
    protected $depth;
    protected $name;
    protected $label;
    protected $description;
    protected $children = array();
    protected $father = null;

    static protected $groups;
    static protected $root = null;

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

    public function type()
    {
        return $this->type;
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

    public function label()
    {
        return $this->label;
    }

    public function description()
    {
        return $this->description;
    }

    /**
    * Check if all the children are loaded within a certain depth
    *
    * @param $depth is the depth of the search for children
    */
    protected function childrenLoaded($depth)
    {
        $indexedByL = self::indexedByL($this->children);
        $L = $this->L() + 1;
        while (isset($indexedByL[$L])) {
            $L = $indexedByL[$L]->R() + 1;
            if ($depth > 1 && !$indexedByL[$L]->childrenLoaded($depth - 1))
                return false;
        }
        return $L == $this->R();
    }

    /**
    * Check if all the fathers are loaded within a certain depth
    *
    * @param $depth is the depth of the search for fathers
    */
    protected function fathersLoaded($depth)
    {
        if ($depth == 0) return true;
        if ($this->father === null && $this->gid() != self::root()->gid()) return false;
        return $this->father->fathersLoaded($depth - 1);
    }

    /**
    * Try to find and build the family links with the groups passed as a paramater
    *
    * @param $gs is an array of Groups
    */
    protected function buildLinks(array $gs)
    {
        foreach ($gs as $g) {
            if ($this->isFatherOf($g)) {
                $this->children[$g->gid()] = $g;
                $g->father = $this;
            } else if ($this->isChildOf($g)) {
                $g->children[$this->gid()] = $this;
                $this->father = $g;
            }
        }
    }

    // TODO: all the parameters don't work for the time being
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
                                $this->type(), $this->L, $this->R, $this->depth,
                                $this->name(), $this->label(), $this->description());

        $gid = XDB::insertId();

        XDB::execute('UNLOCK TABLES');

        self::$groups[$this->gid()]  = $this;
        self::$groups[$this->name()] = $this;
    }

    /**
     * Get the childrens of the group and load them if necessary
     *
     * @param $depth is the depth of the tree fetching
     */
    public function children($depth = 1)
    {
        self::batchChildren($this, $depth);
        return $this->children;
    }

    public function fathers($depth = 1)
    {
        self::batchFathers($this, $depth);
        return $this->father;
    }

    public function father()
    {
        return $this->fathers(1);
    }

    /**
    * Refresh the datas of the Group
    */
    // TODO: handle parameters specifying the asked datas 
    public function refresh()
    {
        $res = XDB::query('SELECT  gid, type, L, R, depth, name, label
                             FROM  groups
                            WHERE  gid = {?}', $this->gid());
        $this->fillFromArray($res->fetchOneAssoc());
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

    // TODO: handle parameters specifying the asked datas
    public function toJson()
    {
        return array( "gid"   => $this->gid(),
                      "name"  => $this->name(),
                      "label" => $this->label() );
    }

    protected static function feed($group)
    {
        self::$groups[$group->gid()]  = $group;
        self::$groups[$group->name()] = $group;
    }

    public static function groupsToGids($gs)
    {
        $result = array();
        foreach ($gs as $key => $g)
            if ($g instanceof Group)
                $result[$key] = $g->gid();
            else
                $result[$key] = $g;
        return $result;
    }

    public static function isGid($g)
    {
        return strval(intval($g)) == $g;
    }

    /**
    * Returns the root Group
    */
    public static function root()
    {
        if (self::$root === null) {
            $res = XDB::query('SELECT  gid
                                 FROM  groups
                                WHERE  (R - L + 1) / 2 = (SELECT COUNT(*) FROM groups)');
            self::$root = self::get($res->fetchColumn());
        }
        return self::$root;
    }

    protected static function isLoaded($g)
    {
        return isset(self::$groups[$g]);
    }

    protected function isFatherOf($g)
    {
        return ($this->L() < $g->L()) && ($this->R() > $g->R()) && ($this->depth() + 1 == $g->depth());
    }

    protected function isChildOf($g)
    {
        return ($this->L() > $g->L()) && ($this->R() < $g->R()) && ($this->depth() == $g->depth() + 1);
    }

    protected static function _load($gidsToBeFetched, $namesToBeFetched)
    {
        $loaded = array();
        if (count($gidsToBeFetched) > 0 || count($namesToBeFetched) > 0)
        {
            if (count($gidsToBeFetched) == 0)
                $iter = XDB::iterator('SELECT  gid, type, L, R, depth, name, label
                                         FROM  groups
                                        WHERE  name IN {?}', $namesToBeFetched);
            else if (count($namesToBeFetched) == 0)
                $iter = XDB::iterator('SELECT  gid, type, L, R, depth, name, label
                                         FROM  groups
                                        WHERE  gid IN {?}', $gidsToBeFetched);
            else
                $iter = XDB::iterator('SELECT  gid, type, L, R, depth, name, label
                                         FROM  groups
                                        WHERE  ( gid IN {?} ) OR ( name IN {?} )',
                                            $gidsToBeFetched, $namesToBeFetched);

            while ($array_group = $iter->next()) {
                $group = new Group($array_group);
                self::feed($group);
                $loaded[$group->gid()] = $group;
            }

            // Build the relationships between groups
            $toBeConsummed = $loaded;
            while (($g = array_pop($toBeConsummed)) != null) {
                // First build the links with the previously loaded groups
                $g->buildLinks(self::$groups);
                // Then build the links with the rest of the newly loaded groups
                $g->buildLinks($toBeConsummed);
            }
        }
        return $loaded;
    }

    /**
    * Returns the groups associated with the gids or names given as parameter
    *
    * @param $gs an array of gids, names or groups
    */
    public static function get($gs)
    {
        $gs = self::unflatten($gs);

        // Dissociate already loaded groups of the others
        $results = array();
        $gidsToBeFetched = array();
        $namesToBeFetched = array();
        foreach ($gs as $g)
            if ($g instanceof Group) {
                $results[$g->gid()] = $g;
            } else if (self::isLoaded($g)) {
                $group = self::$groups[$g];
                $results[$group->gid()] = $group;
            } else {
                if (self::isGid($g))
                    $gidsToBeFetched[]  = $g;
                else
                    $namesToBeFetched[] = $g;
            }

        // Return the results and the newly loaded groups
        return self::flatten($results + self::_load($gidsToBeFetched, $namesToBeFetched));
    }

    // Only for debug purposes
    public static function groups()
    {
        return self::$groups;
    }

    protected static function indexedByL($groups)
    {
        $indexed = array();
        foreach ($groups as $group)
            $indexed[$group->L()] = $group;
        return $indexed;
    }

    public static function flatten($g)
    {
        if (is_array($g) && count($g) <= 1)
            return array_pop($g);
        else
            return $g;
    }

    public static function unflatten($g)
    {
        if (!is_array($g))
            return array($g);
        else
            return $g;
    }

    /**
    * Load the childrens of groups with a certain depth
    *
    * @param $gs an array of gids, names or groups
    * @param $depth is the depth of the search for children
    */
    public static function batchChildren($gs, $depth = 1)
    {
        $gs = self::unflatten(self::get($gs));

        // Remove from the array groups with already loaded childrens
        foreach ($gs as $key => $g)
            if ($g->childrenLoaded($depth))
                unset($gs[$key]);

        if (count($gs) > 0) {
            $res = XDB::query('SELECT  g.gid
                                 FROM  groups AS g
                           INNER JOIN  groups AS current ON current.gid IN {?}
                                WHERE       g.L > current.L
                                       AND  g.R < current.R
                                       AND  g.depth <= current.depth + {?}',
                                       self::groupsToGids($gs), $depth);
            self::get($res->fetchColumn());
        }
    }

    /**
    * Load the fathers of groups with a certain depth
    *
    * @param $gs an array of gids, names or groups
    * @param $depth is the depth of the search for fathers
    */
    public static function batchFathers($gs, $depth)
    {
        $gs = self::unflatten(self::get($gs));

        // Remove from the array groups with already loaded fathers
        foreach ($gs as $key => $g)
            if ($g->fathersLoaded($depth))
                unset($gs[$key]);

        if (count($gs) > 0) {
            $res = XDB::query('SELECT  g.gid
                                 FROM  groups AS g
                           INNER JOIN  groups AS current ON current.gid IN {?}
                                WHERE       g.L < current.L
                                       AND  g.R > current.R
                                       AND  g.depth >= current.depth - {?}',
                                       self::groupsToGids($gs), $depth);
            self::get($res->fetchColumn());
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
