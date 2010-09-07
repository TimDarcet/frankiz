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
    const maxDepth = 666;

    protected $gid = null;
    protected $type = null;
    protected $L;
    protected $R;
    protected $depth;
    protected $name = null;
    protected $label = null;
    protected $description = null;
    protected $children = array();
    protected $partialChildren =  array();
    protected $father = null;

    static protected $groups = array();
    static protected $nameToGroup = array();
    static protected $root = null;
    static protected $partialTreesRoots = array();

    public function __construct($raw)
    {
        $this->fillFromArray($raw);
        if ($this->label == null) $this->label = 'No Name';
        if ($this->type == null) $this->type = 'open';
        if ($this->description == null) $this->description = '';
    }

    protected function fillFromArray(array $values)
    {
        foreach ($values as $key => $value) {
            if (property_exists($this, $key)) {
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

    public function label($label = null)
    {
        if ($label != null)
        {
            $this->label = $label;
            XDB::execute('UPDATE groups SET label = {?} WHERE gid = {?}', $this->label, $this->gid);
        }
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
            if ($depth > 1 && !$indexedByL[$L]->childrenLoaded($depth - 1))
                return false;
            $L = $indexedByL[$L]->R() + 1;
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
        if ($this->gid() == self::root()->gid()) return true;
        if ($this->father === null) return false;
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

    public function addTo($parent)
    {
        if ($this->gid != null)
            throw new Exception('This group already exists');

        $parent = self::get($parent);

        XDB::execute('LOCK TABLES groups WRITE');

        $parent->refresh();

        $this->L = $parent->R;
        $this->R = $this->L + 1;
        $this->depth = $parent->depth + 1;

        XDB::execute('UPDATE  groups
                         SET  R = R + 2
                       WHERE  R >= {?}', $parent->R);

        XDB::execute('UPDATE  groups
                         SET  L = L + 2
                       WHERE  L >= {?}', $parent->R);

        XDB::execute('INSERT INTO  groups
                              SET  type = {?}, L = {?}, R = {?}, depth = {?},
                                   name = {?}, label = {?}, description = {?}',
                                $this->type, $this->L, $this->R, $this->depth,
                                $this->name, $this->label, $this->description);
        $this->gid = XDB::insertId();

        XDB::execute('UNLOCK TABLES');

        $this->feed();
        $this->buildLinks(array($parent));
    }

    protected function shift($delta, $depth)
    {
        $depth++;
        $this->L     += $delta;
        $this->R     += $delta;
        $this->depth  = $depth;
        foreach ($children as $child)
            $child->shift($delta, $depth);
    }

    public function moveTo($parent)
    {
        $parent = self::get($parent);

        // Check if the move is possible
        if ($this->gid() == Group::root()->gid() ||
            ($parent->L >= $this->L && $parent->R <= $this->R) ||
            $this->isChildOf($parent) )
            throw new Exception('This move is unpossible');

        XDB::execute('LOCK TABLES groups WRITE');

        self::batchRefresh(array($this, $parent));
        $delta = $this->R - $this->L;

        $min = min($this->R, $parent->L);
        $max = max($this->R, $parent->L);

        $signe = ($min == $this->R) ? -1 : 1;
        $signeSQL = ($signe == -1) ? '-' : '+';
        if ($signe == 1) {
            $shift = $parent->L - $this->L + 1;
        } else {
            $shift = $parent->L - $this->R;
        }

        // 1. We move the subtree away on the left
        $init_shift = $this->R + 1000;
        XDB::execute('UPDATE  groups
                         SET  R = R - {?}, L = L - {?}
                       WHERE  L >= {?} AND R <= {?}', 
                    $init_shift, $init_shift, $this->L, $this->R);

        // 2. We shit the L and R to the left
        XDB::execute('UPDATE  groups
                         SET  L = L '.$signeSQL.' {?}
                       WHERE  L > {?} AND L <= {?}', $delta + 1, $min, $max);
        XDB::execute('UPDATE  groups
                         SET  R = R '.$signeSQL.' {?}
                       WHERE  R > {?} AND R < {?}', $delta + 1, $min, $max);

        // 3. We move the subtree to its spot
        $end_shift = $init_shift + $shift;
        XDB::execute('UPDATE  groups
                         SET  R = R + {?}, L = L + {?}, depth = depth + {?}
                       WHERE  L >= {?} AND R <= {?}', 
                        $end_shift, $end_shift, $parent->depth - $this->depth + 1,
                        $this->L - $init_shift, $this->R - $init_shift);

        XDB::execute('UNLOCK TABLES');

        // Update the local datas
        foreach (self::$groups as $g) {
            if ($g->L < $this->L || $g->R > $this->R) {
                if ($max >= $g->L && $g->L > $min)
                    $g->L += $signe * ($delta + 1);
                if ($max > $g->R && $g->R > $min)
                    $g->R += $signe * ($delta + 1);
            }
        }

        $this->shift($shift, $parent->depth);

        // Destroy the links
        if ($this->fathersLoaded(1)) {
            unset($this->father->children[$this->gid]);
            $this->father = null;
        }

        // Build the new ones
        $this->buildLinks(array($parent));
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

    /**
     * Return the children of the Group in a partial tree
     * Children *must* be loaded before : INABIAF
     *
     * @param $ptid id of the partial tree
     */
    public function partialChildren($ptid = null)
    {
        if ($ptid === null)
            return $this->children;
        else
            return $this->partialChildren[$ptid];
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
    public function refresh()
    {
        self::batchRefresh($this);
    }

    public function remove()
    {
        XDB::execute('LOCK TABLES groups WRITE');

        $this->refresh();
        $delta = $this->R - $this->L + 1;

        XDB::execute('DELETE FROM  groups
                            WHERE  L >= {?} AND R <= {?}', $this->L, $this->R);

        XDB::execute('UPDATE  groups
                         SET  L = L - {?}
                       WHERE  L >= {?}', $delta, $this->L);

        XDB::execute('UPDATE  groups
                         SET  R = R - {?}
                       WHERE  R >= {?}', $delta, $this->L);

        XDB::execute('UNLOCK TABLES');

        // Destroy the links
        if ($this->fathersLoaded(1)) {
            unset($this->father->children[$this->gid]);
            $this->father = null;
        }

        $this->unfeed();
    }

    public function toJson($depth = 0, $visiblity = 0, $ptid = null)
    {
        $json = array("data"  => array(
                                        "title" => $this->label()
                                      ),
                      "attr"  => array(
                                        "gid"   => $this->gid(),
                                        "name"  => $this->name(),
                                        "title" => $this->name(),
                                        "label" => $this->label()
                                      )
                       );

        if ($this->hasChildren())
        {
            $json['state'] = ($visiblity > 0) ? "open" : "closed";
            if ($depth > 0) {
                $json['children'] = array();
                $children = $this->partialChildren($ptid);
                foreach($children as $child)
                    $json['children'][] = $child->toJson($depth - 1, $visiblity - 1, $ptid);
            }
        }

        return $json;
    }

    protected function feed()
    {
        self::$groups[$this->gid()]       = $this;
        self::$nameToGroup[$this->name()] = $this;
    }

    protected function unfeed()
    {
        unset(self::$groups[$this->gid()]);
        unset(self::$nameToGroup[$this->name()]);
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
        global $globals;

        if (self::$root === null) {
            // If the name or gid of the root groop is specified in the conf file
            // We don't have to query for it !
            if (isset($globals->root) && ($globals->root != '')) {
                self::$root = self::get($globals->root);
            } else {
                $res = XDB::query('SELECT  gid
                                     FROM  groups
                                    WHERE  (R - L + 1) / 2 = (SELECT COUNT(*) FROM groups)');
                self::$root = self::get($res->fetchColumn());
            }
        }
        return self::$root;
    }

    protected static function ifLoaded($g)
    {
        if ($g instanceof Group && isset(self::$groups[$g->gid()]))
            return $g;
        if (isset(self::$groups[$g]))
            return self::$groups[$g];
        if (isset(self::$nameToGroup[$g]))
            return self::$nameToGroup[$g];
        return false;
    }

    protected function isFatherOf($g)
    {
        return ($this->L() < $g->L()) && ($this->R() > $g->R()) && ($this->depth() + 1 == $g->depth());
    }

    protected function isChildOf($g)
    {
        return ($this->L() > $g->L()) && ($this->R() < $g->R()) && ($this->depth() == $g->depth() + 1);
    }

    protected function hasChildren()
    {
        return ($this->L() + 1 != $this->R());
    }

    protected static function _load($gidsToBeFetched, $namesToBeFetched, $create = true)
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
                if ($create) {
                    $group = new Group($array_group);
                } else {
                    $group = self::$groups[$array_group['gid']];
                    $group->unfeed();
                    $group->fillFromArray($array_group);
                }
                $group->feed();
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

        if (count($gs) == 0) return array();

        // Dissociate already loaded groups of the others
        $results = array();
        $gidsToBeFetched = array();
        $namesToBeFetched = array();
        foreach ($gs as $g) {
            $group = self::ifLoaded($g);
            if ($group != false) {
                $results[$group->gid()] = $group;
            } else {
                if (self::isGid($g))
                    $gidsToBeFetched[]  = $g;
                else
                    $namesToBeFetched[] = $g;
            }
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

    public static function batchRefresh($gs)
    {
        self::_load(self::groupsToGids(self::unflatten($gs)), array(), false);
    }

    /**
    * Returns the gids of the children within a certain depth
    * Careful : results are *not* cached
    *
    * @param $gs an array of gids *only*
    * @param $depth is the depth of the search for children
    */
    public static function batchChildrenGids($gs, $depth = 1)
    {
        $gs = self::unflatten($gs);
        if (count($gs) > 0) {
            $res = XDB::query('SELECT  g.gid
                                 FROM  groups AS g
                           INNER JOIN  groups AS current ON current.gid IN {?}
                                WHERE       g.L > current.L
                                       AND  g.R < current.R
                                       AND  g.depth <= current.depth + {?}',
                                       $gs, $depth);
            return $res->fetchColumn();
        }
        return array();
    }

    /**
    * Load the childrens of groups within a certain depth
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

        self::get(self::batchChildrenGids(self::groupsToGids($gs), $depth));
    }

    /**
    * Returns the gids of the fathers within a certain depth
    * Careful : results are *not* cached
    *
    * @param $gs an array of gids *only*
    * @param $depth is the depth of the search for fathers
    */
    public static function batchFathersGids($gs, $depth = 1)
    {
        $gs = self::unflatten($gs);
        if (count($gs) > 0) {
            $res = XDB::query('SELECT  g.gid
                                 FROM  groups AS g
                           INNER JOIN  groups AS current ON current.gid IN {?}
                                WHERE       g.L < current.L
                                       AND  g.R > current.R
                                       AND  g.depth >= current.depth - {?}',
                                       $gs, $depth);
            return $res->fetchColumn();
        }
        return array();
    }

    /**
    * Load the fathers of groups within a certain depth
    * Returns an array containing all the fathers encoutered
    *
    * @param $gs an array of gids, names or groups
    * @param $depth is the depth of the search for fathers
    */
    public static function batchFathers($gs, $depth = 1)
    {
        $gs = self::unflatten(self::get($gs));

        // Remove from the array groups with already loaded fathers
        foreach ($gs as $key => $g)
            if ($g->fathersLoaded($depth))
                unset($gs[$key]);

        self::get(self::batchFathersGids(self::groupsToGids($gs), $depth));
    }

    protected function _ascendingPartialTree($ptid)
    {
        if ($this->gid() == self::root()->gid()) {
            self::$partialTreesRoots[$ptid][$this->gid()] = $this;
        } else {
            $this->father()->partialChildren[$ptid][$this->gid()] = $this;
            $this->father()->_ascendingPartialTree($ptid);
        }
    }

    public static function partialTreeRoots($ptid)
    {
        return self::$partialTreesRoots[$ptid];
    }

    public static function ascendingPartialTree($gs)
    {
        $gs = self::unflatten(self::get($gs));
        self::batchFathers($gs, self::maxDepth);

        $ptid = uniqid();
        self::$partialTreesRoots[$ptid] = array();

        foreach ($gs as $g)
            $g->_ascendingPartialTree($ptid);

        return $ptid;
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
