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

// Auth levels
// Those are defined in core/classes/plsession.php :
// AUTH_SUID = -1
// AUTH_PUBLIC = 0
// AUTH_COOKIE = 5
// AUTH_MDP = 10
define('AUTH_INTERNE', 1);  //Connecting from inside
define('AUTH_ELEVE', 2);//Connecting from eleve zone (binets, Kserts, wifi ; not pits, ...)

class FrankizSession extends PlSession
{
    const COOKIE_INCOMPLETE = -1;
    const COOKIE_SUCCESS = 0;
    const COOKIE_WRONG_HASH = 1;
    const COOKIE_WRONG_UID = -2;

    public function __construct()
    {
        parent::__construct();

        // Set auth as AUTH_INTERNE when inside and had weaker auth
        if(S::i('auth') < AUTH_INTERNE && ip_internal()){
            S::set('auth', AUTH_INTERNE);
        }
        if(S::i('auth') < AUTH_ELEVE && ip_eleve()){
            S::set('auth', AUTH_ELEVE);
        }
    }

    /** Tells if we have enough information to determine the current user
     */
    public function startAvailableAuth()
    {
        //User not logged in, check if there is a cookie
        if(!S::logged())
        {
            switch ($this->tryCookie()) {
            case self::COOKIE_SUCCESS:
                //there is a cookie, and it's ok : log the user in
                if (!$this->start(AUTH_COOKIE)) {
                    return false;
                }
                break;
            case self::COOKIE_WRONG_HASH:
            case self::COOKIE_WRONG_UID:
                return false;
            }
        }
        return true;
    }

    /** Checks the cookie and set user_id according in cookie_uid variable
     */
    private function tryCookie()
    {
        S::kill('cookie_uid'); //Remove previously stored id
        if(!Cookie::has('uid') || !Cookie::has('hash')){
            return self::COOKIE_INCOMPLETE;
        }
        $res = XDB::query('SELECT   eleve_id, password
                             FROM   account
                            WHERE   eleve_id = {?} AND state = \'active\'',
                        Cookie::i('uid'));
        if($res->numRows() == 1)
        {
            list($uid, $password) = $res->fetchOneRow();
            if(sha1($password) == Cookie::v('hash'))
            {
                S::set('cookie_uid', $uid);
                return self::COOKIE_SUCCESS;
            } else {
                return self::COOKIE_WRONG_HASH;
            }
        }
        return self::COOKIE_WRONG_UID;
    }

    /** Check that we have at least $level auth
     */
    protected function doAuth($level)
    {
        //If only AUTH_COOKIE is required, and we haven't checked the presence of a cookie, do it now
        if ($level == AUTH_COOKIE && !S::has('cookie_uid')) {
            $this->tryCookie();
        }
        //If AUTH_COOKIE is required, and it has succeeded
        if($level == AUTH_COOKIE && S::has('cookie_uid')) {
            if(!S::logged()) {
                S::set('auth', AUTH_COOKIE);
            }
            return User::getSilentWithUID(S::i('cookie_uid'));
        }

        /*If we are here, we want AUTH_MDP
          So we check if the required fields are here */

        // FIXME : lesser checks until new mechanism is ready
        //if(!Post::has('username') || !Post::has('response') || !S::has('challenge'))
        if(!Post::has('username') || !Post::has('password')) {
            return null;
        }
        
        /* So we come from an authentication form */
        if (S::suid()) {
            $login = S::suid('uid');
            $redirect = false;
        } else {
            $login = Env::v('username');
            $redirect = false;
            if (Post::has('domain')) {
                Cookie::set('domain', Post::v('domain'), 300);
            }
        }

        // FIXME : using Post::v('password') until new authentication mechanism is ready
        $uid = $this->checkPassword($login, Post::v('password'), is_numeric($login) ? 'eleve_id' : 'alias');
        if (!is_null($uid) && S::suid()) {
            if (S::suid('uid') == $uid) {
                $uid = S::i('uid');
            } else {
                $uid = null;
            }
        }
        if (!is_null($uid)) {
            S::set('auth', AUTH_MDP);
            S::kill('challenge');
        }
        return User::getSilentWithUID($uid);
    }

    /** Check whether a password is valid
     * login_type can be eleve_id, alias (for an email alias), hruid
     */
    private function checkPassword($login, $response, $login_type = 'eleve_id')
    {
        if ($login_type == 'alias') {
            list($forlife, $domain) = explode('@', $login, 2);
            $res = XDB::query('SELECT   s.eleve_id
                                 FROM   studies AS s
                            LEFT JOIN   formations AS f on (f.formation_id = s.formation_id AND f.domain = {?})
                                WHERE   s.forlife = {?}',
                              $domain, $forlife);
            $login = $res->fetchOneCell();
            $login_type = 'eleve_id';
        }

        $res = XDB::query('SELECT   eleve_id, password, hruid
                             FROM   account
                            WHERE   '.$login_type.' = {?}',
                        $login);
        // FIXME : temporary, simple password check
        if(list($uid, $password, $hruid) = $res->fetchOneRow()) {
            //require_once 'secure_hash.inc.php';
            //$expected_response = hash_encrypt("$hruid:$password:".S::v('challenge'));
            //if ($response != $expected_response){
            if ($password != $response) {
                if (!S::logged()) {
                    Platal::page()->trigError('Mot de passe ou nom d\'utilisateur invalide');
                } else {
                    Platal::page()->trigError('Mot de passe invalide');
                }
                return null;
            }
            return $uid;
        }
        Platal::page()->trigError('Mot de passe ou nom d\'utilisateur invalide');
        return null;
    }

    /** Start a session as user $user
     */
    protected function startSessionAs($user, $level)
    {
        /* Session data and required data mismatch */
        if ((!is_null(S::v('user')) && S::v('user')->id() != $user->id()) || (S::has('uid') && S::i('uid') != $user->id()))
        {
            return false;
        } else if (S::has('uid')) {
            return true;
        }
        /* If we want to do a SUID */
        if ($level == AUTH_SUID)
        {
            S::set('auth', AUTH_MDP);
        }

        S::set('user', $user);
        S::set('uid', $user->id());
        S::set('skin', $user->skin());

        if (!S::suid()) {
            Cookie::set('uid', $user->id(), 300);
            if (S::i('cookie_uid') == $user->id() || Post::v('remember', 'false') == 'on') {
                $this->setAccessCookie(false);
            } else {
                $this->killAccessCookie();
            }
        }

        // Set session perms from User perms
        $this->makePerms($user->perms, S::b('is_admin'));

        /* Clean temp var 'cookie_uid' */
        S::kill('cookie_uid');

        return true;
    }

    /** Token auth, for RSS feeds
     */
    public function tokenAuth($login, $token)
    {
        /* Load main user data */
        $res = XDB::query('SELECT   eleve_id as uid, perms, hruid, name, forename, skin_params
                             FROM   account
                        LEFT JOIN   trombino USING (eleve_id)
                            WHERE   eleve_id = {?} AND hash_rss = {?} and state = \'active\'',
                        $login, $token);
        if($res->numRows()==1)
        {
            $sess = $res->fetchOneAssoc();
            return new User($sess['hruid'], $sess);
        }
        return null;
    }

    /** Old function, still needed by core, but should disappear quickly
     */
    protected function makePerms($perm, $is_admin)
    {
        S::set('perms', User::makePerms($perm, $is_admin));
    }

    public function loggedLevel()
    {
        return AUTH_COOKIE;
    }

    public function sureLevel()
    {
        return AUTH_MDP;
    }

    public function setAccessCookie($replace = false) {
        if (S::suid() || ($replace && !Cookie::blank('hash'))) {
            return;
        }
        // FIXME : should switch to true instead of false for HTTPS safety
        Cookie::set('hash', sha1(S::user()->password()), 300, false);
    }

    public function killAccessCookie() {
        Cookie::kill('hash');
    }

    public function killLoginFormCookies() {
        Cookie::kill('uid');
        Cookie::kill('domain');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
