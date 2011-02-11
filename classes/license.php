<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                        *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                      *
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

class License extends Meta
{
    /*******************************************************************************
         Constants

    *******************************************************************************/



    /*******************************************************************************
         Properties

    *******************************************************************************/
    
    protected $software = null;
    protected $key = null;
    protected $uid = null;
    protected $user = null;
    protected $comments = null;
    protected $admin = false;

    /*******************************************************************************
         Getters & Setters

    *******************************************************************************/

    static public function getSoftwares()
    {
        return array('visualstudio' => 'Visual Studio .NET',
                     'winxp'        => 'Windows XP Professionnel',
                     'winvista'     => 'Windows Vista Business',
                     '2k3serv'      => 'Windows Serveur 2003',
                     '2k3access'    => 'Access 2003',
                     '2k3onenote'   => 'One Note 2003',
                     '2k3visiopro'  => 'Visio Professionnel 2003',
                     'win2k'        => 'Windows 2000 Professionnel'
                    );
    }
    
    public function software()
    {
        return $this->software;
    }

    public function key()
    {
        return $this->key;
    }
    
    public function uid()
    {
        return $this->uid;
    }
    
    public function user()
    {
        if($this->user == null && $this->uid != null)
        {
            $this->user = new User($this->uid);
        }
        return $this->user;
    }
    
    public function comments($comments = null)
    {
        if($set != null)
        {
            XDB::request('UPDATE  msdnaa_keys 
                             SET  comments = {?}
                           WHERE  key = {?} AND software = {?} AND admin = 0', $comments);
            $this->comments = $comments
        }
        return $this->comments;
    }
    
    public function admin()
    {
        return $this->admin;
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/
    XDB::query('SELECT software, key
                                  FROM msdnaa_keys
                                 WHERE given = 1 AND uid = {?}', S::user()->id)->fetchAllAssoc();
                                 
    public function insert($id = null)
    {
        if ($id == null) {
            XDB::execute('INSERT INTO account SET perms = "user"');
            $this->id = XDB::insertId();
        } else {
            XDB::execute('INSERT INTO account SET uid = {?}, perms= "user"', $id);
            $this->id = $id;
        }

        $group = new Group();
        $group->insert();
        $group->ns(Group::NS_USER);
        $group->name('user_' . $this->id());
        $group->priv(true);
        $group->leavable(false);
        $group->visible(false);
        $group->label('Groupe personnel');

        XDB::execute('UPDATE account SET gid = {?} WHERE uid = {?}', $group->id(), $this->id());

        $group->caste(Rights::admin())->addUser($this);
        $group->caste(Rights::member())->addUser($this);

        $this->group = $group;
    }

    public static function batchSelect(array $users, $options = null)
    {
        if (empty($users))
            return;

        $bits = self::optionsToBits($options);

        if (empty($options)) {
            $bits = User::SELECT_BASE | User::SELECT_ROOMS | User::SELECT_MINIMODULES |
                                        User::SELECT_CASTES | User::SELECT_POLY;
            $options = array(User::SELECT_CASTES => array(Caste::SELECT_BASE => Group::SELECT_BASE));
        }

        $users = array_combine(self::toIds($users), $users);

        // Load datas where 1 User = 1 Line
        $joins = array();
        $cols = array();
        if ($bits & self::SELECT_BASE) {
            $cols['a'] = array('hruid', 'perms', 'state', 'gid',
                               'hash', 'hash_rss', 'original', 'photo', 'gender',
                               'email_format', 'email', 'skin', 'cellphone',
                               'firstname', 'lastname', 'nickname', 'birthdate');
        }

        if ($bits & self::SELECT_POLY) {
            $cols['p']  = array('poly');
            $joins['p'] = PlSqlJoin::left('poly', '$ME.uid = a.uid');
        }

        if (!empty($cols)) {
            $iter = XDB::iterator('SELECT  a.uid AS id, ' . self::arrayToSqlCols($cols) . '
                                     FROM  account AS a
                                           ' . PlSqlJoin::formatJoins($joins, array()) . '
                                    WHERE  a.uid IN {?}', array_keys($users));

            $groups = new Collection('Group');
            while ($datas = $iter->next()) {
                if ($bits & self::SELECT_BASE) {
                    $datas['firstname'] = ucwords(strtolower($datas['firstname']));
                    $datas['lastname']  = ucwords(strtolower($datas['lastname']));

                    $datas['perms'] = new PlFlagSet($datas['perms']);
                    $datas['group'] = $groups->addget($datas['gid']);unset($datas['gid']);
                    $datas['birthdate'] = new FrankizDateTime($datas['birthdate']);

                    $datas['original']  = empty($datas['original']) ? false : new FrankizImage($datas['original']);
                    $datas['photo']     = empty($datas['photo']) ? false : new FrankizImage($datas['photo']);
                }
                $users[$datas['id']]->fillFromArray($datas);
            }
        }

        // Load rooms
        if ($bits & self::SELECT_ROOMS)
        {
            foreach ($users as $u)
                $u->rooms = new Collection('Room');

            $iter = XDB::iterRow('SELECT  uid AS id, rid
                                    FROM  rooms_users
                                   WHERE  uid IN {?}',
                                    array_keys($users));

            $rooms = new Collection('Room');
            while (list($uid, $rid) = $iter->next()) {
                $room = $rooms->addget($rid);
                $users[$uid]->rooms->add($room);
            }

            if (isset($options[self::SELECT_ROOMS]))
                $rooms->select($options[self::SELECT_ROOMS]);
        }

        // Load minimodules
        if ($bits & self::SELECT_MINIMODULES)
        {
            foreach ($users as $u)
                $u->minimodules = FrankizMiniModule::emptyLayout();

            $iter = XDB::iterator('SELECT  uid AS id, name, col, row
                                     FROM  users_minimodules
                                    WHERE  uid IN {?}
                                 ORDER BY  col, row', array_keys($users));

            while ($am = $iter->next())
                array_push($users[$am['id']]->minimodules[$am['col']], $am['name']);
        }

        // Load castes
        if ($bits & self::SELECT_CASTES)
        {
            foreach ($users as $u)
                $u->castes = new Collection('Caste');

            $iter = XDB::iterRow('SELECT  cu.uid, cu.cid
                                    FROM  castes_users AS cu
                                   WHERE  cu.uid IN {?}', array_keys($users));

            $castes = new Collection('Caste');
            while (list($uid, $cid) = $iter->next()) {
                $caste = $castes->addget($cid);
                $users[$uid]->castes->add($caste);
            }

            if (isset($options[self::SELECT_CASTES]))
                $castes->select($options[self::SELECT_CASTES]);
        }

        // Load Studies
        if ($bits & self::SELECT_STUDIES)
        {
            foreach ($users as $u)
                $u->studies = array();

            $iter = XDB::iterator('SELECT  uid, formation_id, year_in, year_out, promo, forlife
                                     FROM  studies
                                    WHERE  uid IN {?}', array_keys($users));

            $formations = new Collection('Formation');
            while ($datas = $iter->next()) {
                $formation_id = $datas['formation_id'];
                $datas['formation'] = $formations->addget($formation_id); unset($datas['formation_id']);
                $users[$datas['uid']]->studies[$formation_id] = new Study($datas);
            }

            if (isset($options[self::SELECT_STUDIES])) {
                $formations->select($options[self::SELECT_STUDIES]);
            }
        }

        // Load comments
        if ($bits & self::SELECT_COMMENTS)
        {
            foreach ($users as $u)
                $u->comments = array();

            $iter = XDB::iterRow('SELECT  uid, gid, comment
                                    FROM  users_comments
                                   WHERE  uid IN {?}', array_keys($users));

            while (list($uid, $gid, $comment) = $iter->next()) {
                $users[$uid]->comments[$gid] = $comment;
            }

        }

    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
