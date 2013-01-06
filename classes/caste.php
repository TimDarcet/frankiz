<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
 *  http://br.binets.fr/                                                   *
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

class CasteSchema extends Schema
{
    public function className() {
        return 'Caste';
    }

    public function table() {
        return 'castes';
    }

    public function id() {
        return 'cid';
    }

    public function tableAs() {
        return 'c';
    }

    public function objects() {
        return array('group' => 'Group',
                    'rights' => 'Rights',
                'userfilter' => 'UserFilter');
    }

    public function collections() {
        return array('users' => array('User', 'castes_users', 'uid'));
    }
}

class CasteSelect extends Select
{
    public function className() {
        return 'Caste';
    }

    public static function base($subs = null) {
        return new CasteSelect(array('group', 'rights', 'userfilter'), $subs);
    }

    public static function group() {
        return new CasteSelect(array('group', 'rights'), array('group' => GroupSelect::base()));
    }

    public static function validate() {
        return new CasteSelect(array('group', 'rights'), array('group' => GroupSelect::validate()));
    }

    public static function bubble($subs = null) {
        return new CasteSelect(array('userfilter'), $subs);
    }

    public static function users($subs = null) {
        return new CasteSelect(array('users'), $subs);
    }

    protected function handlers() {
        return array('main' => array('group', 'rights', 'userfilter'),
              'collections' => array('users'));
    }
}

class Caste extends Meta
{
    protected $group      = null;
    protected $rights     = null;
    protected $userfilter = null;

    protected $users = null;

    public static function batchGroups(array $castes)
    {
        $gs = new Collection('Group');
        foreach ($castes as $c) {
            $gs->add($c->group());
        }
        return $gs;
    }

    /*******************************************************************************
         Users

    *******************************************************************************/

    public function hasUser(User $user = null)
    {
        if ($user === null)
            $user = S::user();

        return $this->users->get($user);
    }

    /**
    * Add a User to the caste
    *
    * @param $user  A User
    */
    public function addUser(User $user)
    {
        XDB::execute('INSERT IGNORE  castes_users
                                SET  cid = {?}, uid = {?}',
                                     $this->id(), $user->id());
        $this->bubble();
    }

    /**
    * Remove a User frome the caste
    *
    * @param $user  A User
    */
    public function removeUser(User $user)
    {
        if ($this->userfilter()) {
            throw new Exception("This caste is defined by a UserFilter, you can't remove a user");
        }
        XDB::execute('DELETE FROM  castes_users
                            WHERE  cid = {?} AND uid = {?}',
                                   $this->id(), $user->id());
        $this->bubble();
    }

    /*******************************************************************************
         UserFilter

    *******************************************************************************/

    /**
    * Gets or sets the userfilter defining the caste
    * /!\ The userfilter must have been fetched before, even to set a new one.
    *
    * @param $userfilter  A UserFilter or false to unset it
    */
    public function userfilter($userfilter = null)
    {
        if ($userfilter !== null)
        {
            if ($userfilter === false) {
                XDB::execute('UPDATE castes SET userfilter = NULL WHERE cid = {?}', $this->id());
                XDB::execute('DELETE FROM castes_dependencies WHERE cid = {?}', $this->id());
                if ($this->userfilter() !== false) {
                    XDB::execute('DELETE FROM castes_users WHERE cid = {?}', $this->id());
                }
            } else {
                XDB::execute('UPDATE castes SET userfilter = {?} WHERE cid = {?}',
                                         json_encode($userfilter->export()), $this->id());
                $castes = $userfilter->dependencies();
                if (!empty($castes)) {
                    $sql = array();
                    foreach ($castes as $caste)
                        $sql[] = XDB::format('({?}, "caste", {?})', $this->id(), $caste);

                    XDB::execute('INSERT INTO castes_dependencies (cid, type, id)
                                    VALUES ' . implode(', ', $sql));
                }
            }
            $this->userfilter = $userfilter;
            $this->compute();
        }

        return $this->userfilter;
    }

    public function compute()
    {
        if ($this->userfilter === null) {
            throw new Exception("The UserFilter (if it exists) of the caste (" . $this->id() . ") hasn't been fetched");
        }

        if ($this->userfilter !== false) {
            // First: flush the users
            XDB::execute('DELETE FROM castes_users WHERE cid = {?}', $this->id());

            // Second: search the users corresponding to the filter
            $this->users = $this->userfilter->get();

            // Third: repopulate the table with them
            if ($this->users->count() > 0) {
                $sql = array();
                foreach ($this->users as $user)
                    $sql[] = XDB::format('({?}, {?})', $this->id(), $user->id());

                XDB::execute('INSERT INTO castes_users (cid, uid)
                                VALUES ' . implode(', ', $sql));
            }
        }

        $this->bubble();
    }

    public function parents() {
        $iter = XDB::iterRow("SELECT  cid, type, id
                                FROM  castes_dependencies
                               WHERE  id = {?}", $this->id());

        $castes = new Collection('Caste');
        while (list($cid, $type, $id) = $iter->next()) {
            // For the time-being, only type=caste is supported
            if ($type == 'caste') {
                $castes->add($cid);
            }
        }

        return $castes;
    }

    public function bubble()
    {
        $castes = $this->parents()->select(CasteSelect::bubble());

        foreach ($castes as $caste) {
            $caste->compute();
        }
    }

    public function isRights($rights) {
        if (!$rights instanceof Rights) {
            $rights = new Rights($rights);
        }
        return $this->rights->isMe($rights);
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/

    public function insert()
    {
        XDB::execute('INSERT INTO castes SET `group` = {?}, rights = {?}',
                            $this->group->id(), (string) $this->rights);
        $this->id = XDB::insertId();
    }

    public static function batchFrom(array $mixed)
    {
        $collec = new Collection('Caste');

        if (!empty($mixed)) {
            $sql = array();
            foreach ($mixed as $mix)
                $sql[] = XDB::format('(`group` = {?} AND rights = {?})', Group::toId($mix['group']), (string) $mix['rights']);

            $iter = XDB::iterRow('SELECT  cid, `group`, rights
                                    FROM  castes
                                   WHERE  ' . implode(' OR ', $sql));

            $groups =  new Collection('Group');
            while (list($cid, $group, $rights) = $iter->next()) {
                $group = $groups->addget($group);
                $collec->add(new self(array('id' => $cid, 'group' => $group, 'rights' => new Rights($rights))));
            }
        }

        return $collec;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
