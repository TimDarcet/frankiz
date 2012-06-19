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
                     'mail', 'name', 'ns', 'score', 'visible', 'web', 'wikix');
    }

    public function objects() {
        return array('image' => 'FrankizImage', 'premises' => 'Array', 'ips' => 'Array');
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
        return new GroupSelect(array('ns', 'score', 'name', 'label', 'image', 'external'), $subs);
    }

    public static function castes($subs = null) {
        return new GroupSelect(array('castes'), $subs);
    }

    public static function subscribe() {
        return new GroupSelect(array('ns', 'name', 'label', 'visible', 'castes', 'leavable'),
                               array('castes' => CasteSelect::bubble()));
    }

    public static function validate() {
        return new GroupSelect(array('ns', 'name', 'label', 'mail', 'castes'), array('castes' => CasteSelect::base()));
    }

    public static function see() {
        return new GroupSelect(array('ns', 'score', 'name', 'label', 'description',
                                     'image', 'wikix', 'web', 'mail', 'visible', 'castes', 'leavable','external',
                                     'premises','ips'),
                               array('castes' => CasteSelect::base()));
    }

    public static function premises($subs = null) {
        return new GroupSelect(array('premises','ips'), $subs);
    }

    protected function handlers() {
        return array('main'   => array_merge(Schema::group()->scalars(), array('image')),
                   'premises' => array('premises'),
                   'ips'      => array('ips'),
                   'castes'   => array('castes'));
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

    protected function handler_premises(Collection $groups, $fields) {
        foreach ($groups as $g) {
            $rf = new RoomFilter(new RFC_Group($g->id()));
            $rooms = $rf->get();
            $rooms->select(RoomSelect::premise());
            $premises = array();
            foreach($rooms as $premise) {
                $opens = $premise->open();
                $premises[$premise->id()] = array('label' => $premise->comment(),
                                        'phone' => $premise->phone(),
                                        'open'  => $opens[$g->id()]);
            }
            $g->fillFromArray(array('premises' => $premises));
        }
    }

    protected function handler_ips(Collection $groups, $fields) {
        foreach ($groups as $g) {
            $rf = new RoomFilter(new RFC_Group($g->id()));
            $rooms = $rf->get();
            $rooms->select(RoomSelect::premise());
            $ips = array();
            foreach($rooms as $premise) {
                $ips = array_merge($ips, $premise->ips());
            }
            $g->fillFromArray(array('ips' => $ips));
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
    const NS_INSTRUMENT  = 'instrument';
    const NS_COURSE      = 'course';
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
    protected $wikix  = null;
    protected $mail = null;

    protected $description = null;

    protected $castes = null;

    protected $premises = null;
    protected $ips = null;

    public function bestId()
    {
        if (empty($this->name))
            return $this->id();

        return $this->name();
    }

    public function addCaste(Rights $rights)
    {
        if ($this->castes === null)
            $this->castes = new Collection('Caste');

        $caste = new Caste(array('group' => $this, 'rights' => $rights));
        $caste->insert();
        $this->castes->add($caste);

        return $caste;
    }

    public function caste(Rights $rights = null)
    {
        if ($rights === null)
            return $this->castes;

        /*
         * /!\ Don't forget to select Castes before you try to access them
        */

        if ($this->castes === null)
            throw new DataNotFetchedException("The castes of the Group " + $this->id() + " have not been fetched");

        // Find the caste corresponding to the specified $rights
        return $this->castes->filter('rights', $rights)->first();
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

    *******************************************************************************/

    public function selectRights(Collection $users)
    {
        $rights = array();
        if ($users->count() > 0) {
            $iter = XDB::iterRow('SELECT  cu.uid, c.rights
                                     FROM  castes AS c
                               INNER JOIN  castes_users AS cu ON cu.cid = c.cid
                               INNER JOIN  groups AS g ON g.gid = c.`group`
                                    WHERE  g.gid = {?} AND cu.uid IN {?}',
                                           $this->id(), $users->ids());
            while (list($uid, $right) = $iter->next()) {
                if (empty($rights[$uid])) {
                    $rights[$uid] = array();
                }
                $rights[$uid][] = new Rights($right);
            }
        }

        return $rights;
    }

    public function insert($id = null, $type = 'all')
    {
        if ($id == null) {
            $this->name = uniqid();
            XDB::execute('INSERT INTO groups SET name = {?}', $this->name);
            $this->id = XDB::insertId();
        } else {
            $this->name = 'g_' . $id;
            XDB::execute('INSERT INTO groups SET gid = {?}, name= {?}', $id, $this->name);
            $this->id = $id;
        }

        /*
         * Create the castes
         */
        if ($type == 'user') {
            // A user group only needs an admin caste & a restricted caste.
            $this->addCaste(Rights::admin());
            $this->addCaste(Rights::restricted());
        } else {
            $admins  = $this->addCaste(Rights::admin());
            $members = $this->addCaste(Rights::member());
            $logics  = $this->addCaste(Rights::logic());
            $friends = $this->addCaste(Rights::friend());

            /*
             * Create the 'restricted' caste
             */
            $restricted = new UserFilter(new UFC_Caste(array($admins, $members, $logics)));
            $this->addCaste(Rights::restricted())->userfilter($restricted);

            /*
             * Create the 'everybody' caste
             * It's better not to refer to the restricted caste, as we don't know in what
             * order the bubbling is going to happen
             */
            $everybody = new UserFilter(new UFC_Caste(array($admins, $members, $logics, $friends)));
            $this->addCaste(Rights::everybody())->userfilter($everybody);
        }
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

        if (count($mixed) != $collec->count()) {
            throw new ItemNotFoundException('Asking for ' . implode(', ', $mixed) . ' but only found ' . implode(', ', $collec->ids()));
        }

        return $collec;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
