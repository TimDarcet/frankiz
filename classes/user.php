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

    const GENDER_FEMALE = 'woman';
    const GENDER_MALE   = 'man';

    const FORMAT_HTML = "html";
    const FORMAT_TEXT = "text";

    const SELECT_BASE         = 0x01;
    const SELECT_ROOMS        = 0x02;
    const SELECT_MINIMODULES  = 0x04;
    const SELECT_CASTES       = 0x08;
    const SELECT_STUDIES      = 0x10;
    const SELECT_COMMENTS     = 0x20;
    const SELECT_POLY         = 0x40;

    const IMAGE_ORIGINAL      = 0x01;
    const IMAGE_PHOTO         = 0x02;
    const IMAGE_BEST          = 0x03;

    const EXPORT_MICRO        = 0x01;
    const EXPORT_SMALL        = 0x02;

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

    // the NS_USER-type group owned by the user
    protected $group = null;

    // Studies
    protected $studies = null;

    // Mails
    protected $email = null;
    protected $email_format = null;  // Acceptable values are FORMAT_HTML and FORMAT_TEXT

    // Names
    protected $firstname = null;
    protected $lastname  = null;
    protected $nickname  = null;

    // Gender
    protected $gender = null;  // Acceptable values are GENDER_MALE and GENDER_FEMALE

    // Permissions
    protected $perms = null;
    protected $perm_flags = null;

    // enum('active','pending','unregistered','disabled')
    protected $state = null;

    // The name of the user's prefered skin
    protected $skin = null;

    // Collection of castes
    protected $castes   = null;
    protected $comments = null;

    // Contains the hash sent by mail to recover the password
    protected $hash = null;

    // Contains the hash to receive rss flow
    protected $hash_rss = null;

    // Contains the original picture
    protected $original = null;

    // Contains the current picture
    protected $photo = null;

    // Contains an array of minimodules
    protected $minimodules = null;

    // Rooms
    protected $rooms = null;

    // Miscellaneous
    protected $birthdate = null;
    protected $cellphone = null;

    // Poly
    protected $poly = null;

    /*******************************************************************************
         Getters & Setters

    *******************************************************************************/

    public function login($login = null)
    {
        if ($login != null) {
            $this->hruid = $login;
            XDB::execute('UPDATE account SET hruid = {?} WHERE uid = {?}', $login, $this->id());
        }
        return $this->hruid;
    }

    public function bestEmail()
    {
        return $this->email;
    }

    public function firstname($firstname = null)
    {
        if ($firstname != null) {
            $this->firstname = $firstname;
            XDB::execute('UPDATE account SET firstname = {?} WHERE uid = {?}', $this->firstname, $this->id());
        }
        return $this->firstname;
    }

    public function lastname($lastname = null)
    {
        if ($lastname != null) {
            $this->lastname = $lastname;
            XDB::execute('UPDATE account SET lastname = {?} WHERE uid = {?}', $this->lastname, $this->id());
        }
        return $this->lastname;
    }

    public function nickname($nickname = null)
    {
        if ($nickname != null) {
            $this->nickname = $nickname;
            XDB::execute('UPDATE account SET nickname = {?} WHERE uid = {?}', $this->nickname, $this->id());
        }
        return $this->nickname;
    }

    public function displayName()
    {
        return (empty($this->nickname)) ? $this->firstname : $this->nickname;
    }

    public function fullName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function gender($gender = null)
    {
        if ($gender !== null)
        {
            $this->gender = $gender;
            XDB::execute('UPDATE account SET gender = {?} WHERE uid = {?}', $gender, $this->id());
        }
        return $this->gender;
    }

    public function isFemale()
    {
        return $this->gender() == self::GENDER_FEMALE;
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
    public function password($password = null, $encrypt = true)
    {
        if ($password != null)
        {
            $this->password = ($encrypt) ? hash_encrypt($password) : $password;
            XDB::execute('UPDATE account SET password = {?} WHERE uid = {?}',
                                                 $this->password, $this->id());
        }
        return XDB::fetchOneCell('SELECT  password FROM  account WHERE  uid = {?}', $this->id());
    }

    /**
    * Original picture
    *
    * @param $original If specified, updates the picture in the database
    */
    public function original(FrankizImage $original = null)
    {
        if ($original != null)
        {
            $this->original = $original;
            XDB::execute('UPDATE account SET original = {?} WHERE uid = {?}',
                                                 $original->id(), $this->id());
        }
        return $this->original;
    }

    /**
    * Current picture
    *
    * @param $photo If specified, updates the picture in the database
    */
    public function photo(FrankizImage $photo = null)
    {
        if ($photo != null)
        {
            $this->photo = $photo;
            XDB::execute('UPDATE account SET photo = {?} WHERE uid = {?}',
                                                 $photo->id(), $this->id());
        }
        return $this->photo;
    }

    public function image()
    {
        global $globals;

        if ((!empty($this->photo)))
            return $this->photo;
        if ((!empty($this->original)))
            return $this->original;

        return new StaticImage(($this->isFemale()) ? $globals->images->woman : $globals->images->man);
    }

    /**
    * Returns the skin name of the User
    *
    * @param $skin name of the skin to associate with the User
    */
    public function skin($skin = null)
    {
        if ($skin != null) {
            $res = XDB::query('SELECT name FROM skins WHERE name = {?}', $skin);

            if ($res->numRows() != 1)
                throw new Exception ("Cette skin n'existe pas et ne peut donc pas être choisie");

            $this->skin = $skin;
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

    /**
    * Returns the hash_rss given to the user
    *
    * @param $hash_rss If specified, update the Hash
    */
    public function hash_rss($hash_rss = null)
    {
        if ($hash_rss != null) {
            $this->hash_rss = $hash_rss;
            XDB::execute('UPDATE account SET hash_rss = {?} WHERE uid = {?}', $this->hash_rss, $this->id());
        }
        return $this->hash_rss;
    }

    public function birthdate(FrankizDateTime $birthdate = null)
    {
        if ($birthdate != null) {
            $this->birthdate = $birthdate;
            XDB::execute('UPDATE account SET birthdate = {?} WHERE uid = {?}', $birthdate->format(), $this->id());
        }
        return $this->birthdate;
    }

    public function cellphone($cellphone = null)
    {
        if ($cellphone != null) {
            $this->cellphone = $cellphone;
            XDB::execute('UPDATE account SET cellphone = {?} WHERE uid = {?}', $cellphone, $this->id());
        }
        return $this->cellphone;
    }

    /**
    * Returns the poly login, an outdated login but the only one for X < 2005
    */
    public function poly($poly = null)
    {
        if ($poly != null) {
            $this->poly = $poly;
            XDB::execute('INSERT INTO poly SET uid = {?}, poly = {?}
                          ON DUPLICATE KEY UPDATE poly = {?}', $this->id(), $poly, $poly);
        }
        return $this->poly;
    }

    /*******************************************************************************
         Studies

    *******************************************************************************/

    public function studies($all = false)
    {
        if ($all) {
            return $this->studies;
        }

        return array_filter($this->studies, function ($s) {
            return ($s->formation()->id() > 0);
        });
    }

    public function addStudy($formation, $year_in, $year_out, $promo, $forlife)
    {
        $formation_id = ($formation instanceof Formation) ? $formation->id() : $formation;
        XDB::execute('INSERT IGNORE INTO  studies
                                     SET  uid = {?}, formation_id = {?},
                                          year_in = {?}, year_out = {?},
                                          promo = {?}, forlife = {?}',
                                        $this->id(), $formation_id,
                                        $year_in, $year_out,
                                        $promo, $forlife);

        if (!(XDB::affectedRows() > 0))
            return false;

        return true;
    }

    /*******************************************************************************
         Rooms

    *******************************************************************************/

    /**
    * Returns the Collection of Rooms of the User
    */
    public function rooms()
    {
        return $this->rooms;
    }

    /**
    * Add a Room to the user
    * @param $r the room to add
    */
    public function addRoom(Room $r)
    {
        XDB::execute('INSERT IGNORE  rooms_users
                                SET  rid = {?}, uid = {?}',
                            $r->id(), $this->id());

        if (!(XDB::affectedRows() > 0))
            return false;

        if (empty($this->rooms))
            $this->rooms = new Collection('Room');

        $this->rooms->add($r);
        return true;
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

    public function perms()
    {
        return $this->perms;
    }

    public function addPerm($perm)
    {
        XDB::execute("UPDATE  account
                         SET  perms = CONCAT_WS(',', perms, {?})
                       WHERE  uid = {?}",
                              $perm, $this->id());
        if (empty($this->perms)) {
            $this->perms = new PlFlagSet();
        }
        $this->perms->addFlag($perm);
    }

    public function removePerm($perm)
    {
        XDB::execute("UPDATE  account
                         SET  perms = REPLACE(perms, {?},'')
                       WHERE  uid = {?}",
                              $perm, $this->id());
        if (empty($this->perms)) {
            $this->perms = new PlFlagSet();
        }
        $this->perms->rmFlag($perm);
    }

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
    * @param $g the group
    * @param $comments if specified, let the function set the comment
    */
    public function comment(Group $g, $comments = null)
    {
        if ($comments !== null) {
            XDB::execute('INSERT INTO  users_comments
                                  SET  uid = {?}, gid = {?}, comment = {?}
              ON DUPLICATE KEY UPDATE  comment = {?}',
                                     $this->id(), $g->id(), $comments, $comments);

            if ($this->comments == null)
                $this->comments = array();

            $this->comments[$g->id()] = $comments;
        }
        return empty($this->comments[$g->id()]) ? false : $this->comments[$g->id()];
    }

    public function castes($mixed = null)
    {
        return $this->castes;
    }

    public function rights(Group $g)
    {
        $rights = array();
        foreach ($this->castes as $c) {
            if ($c->group()->isMe($g))
                array_push($rights, $c->rights());
        }
        return $rights;
    }

    public function hasRights(Group $g, Rights $r)
    {
        foreach ($this->castes as $c) {
            if ($c->group()->isMe($g))
                if ($r->isMe($c->rights()))
                    return true;
        }

        return false;
    }

    public function group()
    {
        return $this->group;
    }

    /*******************************************************************************
         Miscellaneous

    *******************************************************************************/

    // Actually only called by S to get an anonymous user
    public static function getSilentWithValues($login, $values)
    {
        if ($login == 0) {
            // If the anonymous_user is already in session
            if (S::has('anonymous_user'))
                return S::v('anonymous_user');

            $u = new User(0);
            $u->select();
            S::set('anonymous_user', $u);
            return $u;
        }

        throw new Exception('DEPRECATED call to getSilentWithValues()');
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
        $res = XDB::query("SELECT  s.forlife
                             FROM  studies AS s
                            WHERE  s.uid = {?}
                         ORDER BY  s.promo ASC
                            LIMIT  1", $uid);
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
            $res = XDB::query("SELECT  f.domain
                                 FROM  formations AS f
                            LEFT JOIN  studies AS s ON (s.formation_id = f.formation_id)
                                WHERE  s.uid = {?}
                             ORDER BY  s.promo ASC
                                LIMIT  1", $uid);
            if ($res->numRows()) {
                return $res->fetchOneCell();
            }
        }
        return "";
    }

    public function export($bits = null)
    {
        $export = parent::export();
        $export['hruid'] = $this->login();

        if ($bits & self::EXPORT_MICRO) {
            $export['displayName'] = $this->displayName();
            $export['micro'] = $this->image()->src(ImageInterface::SELECT_MICRO);
        }
        if ($bits & self::EXPORT_SMALL) {
            $export['displayName'] = $this->displayName();
            $export['small'] = $this->image()->src(ImageInterface::SELECT_SMALL);
        }

        return $export;
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/

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
