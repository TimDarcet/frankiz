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

class UserSchema extends Schema
{
    public function className() {
        return 'User';
    }

    public function table() {
        return 'account';
    }

    public function id() {
        return 'uid';
    }

    public function tableAs() {
        return 'a';
    }

    public function scalars() {
        return array('hruid', 'perms', 'state', 'group',
                     'hash', 'hash_rss', 'original', 'photo', 'gender',
                     'email_format', 'email', 'skin', 'birthdate',
                     'firstname', 'lastname', 'nickname', 'comment', 'poly');
    }

    public function objects() {
        return array('perms' => 'PlFlagSet',
                     'group' => 'Group',
                  'original' => 'FrankizImage',
                     'photo' => 'FrankizImage',
                 'birthdate' => 'FrankizDateTime',
                 'cellphone' => 'Phone');
    }

    public function collections() {
        return array('rooms' => 'Room',
                    'castes' => 'Caste');
    }
}

class UserSelect extends Select
{
    protected static $natives = array('hruid', 'perms', 'state', 'group',
                               'hash', 'hash_rss', 'original', 'photo', 'gender',
                               'email_format', 'email', 'skin', 'cellphone',
                               'firstname', 'lastname', 'nickname', 'birthdate', 'comment');

    public function className() {
        return 'User';
    }

    protected function handlers() {
        return array('main' => array_merge(self::$natives, array('poly')),
                    'rooms' => array('rooms'),
                   'castes' => array('castes'),
              'minimodules' => array('minimodules'),
                  'studies' => array('studies'),
                 'comments' => array('comments'),
           'defaultfilters' => array('defaultfilters'));
    }

    protected function handler_main(Collection $users, array $fields) {
        $joins = array();
        $cols  = array();

        $loc_fields = array_intersect($fields, self::$natives);
        if (!empty($loc_fields)) {
            $cols['a'] = $loc_fields;
        }

        if (in_array('poly', $fields)) {
            $cols['p']  = array('poly');
            $joins['p'] = PlSqlJoin::left('poly', '$ME.uid = a.uid');
        }

        if (in_array('default_filters', $fields)) {
            $cols['udf']  = array('default_filters');
            $joins['udf'] = PlSqlJoin::left('users_defaultfilters', '$ME.uid = a.uid');
        }

        $this->helper_main($users, $cols, $joins);
    }

    protected function handler_minimodules(Collection $users, $fields) {
        $_users = array();
        foreach ($users as $u) {
            $_users[$u->id()] = FrankizMiniModule::emptyLayout();
        }

        $iter = XDB::iterRow('SELECT  uid AS id, name, col
                                FROM  users_minimodules
                               WHERE  uid IN {?}
                            ORDER BY  col, row', $users->ids());

        while (list($uid, $name, $col) = $iter->next()) {
            array_push($_users[$uid][$col], $name);
        }

        foreach ($users as $u) {
            $u->fillFromArray(array('minimodules' => $_users[$u->id()]));
        }
    }

    protected function handler_studies(Collection $users, $fields) {
        $_users = array();
        foreach ($users as $u) {
            $_users[$u->id()] = array();
        }

        $iter = XDB::iterator('SELECT  uid, formation_id, year_in, year_out, promo, forlife
                                 FROM  studies
                                WHERE  uid IN {?}', $users->ids());

        $formations = new Collection('Formation');
        while ($datas = $iter->next()) {
            $formation_id = $datas['formation_id'];
            $datas['formation'] = $formations->addget($formation_id); unset($datas['formation_id']);
            $_users[$datas['uid']][$formation_id] = new Study($datas);
        }

        foreach ($users as $u) {
            $u->fillFromArray(array('studies' => $_users[$u->id()]));
        }

        if (!empty($formations) && !empty($this->subs['studies'])) {
            $formations->select($this->subs['studies']);
        }
    }

    protected function handler_defaultfilters(Collection $users, $fields) {
        foreach ($users as $u) {
            $u->fillFromArray(array('defaultfilters' => new Collection('Group')));
        }

        $iter = XDB::iterRow('SELECT  uid AS id, defaultfilters
                                FROM  users_defaultfilters
                               WHERE  uid IN {?}', $users->ids());

        $groups = new Collection('Group');
        while (list($uid, $defaultfilters) = $iter->next()) {
            $defaultfilters = json_decode($defaultfilters);
            $defaultgroups = new Collection('Group');
            $defaultgroups->add($defaultfilters);
            $groups->safeMerge($defaultgroups);
            $users->get($uid)->fillFromArray(array('defaultfilters' => $defaultgroups));
        }

        if (!empty($groups) && !empty($this->subs['defaultfilters'])) {
            $groups->select($this->subs['defaultfilters']);
        }
    }

    protected function handler_comments(Collection $users, $fields) {
        $_users = array();
        foreach ($users as $u) {
            $_users[$u->id()] = array();
        }

        $iter = XDB::iterRow('SELECT  uid, gid, comment
                                FROM  users_comments
                               WHERE  uid IN {?}',  $users->ids());

        while (list($uid, $gid, $comment) = $iter->next()) {
            $_users[$uid][$gid] = $comment;
        }

        foreach ($users as $u) {
            $u->fillFromArray(array('comments' => $_users[$u->id()]));
        }
    }

    protected function handler_rooms(Collection $users, $fields) {
        $this->helper_collection($users, array('id' => 'rid',
                                            'table' => 'rooms_users',
                                            'field' => 'rooms'));
    }

    protected function handler_castes(Collection $users, $fields) {
        $this->helper_collection($users, array('id' => 'cid',
                                            'table' => 'castes_users',
                                            'field' => 'castes'));
    }

    public static function base() {
        return new UserSelect(self::$natives);
    }

    public static function minimodules() {
        return new UserSelect(array('minimodules'));
    }

    public static function castes() {
        return new UserSelect(array('castes'), array('castes' => CasteSelect::group()));
    }

    public static function login() {
        $cb = function($users) {
            $web = Group::from('webmasters');

            foreach ($users as $u) {
                if ($u->hasRights($web, Rights::member())) {
                    $u->perms()->addFlag('web');
                }
            }
        };

        return new UserSelect(array_merge(self::$natives,
                                          array('rooms', 'minimodules', 'castes', 'poly', 'comments', 'defaultfilters')),
                              array('castes' => CasteSelect::group(),
                            'defaultfilters' => GroupSelect::base()),
                              $cb);
    }

    public static function tol() {
        return new UserSelect(array_merge(self::$natives, array('rooms', 'castes', 'comments', 'studies')),
                              array('castes' => CasteSelect::group(),
                                     'rooms' => RoomSelect::all(),
                                   'studies' => Formation::SELECT_BASE));
    }

    public static function minitol() {
        return new UserSelect(array_merge(self::$natives, array('rooms', 'castes', 'comments', 'studies')),
                              array('castes' => CasteSelect::group(),
                                     'rooms' => RoomSelect::all(),
                                   'studies' => Formation::SELECT_BASE));
    }

    public static function birthday() {
        return new UserSelect(array('hruid', 'original', 'photo', 'gender',
                                    'firstname', 'lastname', 'nickname', 'studies'),
                              array('studies' => Formation::SELECT_BASE));
    }
}

class User extends Meta
{
    /*******************************************************************************
         Constants

    *******************************************************************************/

    const GENDER_FEMALE = 'woman';
    const GENDER_MALE   = 'man';

    const FORMAT_HTML = "html";
    const FORMAT_TEXT = "text";

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

    protected $bestEmail = null;

    // Names
    protected $firstname = null;
    protected $lastname  = null;
    protected $nickname  = null;

    // Gender
    protected $gender = null;  // Acceptable values are GENDER_MALE and GENDER_FEMALE

    // Permissions
    protected $perms = null;

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
    protected $comment = null;

    // Poly
    protected $poly = null;

    // Default filters
    protected $defaultfilters = null;

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

    /**
    * Tries to returns an email adresse of the user even if
    * the emil field is empty.
    *
    * /!\ To set the 'email' field, use the email() setter
    *
    */
    public function bestEmail()
    {
        if ($this->email == '') {
            if (empty($this->bestEmail)) {
                $res = XDB::fetchOneRow("SELECT  s.forlife, f.domain
                                           FROM  studies AS s
                                     INNER JOIN  formations AS f
                                             ON  s.formation_id = f.formation_id
                                          WHERE  s.uid = {?} ", $this->id);
                $this->bestEmail = $res[0] . '@' . $res[1];
            }
            return $this->bestEmail;
        }
        return $this->email;
    }

    public function displayName()
    {
        return (empty($this->nickname)) ? $this->firstname : $this->nickname;
    }

    public function fullName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function isFemale()
    {
        return $this->gender() == self::GENDER_FEMALE;
    }

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
        if ($password != null) {
            $this->password = ($encrypt) ? hash_encrypt($password) : $password;
            XDB::execute('UPDATE account SET password = {?} WHERE uid = {?}',
                                                 $this->password, $this->id());
        }
        return XDB::fetchOneCell('SELECT  password FROM  account WHERE  uid = {?}', $this->id());
    }

    /**
    * Original picture
    * /!\ The field must have been fetched even to set a new picture !
    *
    * @param $original If specified, updates the picture in the database
    */
    public function original(FrankizImage $original = null)
    {
        if ($original != null) {
            if ($this->original) {
                $this->original->delete();
            }
            $this->original = $original;
            XDB::execute('UPDATE account SET original = {?} WHERE uid = {?}',
                                             $original->id(), $this->id());
        }
        return $this->original;
    }

    /**
    * Current picture
    * /!\ The field must have been fetched even to set a new picture !
    *
    * @param $photo If specified, updates the picture in the database
    */
    public function photo(FrankizImage $photo = null)
    {
        if ($photo != null) {
            if ($this->photo) {
                $this->photo->delete();
            }
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

    /**
    * Getter & Setter for the groups to be displayed by default in the groups_pickers
    * /!\ When you set a Collection, the groups must have been fetched.
    */
    public function defaultFilters(Collection $groups = null)
    {
        if ($groups !== null) {
            if ($groups === false) {
                XDB::execute('DELETE FROM  users_defaultfilters
                                    WHERE  uid = {?}', $this->id());
            } else {
                $export = json_encode($groups->ids());
                XDB::execute('INSERT INTO  users_defaultfilters
                                      SET  uid = {?}, defaultfilters = {?}
                  ON DUPLICATE KEY UPDATE  defaultfilters = {?}',
                                           $this->id(), $export, $export);
            }
            $this->defaultfilters = $groups;
        }

        return $this->defaultfilters;
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

        if (!(XDB::affectedRows() > 0)) {
            return false;
        }

        $this->select(UserSelect::minimodules());
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
        if (is_null($this->perms)) {
            throw new DataNotFetchedException('The perms have not been fetched');
        }
        if (empty($perms)) {
            return true;
        }
        return $this->perms->hasFlagCombination($perms);
    }

    public function isWeb()
    {
        return ($this->perms()->hasFlag('web')) || ($this->perms()->hasFlag('admin'));
    }

    public function isAdmin()
    {
        return ($this->perms()->hasFlag('admin'));
    }

    /*******************************************************************************
         Groups

    *******************************************************************************/

    /**
    * Returns or updates the comment binding an user to a group.
    * @param $g the group
    * @param $comments if specified, let the function set the comment
    */
    public function comments(Group $g, $comments = null)
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

    public function castes(Rights $rights = null)
    {
        if ($rights == null) {
            return $this->castes;
        }
        return $this->castes->filter('rights', $rights);
    }

    /*
     * Returns only castes of type 'restricted' & 'everybody'
     */
    public function targetCastes()
    {
        $target_castes = new Collection();
        $target_castes->merge($this->castes(Rights::restricted()));
        $target_castes->merge($this->castes(Rights::everybody()));
        return $target_castes;
    }

    public function rights(Group $g, $string = false)
    {
        $rights = array();
        foreach ($this->castes as $c) {
            if ($c->group()->isMe($g)) {
                if ($string) {
                    array_push($rights, (string) $c->rights());
                } else {
                    array_push($rights, $c->rights());
                }
            }
        }
        return $rights;
    }

    public function hasRights(Group $g, Rights $r = null)
    {
        foreach ($this->castes as $c) {
            if ($c->group()->isMe($g)) {
                if ($r === null) {
                    return true;
                }
                if ($r->isMe($c->rights())) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
    * Returns true if the user is allowed to see the content of the caste
    * taking into account the level of AUTH
    * @param $caste the rights of the caste must be already fetched
    */
    public function canSee(Caste $caste)
    {
        // If we are inside the platal & the caste is of type everybody
        if (S::i('auth') >= AUTH_INTERNAL && $caste->rights()->isMe(Rights::everybody())) {
            return true;
        }

        // If we are here, it means we are outside or that the caste is restricted
        // In either case, in order to see the content, the user must be part of the caste
        if (S::user()->castes()->get($image->caste()) != false) {
            return true;
        }

        return false;
    }

    /*******************************************************************************
         Miscellaneous

    *******************************************************************************/

    // Actually only called by S to get an anonymous user
    public static function getSilentWithValues($login, $values)
    {
        global $globals;

        if ($login == 0) {
            // If the anonymous_user is already in session
            if (S::has('anonymous_user'))
                return S::v('anonymous_user');

            $uid = (IP::is_internal()) ? $globals->anonymous->internal : $globals->anonymous->external;
            $u = new User($uid);
            $u->select(UserSelect::login());
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
        $export['displayName'] = $this->displayName();
        $export['fullName'] = $this->fullName();

        if ($bits & self::EXPORT_MICRO) {
            $export['micro'] = $this->image()->src('micro');
        }
        if ($bits & self::EXPORT_SMALL) {
            $export['small'] = $this->image()->src('small');
        }

        return $export;
    }

    /*******************************************************************************
         Data fetcher

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
        $group->insert(null, 'user');
        $group->ns(Group::NS_USER);
        $group->name('user_' . $this->id());
        $group->leavable(false);
        $group->visible(false);
        $group->label('Groupe personnel de ' . $this->fullName());

        XDB::execute('UPDATE account SET `group` = {?} WHERE uid = {?}', $group->id(), $this->id());

        $group->caste(Rights::admin())->addUser($this);
        $group->caste(Rights::restricted())->addUser($this);

        $this->group = $group;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
