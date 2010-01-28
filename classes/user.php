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
    /* List of available fields, with examples
     * hruid        prenom.nom.X / prenom.nom.SUPOP / ...
     * user_id      42666
     * forlife      main for-life address
     * bestalias    preferred email address
     * display_name Pseudo
     * full_name    Prenom Nom
     * gender       GENDER_MALE | GENDER_FEMALE
     * email_format FORMAT_HTML | FORMAT_TEXT
     * perm_flags   Flag combination describing user perms
     * state        Status : active | pending | disabled
     * on_platal    resides on platal
     */

    protected $on_platal = null;

    protected $state = null;

    protected $skin = null;
    protected $nav_layout = null;

    protected $main_promo = null;

    // Implementation of the login to uid method.
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
            && $this->main_promo !== null) {
            return;
        }

        global $globals;
        $res = XDB::query("SELECT   a.hruid, a.perms, sk.name AS skin, a.state,
                                    CONCAT(a.firstname, ' ', a.lastname) AS full_name,
                                    a.gender, a.on_platal, a.email_format,
                                    IF(a.nickname = '', a.firstname, a.nickname) AS display_name,
                                    CONCAT(s.forlife, '@', f.domain) AS bestalias,
                                    CONCAT(f.abbrev, s.promo) AS main_promo,
                                    a.nav_layout AS nav_layout
                             FROM   account AS a
                        LEFT JOIN   formations AS f ON (f.formation_id = a.main_formation)
                        LEFT JOIN   studies AS s ON (s.formation_id = a.main_formation AND s.uid = a.uid)
                        LEFT JOIN   skins AS sk ON (a.skin = sk.skin_id)
                            WHERE   a.uid = {?}", $this->user_id);
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

    // Return the password of the user
    public function password()
    {
        return XDB::fetchOneCell('SELECT  a.password
                                    FROM  account AS a
                                   WHERE  a.uid = {?}', $this->id());
    }

    public function skin()
    {
        return $this->skin;
    }

    public function nav_layout()
    {
        return $this->nav_layout;
    }

    public function promo()
    {
        return $this->main_promo;
    }
    
    public function clusters()
    {
        $clusters = array();
        $res = XDB::query('SELECT cid
                             FROM users_clusters
                            WHERE uid = {?}',
                            $this->id());
        return $res->fetchColumn();
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
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
