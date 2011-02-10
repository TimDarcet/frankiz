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

class GroupSchema extends Schema
{
    public function className() {
        return 'Group';
    }

    public function table() {
        return 'groups';
    }

    public function id() {
        return 'gid';
    }

    public function tableAs() {
        return 'g';
    }

    public function scalars() {
        return array('description', 'external', 'label', 'leavable',
                     'mail', 'name', 'ns', 'score', 'visible', 'web');
    }

    public function objects() {
        return array('image' => 'FrankizImage');
    }

    public function collections() {
        return array('castes' => 'Caste');
    }
}

class GroupSelect extends Select
{
    public function className() {
        return 'Group';
    }

    public static function base($subs = null) {
        return new GroupSelect(array('ns', 'score', 'name', 'label', 'image'), $subs);
    }

    public static function castes($subs = null) {
        return new GroupSelect(array('castes'), $subs);
    }

    public static function subscribe() {
        return new GroupSelect(array('ns', 'name', 'visible', 'castes', 'leavable'),
                               array('castes' => CasteSelect::bubble()));
    }

    public static function validate() {
        return new GroupSelect(array('ns', 'name', 'label', 'mail'));
    }

    public static function see() {
        return new GroupSelect(array('ns', 'score', 'name', 'label', 'description',
                                     'image', 'web', 'mail', 'visible', 'castes', 'leavable'),
                               array('castes' => CasteSelect::base()));
    }

    protected function handlers() {
        return array('main' => array_merge(Schema::group()->scalars(), array('image')),
                   'castes' => array('castes'));
    }

    protected function handler_castes(Collection $groups, $fields) {
        $_groups = array();
        foreach ($groups as $g) {
            $_groups[$g->id()] = new Collection('Caste');
        }

        $iter = XDB::iterRow('SELECT  cid, `group`, rights
                                FROM  castes
                               WHERE  `group` IN {?}', $groups->ids());

        $castes = new Collection('Caste');
        while (list($cid, $group, $rights) = $iter->next()) {
            $caste = new Caste(array('id' => $cid,
                                  'group' => $groups->get($group),
                                 'rights' => new Rights($rights)));

            $castes->add($caste);
            $_groups[$group]->add($caste);
        }

        foreach ($groups as $g) {
            $g->fillFromArray(array('castes' => $_groups[$g->id()]));
        }

        if (!empty($castes) && !empty($this->subs['castes'])) {
            $castes->select($this->subs['castes']);
        }
    }
}

class Group extends Meta
{
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

    public function removeUser(User $user)
    {
        foreach ($this->castes as $caste) {
            if (!$caste->userfilter()) {
                $caste->removeUser($user);
            }
        }
    }

    public function export($bits = null)
    {
        global $globals;

        $img = $this->image();
        if (!$img) {
            $img = new StaticImage($globals->images->group);
        }

        $json = array("id"    => $this->id(),
                      "name"  => $this->name(),
                      "label" => $this->label(),
                      "src"   => $img->src('micro'));
        return $json;
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/

    public function insert($id = null)
    {
        if ($id == null) {
            XDB::execute('INSERT INTO groups SET gid = NULL');
            $this->id = XDB::insertId();
        } else {
            XDB::execute('INSERT INTO groups SET gid = {?}', $id);
            $this->id = $id;
        }

        /*
         * Create the main castes
         */
        $admins  = $this->caste(Rights::admin());
        $members = $this->caste(Rights::member());
        $logics  = $this->caste(Rights::logic());
        $friends = $this->caste(Rights::friend());

        /*
         * Create the 'restricted' caste
         */
        $restricted = new UserFilter(new UFC_Caste(array($admins, $members, $logics)));
        $this->caste(Rights::restricted())->userfilter($restricted);

        /*
         * Create the 'everybody' caste
         * It's better not to refer to the restricted caste, as we don't know in what
         * order the bubbling is going to happen
         */
        $everybody = new UserFilter(new UFC_Caste(array($admins, $members, $logics, $friends)));
        $this->caste(Rights::everybody())->userfilter($everybody);
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
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
