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

class Caste extends Meta
{
    const SELECT_BASE      = 0x01;
    const SELECT_FREQUENCY = 0x02;
    const SELECT_USERS     = 0x04;

    protected $group      = null;
    protected $rights     = null;
    protected $userfilter = null;

    protected $users = null;
    protected $frequency = null;

    public function group()
    {
        return $this->group;
    }

    public static function batchGroups(array $castes)
    {
        $gs = new Collection('Group');
        foreach ($castes as $c) {
            $gs->add($c->group());
        }
        return $gs;
    }

    public function rights()
    {
        return $this->rights;
    }

    public function frequency()
    {
        return $this->frequency;
    }

    /*******************************************************************************
         Users

    *******************************************************************************/

    /**
    * Returns the users belonging to the caste
    */
    public function users()
    {
        return $this->users;
    }

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
        $castes = $this->parents()->select(Caste::SELECT_BASE);

        foreach ($castes as $caste) {
            $caste->compute();
        }
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/

    public function insert()
    {
        XDB::execute('INSERT INTO castes SET gid = {?}, rights = {?}',
                            $this->group->id(), (string) $this->rights);
        $this->id = XDB::insertId();
    }

    public static function batchFrom(array $mixed)
    {
        $collec = new Collection('Caste');

        if (!empty($mixed)) {
            $sql = array();
            foreach ($mixed as $mix)
                $sql[] = XDB::format('(gid = {?} AND rights = {?})', Group::toId($mix['group']), (string) $mix['rights']);

            $iter = XDB::iterRow('SELECT  cid, gid, rights
                                    FROM  castes
                                   WHERE  ' . implode(' OR ', $sql));

            $groups =  new Collection('Group');
            while (list($cid, $gid, $rights) = $iter->next()) {
                $group = $groups->addget($gid);
                $collec->add(new self(array('id' => $cid, 'group' => $group, 'rights' => new Rights($rights))));
            }
        }

        return $collec;
    }

    public static function batchSelect(array $castes, $options = null)
    {
        if (empty($castes))
            return;

        if (empty($options)) {
            $options = self::SELECT_BASE;
        }

        $bits = self::optionsToBits($options);
        $castes = array_combine(self::toIds($castes), $castes);

        $joins = array();
        $cols = array();
        if ($bits & self::SELECT_BASE)
            $cols['c']   = array('gid', 'rights', 'userfilter');
        if ($bits & self::SELECT_FREQUENCY) {
            $cols[-1]    = array('COUNT(cu.uid) AS frequency');
            $joins['cu'] = PlSqlJoin::left('castes_users', '$ME.cid = c.cid');
        }

        if (!empty($cols)) {
            $iter = XDB::iterator('SELECT  c.cid AS id, ' . self::arrayToSqlCols($cols) . '
                                     FROM  castes AS c
                                     ' . PlSqlJoin::formatJoins($joins, array()) . '
                                    WHERE  c.cid IN {?}
                                 GROUP BY  c.cid', self::toIds($castes));

            $groups = new Collection('Group');
            while ($datas = $iter->next()) {
                $datas['rights'] = new Rights($datas['rights']);
                $datas['group']  = $groups->addget($datas['gid']); unset($datas['gid']);
                $datas['userfilter'] = ($datas['userfilter'] === null) ? false :
                                         UserFilter::fromExport(json_decode($datas['userfilter'], true));

                $castes[$datas['id']]->fillFromArray($datas);
            }

            if (!empty($options[self::SELECT_BASE]))
                    $groups->select($options[self::SELECT_BASE]);
        }

        if ($bits & self::SELECT_USERS)
        {
            foreach($castes as $caste)
                $caste->users = new Collection('User');

            $iter = XDB::iterator("SELECT  cid, uid
                                     FROM  castes_users
                                    WHERE  cid IN {?}", self::toIds($castes));

            $users = new Collection('User');
            while ($datas = $iter->next()) {
                $user = $users->addget($datas['uid']);
                $castes[$datas['cid']]->users->add($user);
            }

            if (!empty($options[self::SELECT_USERS]))
                $users->select($options[self::SELECT_USERS]);
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
