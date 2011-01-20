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

class Group extends Meta
{
    const SELECT_BASE        = 0x01;
    const SELECT_CASTES      = 0x02;
    const SELECT_DESCRIPTION = 0x04;
    const SELECT_COMMENTS    = 0x08;

    const NS_USER        = 'user';         // User groups
    const NS_FREE        = 'free';         // Non-Validated group
    const NS_BINET       = 'binet';        // Validated group
    const NS_STUDY       = 'study';
    const NS_PROMO       = 'promo';
    const NS_SPORT       = 'sport';
    const NS_NATIONALITY = 'nationality';

    protected $image = null;
    protected $ns    = null;
    protected $name  = null;
    protected $label = null;
    protected $score = null;

    protected $external = null; // If true, you can access the group's page with AUTH_PUBLIC
    protected $priv     = null; // If false, you become a member when you join the group
    protected $leavable = null; // If true, you can leave the group
    protected $visible  = null; // If true, the groups is invisible

    protected $web  = null;
    protected $mail = null;

    protected $description = null;

    protected $castes = null;

    public function bestId()
    {
        if (empty($this->name))
            return $this->id();

        return $this->name();
    }

    public function name($name = null)
    {
        if ($name != null) {
            $this->name = $name;
            XDB::execute('UPDATE groups SET name = {?} WHERE gid = {?}', $name, $this->id());
        }
        return $this->name;
    }

    public function ns($ns = null)
    {
        if ($ns !== null) {
            $this->ns = $ns;
            XDB::execute('UPDATE groups SET ns = {?} WHERE gid = {?}', $ns, $this->id());
        }
        return $this->ns;
    }

    public function label($label = null)
    {
        if ($label !== null) {
            $this->label = $label;
            XDB::execute('UPDATE groups SET label = {?} WHERE gid = {?}', $label, $this->id());
        }
        return $this->label;
    }

    public function image(FrankizImage $image = null)
    {
        global $globals;

        if ($image != null) {
            $this->image = $image;
            XDB::execute('UPDATE groups SET image = {?} WHERE gid = {?}',
                                              $image->id(), $this->id());
        }

        if (!empty($this->image)) {
            return $this->image;
        }

        return new StaticImage($globals->images->group);
    }

    public function score()
    {
        return $this->score;
    }

    public function external($external = null)
    {
        if ($external !== null) {
            $this->external = $external;
            XDB::execute('UPDATE groups SET external = {?} WHERE gid = {?}', $this->external, $this->id());
        }
        return $this->external;
    }

    public function priv($priv = null)
    {
        if ($priv !== null) {
            $this->priv = $priv;
            XDB::execute('UPDATE groups SET priv = {?} WHERE gid = {?}', $this->priv, $this->id());
        }
        return $this->priv;
    }

    public function leavable($leavable = null)
    {
        if ($leavable !== null) {
            $this->leavable = $leavable;
            XDB::execute('UPDATE groups SET leavable = {?} WHERE gid = {?}', $leavable, $this->id());
        }
        return $this->leavable;
    }

    public function visible($visible = null)
    {
        if ($visible !== null) {
            $this->visible = $visible;
            XDB::execute('UPDATE groups SET visible = {?} WHERE gid = {?}', $visible, $this->id());
        }
        return $this->visible;
    }

    public function description($description = null)
    {
        if ($description !== null) {
            $this->description = $description;
            XDB::execute('UPDATE groups SET description = {?} WHERE gid = {?}', $description, $this->id());
        }
        return $this->description;
    }

    public function web($web = null)
    {
        if ($web != null) {
            $this->web = $web;
            XDB::execute('UPDATE groups SET web = {?} WHERE gid = {?}', $web, $this->id());
        }
        return $this->web;
    }

    public function mail($mail = null)
    {
        if ($mail != null) {
            $this->mail = $mail;
            XDB::execute('UPDATE groups SET mail = {?} WHERE gid = {?}', $mail, $this->id());
        }
        return $this->mail;
    }

    public function caste(Rights $rights = null)
    {
        if ($rights === null)
            return $this->castes;

        /* /!\ Don't forget to select Castes before you try to access them
         * Because if this method doesn't find the wanted caste, it will create it
         * and you will get an error from the DB
        */

        if ($this->castes === null)
            $this->castes = new Collection('Caste');

        // Find the caste corresponding to the specified $rights
        $caste = $this->castes->filter('rights', $rights)->first();

        if ($caste === false) {
            $caste = new Caste(array('group' => $this, 'rights' => $rights));
            $caste->insert();
            $this->castes->add($caste);
        }

        return $caste;
    }

    public function hasUser(User $user = null)
    {
        if ($user === null)
            $user = S::user();

        foreach ($this->castes as $caste) {
            if ($caste->hasUser($user) != false)
                return true;
        }

        return false;
    }

    public function removeUser(User $user = null)
    {
        if ($user === null)
            $user = S::user();

        foreach ($this->castes as $caste)
            $caste->removeUser($user);
    }

    public function export($stringify = false)
    {
        $json = array("id"    => $this->id(),
                      "name"  => $this->name(),
                      "label" => $this->label(),
                      "src"   => $this->image()->src(ImageInterface::SELECT_MICRO));

        return ($stringify) ? json_encode($json) : $json;
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/

    public function insert($id = null)
    {
        if ($id == null) {
            XDB::execute('INSERT INTO groups SET priv = 1');
            $this->id = XDB::insertId();
        } else {
            XDB::execute('INSERT INTO groups SET gid = {?}, priv = 1', $id);
            $this->id = $id;
        }

        // Create the main castes
        $this->caste(Rights::admin());
        $this->caste(Rights::member());
    }

    public static function batchFrom(array $mixed)
    {
        $collec = new Collection();
        if (!empty($mixed)) {
            $iter = XDB::iterator('SELECT  gid AS id, name
                                     FROM  groups
                                    WHERE  name IN {?}', $mixed);
            while ($g = $iter->next())
                $collec->add(new self($g));
        }

        return $collec;
    }

    public static function batchSelect(array $groups, $options = null)
    {
        if (empty($groups))
            return;

        if (empty($options)) {
            $options = array(self::SELECT_BASE => null);
            $options[self::SELECT_CASTES] = Caste::SELECT_BASE;
        }

        $bits = self::optionsToBits($options);
        $groups = array_combine(self::toIds($groups), $groups);

        $joins = array();
        $cols = array();
        if ($bits & self::SELECT_BASE)
            $cols['g']   = array('ns', 'name', 'label', 'score', 'image',
                                 'priv', 'leavable', 'visible', 'external');
        if ($bits & self::SELECT_DESCRIPTION)
            $cols['g'] = array_merge($cols['g'], array('description', 'web', 'mail'));

        if (!empty($cols)) {
            $iter = XDB::iterator('SELECT  g.gid AS id, ' . self::arrayToSqlCols($cols) . '
                                     FROM  groups AS g
                                     ' . PlSqlJoin::formatJoins($joins, array()) . '
                                    WHERE  g.gid IN {?}
                                 GROUP BY  g.gid', self::toIds($groups));

            while ($datas = $iter->next()) {
                if ($bits & self::SELECT_BASE) {
                    $datas['name']  = ($datas['name'] === null) ? false : $datas['name'];
                    $datas['image'] = empty($datas['image']) ? false : new FrankizImage($datas['image']);
                }

                $groups[$datas['id']]->fillFromArray($datas);
            }
        }

        if ($bits & self::SELECT_CASTES)
        {
            foreach($groups as $group)
                $group->castes = new Collection('Caste');

            $iter = XDB::iterRow("SELECT  cid, gid, rights
                                    FROM  castes
                                   WHERE  gid IN {?}", self::toIds($groups));

            $castes = new Collection('Caste');
            while (list($cid, $gid, $rights) = $iter->next()) {
                $caste = new Caste(array('id' => $cid, 'group' => $groups[$gid], 'rights' => new Rights($rights)));

                $castes->add($caste);
                $groups[$gid]->castes->add($caste);
            }

            if (!empty($options[self::SELECT_CASTES]))
                $castes->select($options[self::SELECT_CASTES]);
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
