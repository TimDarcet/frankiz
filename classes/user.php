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

class User extends Meta
{
    /*******************************************************************************
         Constants

    *******************************************************************************/

    const GENDER_FEMALE = true;
    const GENDER_MALE   = false;

    const FORMAT_HTML = "html";
    const FORMAT_TEXT = "text";

    const SELECT_BASE         = 0x01;
    const SELECT_SKIN         = 0x02;
    const SELECT_MINIMODULES  = 0x04;
    const SELECT_GROUPS       = 0x08;
    const SELECT_COMMENTS     = 0x20;

    const IMAGE_ORIGINAL      = 0x01;
    const IMAGE_PHOTO         = 0x02;
    const IMAGE_BEST          = 0x03;

    const FIRST_NAME    = 1;
    const LAST_NAME     = 2;
    const NICK_NAME     = 4;
    const ANY_NAME      = 7;

    /*******************************************************************************
         Properties

    *******************************************************************************/

    /**
     * User data storage.
     * By convention, null means the information hasn't been fetched yet, and
     * false means the information is not available.
     */

    // id is internal user ID (potentially numeric), whereas hruid is a
    // "human readable" unique ID
    protected $hruid = null;

    // User main email aliases (forlife is the for-life email address, bestalias
    // is user-chosen preferred email address, email might be any email available
    // for the user).
    protected $forlife = null;
    protected $bestalias = null;
    protected $email = null;

    // Display name is user-chosen name to display (eg. in "Welcome
    // <display name> !"), while full name is the official full name.
    protected $display_name = null;
    protected $full_name = null;

    // Other important parameters used when sending emails.
    protected $gender = null;  // Acceptable values are GENDER_MALE and GENDER_FEMALE
    protected $email_format = null;  // Acceptable values are FORMAT_HTML and FORMAT_TEXT

    // Permissions
    public    $perms = null;  // TODO: getter & setter
    protected $perm_flags = null;

    // enum('active','pending','unregistered','disabled')
    protected $state = null;

    // The name of the user's prefered skin
    protected $skin = null;

    // Collection of groups and their associated rights and comments
    protected $groups   = null;
    protected $rights   = null;
    protected $comments = null;

    // Contains the hash sent by mail to recover the password
    protected $hash = null;

    // Contains the iid of the original picture
    protected $original = null;

    // Contains the iid of the current picture
    protected $photo = null;

    // Contains an array of minimodules
    protected $minimodules = null;


    /*******************************************************************************
         Getters & Setters

    *******************************************************************************/

    public function login()
    {
        return $this->hruid;
    }

    public function bestEmail()
    {
        if (!empty($this->bestalias)) {
            return $this->bestalias;
        }
        return $this->email;
    }

    public function forlifeEmail()
    {
        if (!empty($this->forlife)) {
            return $this->forlife;
        }
        return $this->email;
    }

    public function displayName()
    {
        return $this->display_name;
    }

    public function fullName()
    {
        return $this->full_name;
    }

    // Fallback value is GENDER_MALE.
    public function isFemale()
    {
        return $this->gender == self::GENDER_FEMALE;
    }

    // Fallback value is FORMAT_TEXT.
    public function isEmailFormatHtml()
    {
        return $this->email_format == self::FORMAT_HTML;
    }

    /**
    * Returns the password of the User
    *
    * @param $password If specified, update the password in the database
    */
    public function password($password = null)
    {
        if ($password != null)
        {
            $this->password = hash_encrypt($password);
            XDB::execute('UPDATE account SET password = {?} WHERE uid = {?}',
                                                 $this->password, $this->id());
        }
        return XDB::fetchOneCell('SELECT  password FROM  account WHERE  uid = {?}', $this->id());
    }

    public function image($bits = self::IMAGE_BEST)
    {
        if (($bits & self::IMAGE_PHOTO) && (!empty($this->photo)))
            return $this->photo;
        if (($bits & self::IMAGE_ORIGINAL) && (!empty($this->original)))
            return $this->original;

        return false;
    }

    /**
    * Returns the skin name of the User
    *
    * @param $skin name of the skin to associate with the User
    */
    public function skin($skin = null)
    {
        if ($skin != null)
        {
            $res = XDB::query('SELECT skin_id FROM skins WHERE name = {?}', $skin);

            if ($res->numRows() != 1)
                throw new Exception ("Cette skin n'existe pas et ne peut donc pas être choisie");

            $this->skin = $res->fetchOneField();
            XDB::execute('UPDATE account SET skin = {?} WHERE uid = {?}', $this->skin, $this->id());
        }
        return $this->skin;
    }

    /**
    * Returns the hash sent by password to recover the password
    *
    * @param $hash If specified, update the Hash
    */
    public function hash($hash = null)
    {
        if ($hash != null) {
            $this->hash = $hash;
            XDB::execute('UPDATE account SET hash = {?} WHERE uid = {?}', $this->hash, $this->id());
        }
        return $this->hash;
    }

    /*******************************************************************************
         Minimodules

    *******************************************************************************/

    public function minimodules($col = null)
    {
        if (empty($col))
        {
            $minimodules = array();
            foreach ($this->minimodules as $rows)
                foreach ($rows as $name)
                        $minimodules[] = $name;
            return $minimodules;
        } else {
            ksort($this->minimodules[$col]);
            return $this->minimodules[$col];
        }
    }

    /**
    * Add a MiniModule to the user
    * @param $m the minimodule to add
    */
    public function addMinimodule(FrankizMiniModule $m)
    {
        if (!$m->checkAuthAndPerms())
            return false;

        XDB::execute('INSERT INTO  users_minimodules
                              SET  uid = {?}, name = {?}, col = "COL_FLOAT",
                                   row = (SELECT COALESCE(MIN(um.row) - 1, -1)
                                            FROM users_minimodules AS um WHERE um.uid = {?})
          ON DUPLICATE KEY UPDATE  row = (SELECT COALESCE(MIN(um.row) - 1, -1)
                                            FROM users_minimodules AS um WHERE um.uid = {?})',
                                            $this->id(), $m->name(), $this->id(), $this->id());

        if (!(XDB::affectedRows() > 0))
            return false;

        array_unshift($this->minimodules[FrankizMiniModule::COL_FLOAT], $m->name());
        return true;
    }

    public function layoutMinimodules(array $layout)
    {
        $cols = array_keys(FrankizMiniModule::emptyLayout());

        $sql = array();
        foreach($cols as $col)
            if (isset($layout[$col]))
                foreach ($layout[$col] as $row => $name)
                    $sql[] = XDB::format('({?}, {?}, {?}, {?})', S::user()->id(), $name, $col, $row);

        XDB::execute('INSERT INTO  users_minimodules (uid, name, col, row)
                           VALUES  '.implode(', ', $sql).'
          ON DUPLICATE KEY UPDATE  col = VALUES(col), row = VALUES(row)');

        if (!(XDB::affectedRows() > 0))
            return false;

        $this->select(self::SELECT_MINIMODULES);
        return true;
    }

    public function removeMinimodule(FrankizMiniModule $m)
    {
        $rmName = $m->name();
        XDB::execute('DELETE FROM users_minimodules WHERE uid = {?} AND name = {?}',
                                                          $this->id(), $rmName);
        if (XDB::affectedRows() > 0) {
            $cols = array_keys(FrankizMiniModule::emptyLayout());
            foreach ($cols as $col) {
                $this->minimodules[$col] =
                      array_filter($this->minimodules[$col],
                                  function($name) use($rmName) {
                                      return $name != $rmName;
                                  });
            }
            return true;
        }
        return false;
    }

    /*******************************************************************************
         Permissions

    *******************************************************************************/

    public function checkPerms($perms)
    {
        if (is_null($this->perm_flags)) {
            $this->buildPerms();
        }
        if (is_null($this->perm_flags)) {
            return false;
        }
        return $this->perm_flags->hasFlagCombination($perms);
    }

    // Specialization of the buildPerms method
    // This function build 'generic' permissions for the user.
    protected function buildPerms()
    {
        if (!is_null($this->perm_flags)) {
            return;
        }
        if ($this->perms === null) {
             $this->select();
        }
        $this->perm_flags = self::makePerms($this->perms);
    }

    // Return permission flags for a given permission level.
    public static function makePerms($perms)
    {
        $flags = new PlFlagSet();
        if (is_null($flags) || $perms == 'disabled') {
            return $flags;
        }
        $flags->addFlag(PERMS_USER);
        if ($perms == 'admin') {
            $flags->addFlag(PERMS_ADMIN);
        }
        return $flags;
    }

    /*******************************************************************************
         Groups

    *******************************************************************************/

    /**
    * Returns or updates the comment binding an user to a group.
    * The user *must* already be bound to the specified group
    * @param $g the group
    * @param $comments if specified, let the function set the comment
    */
    public function comments($g, $comments = null)
    {
        $gid = Group::toId($g);
        if ($comments !== null)
        {
            XDB::execute('UPDATE  users_groups
                             SET  comments = {?}
                           WHERE  uid = {?} AND gid = {?} LIMIT 1',
                         $comments, $this->id(), $gid);
            if (isset($this->comments[$gid]))
                $this->comments[$gid] = $comments;
            return;
        }
        return $this->comments[$gid];
    }

    /**
    * Returns the groups of the user
    * @param $ns returns only groups with this namespace
    * @param $rights return only groups in which the user has those rights
    */
    public function groups($ns = null, $rights = null)
    {
        if (empty($ns))
            return $this->groups;

        return $this->groups->filter('ns', $ns);
    }

    public function addToGroup($g, Rights $rights)
    {
        $gid = Group::toId($g);
        XDB::execute('INSERT INTO  users_groups
                              SET  uid = {?}, gid = {?}, rights = {?}, comments = ""
          ON DUPLICATE KEY UPDATE  rights = CONCAT_WS(",", rights, {?})',
                                   $this->id(), $gid, $rights->flags(), $rights->flags());
    }

    public function removeFromGroup($g, Rights $rights = null)
    {
        $gid = Group::toId($g);
        if ($rights === null)
        {
            XDB::execute('DELETE FROM  users_groups
                                WHERE  uid = {?} AND gid = {?}',
                                         $this->id(), $gid);
        } else {
            XDB::execute('UPDATE  users_groups
                             SET  rights = REPLACE(rights , {?}, "")
                           WHERE  uid = {?} AND gid = {?}',
                                  $this->id(), $gid);
            XDB::execute('DELETE FROM  users_groups
                                WHERE  uid = {?} AND gid = {?} AND ',
                                         $this->id(), $gid);
        }
    }

    public function rights($g)
    {
        return $this->rights[$this->groups->get($g)->id()];
    }

    /*******************************************************************************
         Miscellaneous

    *******************************************************************************/

    // Implementation of the default user callback.
    public static function _default_user_callback($login, $results)
    {
        $result_count = count($results);
        if ($result_count == 0 || !S::has_perms()) {
            Platal::page()->trigError("Il n'y a pas d'utilisateur avec l'identifiant : $login");
        } else {
            Platal::page()->trigError("Il y a $result_count utilisateurs avec cet identifiant : " . join(', ', $results));
        }
    }

    public static function getSilentWithValues($login, $values)
    {
        if ($login == 0)
            return new AnonymousUser();
        else
            return User::getWithValues($login, $values, array('User', '_silent_user_callback'));
    }

    public static function getWithValues($login, $values, $callback = false)
    {
        if (!$callback) {
            $callback = array('User', '_default_user_callback');
        }

        try {
            $u = new User($values['id']);
            $u->select();
        } catch (UserNotFoundException $e) {
            return call_user_func($callback, $login, $e->results);
        }
    }

    public static function getNameVariants($name)
    {
        $ret = array();
        if($name & self::FIRST_NAME) {
            $ret[] = self::FIRST_NAME;
        }
        if($name & self::LAST_NAME) {
            $ret[] = self::LAST_NAME;
        }
        if($name & self::NICK_NAME) {
            $ret[] = self::NICK_NAME;
        }
        return $ret;
    }

    // Tries to find the user forlife from data in cookies
    public static function getForlifeFromCookie() {
        if (!Cookie::has('uid')) {
            return "";
        }
        $uid = Cookie::i('uid');
        if (Cookie::has('domain')) {
            $res = XDB::query("SELECT s.forlife
                                 FROM studies AS s
                            LEFT JOIN formations AS f USING (formation_id)
                                WHERE s.uid = {?} AND f.domain = {?}",
                                $uid, Cookie::s('domain'));
            // If a forlife was found, send it ; otherwise, domain wasn't consistent
            if ($res->numRows()) {
                return $res->fetchOneCell();
            }
        }
        $res = XDB::query("SELECT s.forlife
                             FROM studies AS s
                        LEFT JOIN account AS a ON (a.main_formation = s.formation_id)
                            WHERE a.uid = {?}",
                            $uid);
        if ($res->numRows()) {
            return $res->fetchOneCell();
        }
        return "";
    }

    // Tries to find the user domain from data in cookies
    public static function getDomainFromCookie() {
        if(Cookie::has('domain')) {
            return Cookie::s('domain', '');
        }
        if(Cookie::has('uid')) {
            $uid = Cookie::i('uid');
            $res = XDB::query("SELECT f.domain
                                 FROM formations AS f
                            LEFT JOIN account AS a ON (a.main_formation = f.formation_id)
                                WHERE a.uid = {?}",
                                $uid);
            if ($res->numRows()) {
                return $res->fetchOneCell();
            }
        }
        return "";
    }

    public function export()
    {
        $export = parent::export();

        if ($this->display_name !== null)
            $export['displayName'] = $this->displayName();

        return $export;
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/

    public function fillFromArray(array $values)
    {
        if (isset($values['original'])) {
            $this->original = new FrankizImage($values['original']);
            unset($values['original']);
        }

        if (isset($values['photo'])) {
            $this->photo = new FrankizImage($values['photo']);
            unset($values['photo']);
        }

        if (isset($values['gender']) && ($values['gender'] == 'man' || $values['gender'] == 'woman')) {
            $values['gender'] = (bool) ($values['gender'] == 'woman');
            unset($values['gender']);
        }

        if (!isset($values['full_name']) && isset($values['firstname']) && isset($values['lastname'])) {
            $values['full_name'] = $values['firstname'] . ' ' . $values['lastname'];
        }

        if (!isset($values['display_name']) && isset($values['nickname']) && isset($values['firstname'])) {
            $values['display_name'] = (empty($values['nickname'])) ? $values['firstname'] : $values['nickname'];
        }

        parent::fillFromArray($values);
    }

    public static function batchSelect(array $users, $options = null)
    {
        if (empty($users))
            return;

        if (empty($options)) {
            $bits = User::SELECT_BASE | User::SELECT_SKIN | User::SELECT_MINIMODULES | SELECT_GROUPS;
            $options = array(User::SELECT_GROUPS => array("options" => Group::SELECT_BASE | Group::SELECT_FREQUENCY,
                                                          "ns" => Group::NS_BINET));
        } else {
            $bits = self::optionsToBits($options);
        }

        $users = array_combine(self::toIds($users), $users);

        // Load datas where 1 User = 1 Line
        $joins = array();
        $cols = array();
        if ($bits & self::SELECT_BASE) {
            $cols['a'] = array('hruid', 'perms', 'state',
                               'hash', 'original', 'photo', 'gender',
                               'email_format', 'bestalias',
                               'firstname', 'lastname', 'nickname');
        }

        if ($bits & self::SELECT_SKIN) {
            $cols['sk'] = array('name AS skin');
            $joins['sk'] = PlSqlJoin::left('skins', '$ME.skin_id = a.skin');
        }

        if (!empty($cols)) {
            $iter = XDB::iterator('SELECT  a.uid AS id, ' . self::arrayToSqlCols($cols) . '
                                     FROM  account AS a
                                           ' . PlSqlJoin::formatJoins($joins, array()) . '
                                    WHERE  a.uid IN {?}', array_keys($users));

            while ($datas = $iter->next())
                $users[$datas['id']]->fillFromArray($datas);
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

        // Load groups
        if ($bits & self::SELECT_GROUPS)
        {
            foreach ($users as $u) {
                $u->groups = new Collection('Group');
                $u->rights = array();
            }

            $select_comments = (isset($options[self::SELECT_GROUPS]) &&
                                isset($options[self::SELECT_GROUPS]["comments"]) &&
                                $options[self::SELECT_GROUPS]["comments"]) ? true : false;

            $namespaces = (isset($options[self::SELECT_GROUPS]) &&
                                isset($options[self::SELECT_GROUPS]["ns"]) &&
                                $options[self::SELECT_GROUPS]["ns"]) ? $options[self::SELECT_GROUPS]["ns"] : Group::NS_BINET;

            $comments = ($select_comments) ? ', ug.comments' : '';

            $iter = XDB::iterRow('SELECT  ug.uid, ug.gid, ug.rights, g.ns' . $comments . '
                                     FROM  users_groups AS ug
                               INNER JOIN  groups AS g ON g.gid = ug.gid
                                    WHERE  g.ns IN {?} AND ug.uid IN {?}',
                                    unflatten($namespaces), array_keys($users));

            $groups = new Collection('Group');
            while ($line = $iter->next())
            {
                if ($select_comments)
                    list($uid, $gid, $rights, $ns, $comments) = $line;
                else
                    list($uid, $gid, $rights, $ns) = $line;

                $group = $groups->get($gid);
                if ($group == false) {
                    $group = new Group(array('id' => $gid, 'ns' => $ns));
                    $groups->add($group);
                }

                $user = $users[$uid];
                $user->groups->add($group);
                $user->rights[$gid] = new Rights($rights);
                if ($select_comments)
                    $user->comments[$gid] = $comments;
            }

            if (isset($options[self::SELECT_GROUPS]) &&
                isset($options[self::SELECT_GROUPS]["options"]) &&
                $options[self::SELECT_GROUPS]["options"])
                $groups->select($options[self::SELECT_GROUPS]["options"]);
        }

    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
