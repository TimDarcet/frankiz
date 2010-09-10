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
    protected $gids = null;

    // Contains the hash sent by mail to recover the password
    protected $hash = null;

    // Contains the iid of the original picture
    protected $original = null;

    // Contains the iid of the current picture
    protected $photo = null;

    /**
     * Constructs the User object
     *
     * @param $login An user login.
     * @param $values List of known user properties.
     * @param $lazy If datas are missing, should the constructor fetch them in the database ?
     */
    public function __construct($login, $values = array(), $lazy = false)
    {
        $this->fillFromArray($values);

        // If the user id was not part of the known values, determines it from
        // the login.
        if (!$this->id()) {
            $this->uid = $this->getLogin($login);
        }

        if (!$lazy) {
	        // Preloads main properties (assumes the loader will lazily get them
	        // from variables already set in the object).
	        $this->loadMainFields();
        }
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

        global $globals;
        $res = XDB::query("SELECT  a.hruid, a.perms, sk.name AS skin, a.state,
                                   a.hash, a.original, a.photo, a.gender,
                                   a.on_platal, a.email_format, a.bestalias,
                                   CONCAT(a.firstname, ' ', a.lastname) AS full_name,
                                   IF(a.nickname = '', a.firstname, a.nickname) AS display_name
                             FROM  account AS a
                        LEFT JOIN  skins AS sk ON (a.skin = sk.skin_id)
                            WHERE  a.uid = {?}", $this->id());
        $this->fillFromArray($res->fetchOneAssoc());
    }

    // Specialization of the fillFromArray method, to implement hacks to enable
    // lazy loading of user's main properties from the session.
    protected function fillFromArray(array $values)
    {
        // We also need to convert the gender (usually named "femme"), and the
        // email format parameter (valued "texte" instead of "text").
        if (isset($values['gender']) && ($values['gender'] == 'man' || $values['gender'] == 'woman')) {
            $values['gender'] = (bool) ($values['gender'] == 'woman');
        }
        parent::fillFromArray($values);
    }

    public static function getBulkUsersWithUIDs(array $UIDs)
    {
        $users = Array();

        if (count($UIDs) > 0)
        {
            $iter = XDB::iterator("SELECT  a.uid, a.hruid, a.perms, sk.name AS skin, a.state,
                                           a.hash, a.original, a.photo, a.gender,
                                           a.on_platal, a.email_format, a.bestalias,
                                           CONCAT(a.firstname, ' ', a.lastname) AS full_name,
                                           IF(a.nickname = '', a.firstname, a.nickname) AS display_name
                                     FROM  account AS a
                                LEFT JOIN  skins AS sk ON (a.skin = sk.skin_id)
                                    WHERE  a.uid IN {?}", $UIDs);
            while ($datas = $iter->next())
                $users[$datas['uid']] = new User($datas['uid'], $datas, true);
        }

        return $users;
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

    /**
    * Returns the original picture iid if their isn't better one
    */
    public function bestImage()
    {
        return ($this->photo != 0) ? $this->photo : $this->original;
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

    public function groups(PlFlagSet $rights)
    {
        // We load all the groups at once,
        // but return only a restriction.
        Group::get($this->gids());
        return Group::get($this->gids($rights));
    }

    // TODO: Add the groups of the Local (factor with AnonymousUser)
    protected function loadGids()
    {
        // Load the directly associated groups from the database
        $iter = XDB::iterator('SELECT  gid, rights
                                 FROM  users_groups
                                WHERE  uid = {?}',
                                $this->id());

        while ($array_group = $iter->next())
                $this->gids[$array_group['gid']] = new PlFlagSet($array_group['rights']);
// TODO
        // Load the undirect groups
//        $rightsInheritances = Rights::get();

//        $rightsGids = array();
//        foreach ($rightsInheritances as $right => $inheritance)
//            $rightsGids[$right] = array();
//
//        foreach ($this->gids as $gid => $rights)
//            foreach($rightsInheritances as $right => $inheritance)
//                if ($rights->hasFlag($right))
//                    $rightsGids[$right][] = $gid;
//
//        foreach($rightsInheritances as $right => $inheritance)
//            if ($inheritance == Rights::ASCENDING)
//                $this->addGids(Group::batchFathersGids($rightsGids[$right], Group::maxDepth), $right);
//            else if ($inheritance == Rights::ASCENDING)
//                $this->addGids(Group::batchChildrenGids($rightsGids[$right], Group::maxDepth), $right);
    }

    protected function addGids($gids, $right)
    {
        foreach ($gids as $gid)
            if (isset($this->gids[$gid]))
                $this->gids[$gid]->addFlag($right);
            else
                $this->gids[$gid] = new PlFlagSet($right);
    }

    protected function gidsFilter($rights = null)
    {
        $results = array();
        foreach ($this->gids as $gid => $flagSet)
            if ($rights === null || $flagSet->hasFlagCombination($rights))
                $results[] = $gid;

        return $results;
    }

    /**
    * Return the gids associated with the user
    * This function is to be used for building queries
    * involving the groups of the user
    *
    * @param $rights restrict the gids returned with the rights
    */
    public function gids($rights = null)
    {
        if ($this->gids === null)
            $this->loadGids();

        return $this->gidsFilter($rights);
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
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
