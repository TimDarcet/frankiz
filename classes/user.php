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

class User extends PlUser
{
    /*Reminder of PlUser fields
     * uid           id()
     * hruid         login()              prenom.nom.x / prenom.nom.supop / ...
     * bestalias     bestEmail()          preferred email address
     * display_name  displayName()        Pseudo
     * full_name     fullName()           Prenom Nom
     * gender        isFemale()           GENDER_MALE | GENDER_FEMALE
     * email_format  isEmailFormatHtml()  FORMAT_HTML | FORMAT_TEXT
     * perms                              Serialized perm_flags
     * perm_flags    checkPerms()         Flag combination describing user perms
     * DON'T USE! NOT IMPLEMENTED IN FRANKIZ :
     * forlife       forlifeEmail()
     */

    // TODO: try to write something like the Group class, where
    // the queries are factorised and the Users are stored in a
    // private field (careful : PlUser::get() already exists and
    // it might be hard to redefin the constructor ...)

    // boolean to specify if the user is present on the platal
    protected $on_platal = null;

    // enum('active','pending','unregistered','disabled')
    protected $state = null;

    // The name of the user's prefered skin
    protected $skin = null;

    // Array of the Gids and the rights associated
    protected $groups = null;

    // Contains the hash sent by mail to recover the password
    protected $hash = null;

    // Contains the iid of the original picture
    protected $original = null;

    // Contains the iid of the current picture
    protected $photo = null;

    // Contains an array of minimodules
    protected $minimodules = null;

    const SELECT_BASE         = 0x01;
    const SELECT_SKIN         = 0x02;
    const SELECT_MINIMODULES  = 0x04;
    const SELECT_GROUPS       = 0x08;

    const IMAGE_ORIGINAL      = 0x01;
    const IMAGE_PHOTO         = 0x02;
    const IMAGE_BEST          = 0x03;

    /** Constructs the User object
     *
     * @param $datas The User id or an array with User datas
     */
    public function __construct($datas)
    {
        if (!is_array($datas))
            $this->uid = $datas;
        else
            $this->fillFromArray($datas);
    }

    // Implementation of the login to user_id method.
    protected function getLogin($login)
    {
        global $globals;
        if (!$login) {
            throw new UserNotFoundException();
        }

        // If $data is an integer, fetches directly the result.
        if (is_numeric($login)) {
            $res = XDB::query("SELECT uid FROM account WHERE uid = {?}", $login);
            if ($res->numRows()) {
                return $res->fetchOneCell();
            }

            throw new UserNotFoundException();
        }

        // Checks whether $login is a valid hruid or not.
        $res = XDB::query("SELECT uid FROM account WHERE hruid = {?}", $login);
        if ($res->numRows()) {
            return $res->fetchOneCell();
        }

        // From now, $login can only by an email alias.
        $login = trim(strtolower($login));
        if (strstr($login, '@') === false) {
            throw new UserNotFoundException();
        }

        // Checks if $login is a valid alias on the main domains.
        list($mbox, $fqdn) = explode('@', $login);
        $res = XDB::query("SELECT  s.uid
                             FROM  studies AS s
                       INNER JOIN  formations AS f ON (f.formation_id = s.formation_id )
                            WHERE  s.forlife = {?} AND f.domain = {?}", $mbox, $fqdn);
        if ($res->numRows()) {
            return $res->fetchOneCell();
        }
        throw new UserNotFoundException();
    }

    // Implementation of the data loader.
    protected function loadMainFields()
    {
        if ($this->hruid !== null && $this->display_name !== null
            && $this->full_name !== null && $this->gender !== null
            && $this->on_platal !== null && $this->email_format !== null
            && $this->perms !== null && $this->bestalias !== null
            && $this->skin !== null && $this->state !== null
            && $this->hash !== null) {
            return;
        }

        $this->select(User::SELECT_BASE | User::SELECT_SKIN);
    }

    // Specialization of the fillFromArray method, to implement hacks to enable
    // lazy loading of user's main properties from the session.
    protected function fillFromArray(array $values)
    {
        if (isset($values['original'])) {
            $this->original = new FrankizImage($values['original']);
            unset($values['original']);
        }

        if (isset($values['photo'])) {
            $this->photo = new FrankizImage($values['photo']);
            unset($values['photo']);
        }

        // We also need to convert the gender (usually named "femme"), and the
        // email format parameter (valued "texte" instead of "text").
        if (isset($values['gender']) && ($values['gender'] == 'man' || $values['gender'] == 'woman'))
            $values['gender'] = (bool) ($values['gender'] == 'woman');

        if (!isset($values['full_name']) && isset($values['firstname']) && isset($values['lastname']))
            $values['full_name'] = $values['firstname'] . ' ' . $values['lastname'];

        if (!isset($values['display_name']) && isset($values['nickname']) && isset($values['firstname']))
            $values['display_name'] = (empty($values['nickname'])) ? $values['firstname'] : $values['nickname'];

        foreach ($values as $key => $value)
            if (property_exists($this, $key))
                $this->$key = $value;
    }

    // Specialization of the buildPerms method
    // This function build 'generic' permissions for the user.
    protected function buildPerms()
    {
        if (!is_null($this->perm_flags)) {
            return;
        }
        if ($this->perms === null) {
             $this->loadMainFields();
        }
        $this->perm_flags = self::makePerms($this->perms);
    }

    /**
    * Returns the password of the User
    * If you specify an argument, it will update the User's password
    *
    * @param $password password to be hashed and put in the database
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
    * If you specify an argument, it will update the User's skin
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
    * If you specify an argument, it will update the hash
    *
    * @param $hash
    */
    public function hash($hash = null)
    {
        if ($hash != null) {
            $this->hash = $hash;
            XDB::execute('UPDATE account SET hash = {?} WHERE uid = {?}', $this->hash, $this->id());
        }
        return $this->hash;
    }

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

    public function groups($rights)
    {
        return $this->groups[$rights];
    }

    public function addToGroup($gid, PlFlagSet $rights)
    {
        // TODO
    }

    public function removeFromGroup($gid)
    {
        // TODO
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

    // Implementation of the static email locality checker.
    public static function isForeignEmailAddress($email)
    {
        @list($user, $domain) = explode('@', $email);
        if ($domain == "polytechnique.edu") {
            return false;
        }
        return true;
    }

    const FIRST_NAME    = 1;
    const LAST_NAME     = 2;
    const NICK_NAME     = 4;
    const ANY_NAME      = 7;

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

    public static function getSilentWithValues($login, $values)
    {
        if ($login == 0)
            return new AnonymousUser();
        else
            return parent::getSilentWithValues($login, $values);
    }

    public static function getWithValues($login, $values, $callback = false)
    {
        if (!$callback) {
            $callback = array('User', '_default_user_callback');
        }

        try {
            $u = new User($values['uid']);
            return $u->select(User::SELECT_BASE | User::SELECT_SKIN | User::SELECT_MINIMODULES | User::SELECT_GROUPS);
        } catch (UserNotFoundException $e) {
            return call_user_func($callback, $login, $e->results);
        }
    }

    public function select($fields)
    {
        self::batchSelect(array($this), $fields);
        return $this;
    }

    public static function toIds(array $users)
    {
        $result = array();
        foreach ($users as $n)
            if ($n instanceof User)
                $result[] = $n->id();
            else
                $result[] = $n;
        return $result;
    }

    public static function batchSelect(array $_users, $fields)
    {
        if (count($_users) < 1)
            return;

        // Index the array
        $users = array_combine(self::toIds($_users), $_users);

        // Load datas where 1 User = 1 Line
        $joints = array();
        $columns = array();
        if ($fields & self::SELECT_BASE) {
            $columns['a'] = array('hruid', 'perms', 'state',
                                   'hash', 'original', 'photo', 'gender',
                                   'on_platal', 'email_format', 'bestalias',
                                   'firstname', 'lastname', 'nickname');
        }

        if ($fields & self::SELECT_SKIN) {
            $columns['sk'] = array('name AS skin');
            $joints['sk'] = PlSqlJoin::left('skins', '$ME.skin_id = a.skin');
        }

        if (!empty($columns)) {
            $sql_columns = array();
            foreach($columns as $table => $cols)
                $sql_columns[] = implode(', ', array_map(function($value) use($table) { return $table . '.' . $value; }, $cols));

            $iter = XDB::iterator('SELECT  a.uid, ' . implode(', ', $sql_columns) . '
                                     FROM  account AS a
                                           ' . PlSqlJoin::formatJoins($joints, array()) . '
                                    WHERE  a.uid IN {?}', array_keys($users));

            while ($array_datas = $iter->next())
                $users[$array_datas['uid']]->fillFromArray($array_datas);
        }

        // Load minimodules
        if ($fields & self::SELECT_MINIMODULES) {
            foreach ($users as $u)
                $u->minimodules = FrankizMiniModule::emptyLayout();

            $iter = XDB::iterator('SELECT  uid, name, col, row
                                     FROM  users_minimodules
                                    WHERE  uid IN {?}
                                 ORDER BY  col, row', array_keys($users));

            while ($am = $iter->next())
                array_push($users[$am['uid']]->minimodules[$am['col']], $am['name']);
        }

        // Load groups
        // TODO: enable selective loading (SUPER ain't needed for the TOL)
        if ($fields & self::SELECT_GROUPS) {
            foreach ($users as $u)
                $u->groups = Rights::emptyLayout();

            $iter = XDB::iterator('SELECT  ug.uid, ug.rights, g.gid id, g.L, g.R, g.depth
                                     FROM  users_groups AS ug
                               INNER JOIN  groups AS g ON g.gid = ug.gid
                                    WHERE  ug.uid IN {?}', array_keys($users));

            while ($agr = $iter->next()) {
                $rights = new PlFlagSet($agr['rights']);
                foreach($rights as $right)
                    $users[$agr['uid']]->groups[$right]->add(new Group($agr));
            }

            $ascending  = Collection::fromClass('Group');
            $descending = Collection::fromClass('Group');
            $fixed      = Collection::fromClass('Group');
            $rights = Rights::inheritance();
            foreach ($users as $u)
                foreach ($rights as $right => $inheritType)
                    if ($inheritType == Rights::ASCENDING)
                        $ascending->merge($u->groups[$right]);
                    elseif ($inheritType == Rights::DESCENDING)
                        $descending->merge($u->groups[$right]);
                    else
                        $fixed->merge($u->groups[$right]);

            $ascending  =  $ascending->select(array(Group::SELECT_FATHERS  => Group::MAX_DEPTH))->roots();
            $descending = $descending->select(array(Group::SELECT_CHILDREN => Group::MAX_DEPTH))->roots();

            if ($ascending->count() > 0)
                $ascending  =  $ascending->flatten();
            if ($descending->count() > 0)
                $descending = $descending->flatten();

            $groups = new Collection();
            $groups->merge($ascending)->merge($descending)->merge($fixed);
            $groups->buildLinks();

            $groups = $groups->roots();
            if ($groups->count() > 0) {
                $groups->select(Group::SELECT_BASE);

                foreach ($users as $u) {
                    foreach ($rights as $right => $inheritType) {
                        if ($inheritType == Rights::ASCENDING)
                            $u->groups[$right] = $groups->fathersOf($u->groups[$right]);
                        elseif ($inheritType == Rights::DESCENDING)
                            $u->groups[$right] = $groups->childrenOf($u->groups[$right]);
                        else
                            $u->groups[$right] = $u->groups[$right]->buildLinks();
                    }
                }
            }
        }

    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
