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

define('AUTH_SUID', -1);    // When we want to do SUID
define('AUTH_PUBLIC', 0);   //Anyone
define('AUTH_INTERNE', 1);  //Connecting from inside
define('AUTH_ELEVE', 2);//Connecting from eleve zone (binets, Kserts, wifi ; not pits, ...)
define('AUTH_COOKIE', 5);   //Has a cookie
define('AUTH_MDP', 10);     //Has entered password during session

define('COOKIE_INCOMPLETE', -1);
define('COOKIE_OK', 0);
define('COOKIE_WRONG_HASH', 1);
define('COOKIE_WRONG_UID', -2);

class FrankizSession extends PlSession
{
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
            $cookie = $this->tryCookie();
            //there is a cookie, and it's ok : log the user in
            if($cookie == COOKIE_OK)
            {
                return $this->start(AUTH_COOKIE);
            } else if($cookie == COOKIE_WRONG_HASH || $cookie == COOKIE_WRONG_UID)
            {
                return false;
            }
        }
        return true;
    }

    /** Checks the cookie and set user_id according in auth_by_cookie variable
     */
    private function tryCookie()
    {
        S::kill('cookie_uid'); //Remove previously stored id
        if(!Cookie::has('uid') || !Cookie::has('hash')){
            return COOKIE_INCOMPLETE;
        }
        $res = XDB::query('SELECT   eleve_id, password
                             FROM   account
                            WHERE   user_id = {?} AND perms IN(\'admin\', \'user\')',
                        Cookie::i('uid'));
        if($res->numRows() == 1)
        {
            list($uid, $password) = $res->fetchOneRow();
            require_once 'secure_hash.inc.php';
            $expected_value = hash_encrypt($password);
            if($expected_value == Cookie::v('hash'))
            {
                S::set('cookie_uid', $uid);
                return COOKIE_OK;
            } else {
                return COOKIE_WRONG_HASH;
            }
        }
        return COOKIE_WRONG_UID;
    }

    /** Check that we have at least $level auth
     */
    protected function doAuth($level)
    {
        //If only AUTH_COOKIE is required, and we haven't checked the presence of a cookie, do it now
        if ($level == AUTH_COOKIE && !S::has('cookie_uid'))
        {
            $this->tryCookie();
        }
        //If AUTH_COOKIE is required, and it has succeeded
        if($level == AUTH_COOKIE && S::has('cookie_uid'))
        {
            if(!S::logged())
            {
                S::set('auth', AUTH_COOKIE);
            }
            return S::i('cookie_uid');
        }

        /*If we are here, we want AUTH_MDP
          So we check if the required fields are here */

        // FIXME : lesser checks until new mechanism is ready
        //if(!Post::has('username') || !Post::has('response') || !S::has('challenge'))
        if(!Post::has('username') || !Post::has('password'))
        {
            return null;
        }
        
        /* So we come from an authentication form */
        if (S::has('suid')) {
            $suid = S::v('suid');
            $login = $suid['uid'];
            $redirect = false;
        } else {
            $login = Env::v('username');
            $redirect = false;
        }

        // FIXME : using Post::v('password') until new authentication mechanism is ready
        $uid = $this->checkPassword($login, Post::v('password'), is_numeric($login) ? 'eleve_id' : 'alias');
        if (!is_null($uid) && S::has('suid')) {
            $suid = S::v('uid');
            if ($suid['uid'] == $uid) {
                $uid = S::i('uid');
            } else {
                $uid = null;
            }
        }
        if (!is_null($uid)) {
            S::set('auth', AUTH_MDP);
            S::kill('challenge');
        }
        return $uid;
    }

    /** Check whether a password is valid
     * login_type can be eleve_id, alias (for an email alias), hruid
     */
    private function checkPassword($login, $response, $login_type = 'eleve_id')
    {
        if ($login_type == 'alias') {
            list($forlife, $domain) = explode('@', $login, 2);
            $res = XDB::query('SELECT   a.eleve_id
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

    /** Start a session as user $uid
     */
    protected function startSessionAs($uid, $level)
    {
        /* Session data and required data mismatch */
        if ((!is_null(S::v('user')) && S::i('user') != $uid) || (S::has('uid') && S::i('uid') != $uid))
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

        /* Load main user data */
        $res = XDB::query('SELECT   eleve_id AS uid, name, forename, perms, skin, skin_params
                             FROM   account
                        LEFT JOIN   trombino USING (eleve_id)
                            WHERE   eleve_id = {?}',
                        $uid);
        $sess = $res->fetchOneAssoc();
        /* store perms in $perms, for sess will be merged into $_SESSION, and $perms is a PlFlagSet */
        $perms = $sess['perms'];
        unset($sess['perms']);

        /* Load data into the real session */
        $_SESSION = array_merge($_SESSION, $sess);

        if (S::has('suid')) {
            $suid = S::v('suid');
        }

        $this->makePerms($perms);

        /* Clean temp var 'cookie_uid' */
        S::kill('cookie_uid');
        return true;
    }


    /** Convert $perm into a PlFlagSet
     */
    public function makePerms($perm, $is_admin)
    {
        $flags = new PlFlagSet($perm, ',');
        if ($perm == 'disabled') {
            S::set('perms', $flags);
            return;
        }
        $flags->addFlag(PERMS_USER);
        if ($perm == 'admin') {
            $flags->addFlag(PERMS_ADMIN);
        }
        S::set('perms', $flags);
    }

    /** Token auth, for RSS feeds
     */
    public function tokenAuth($login, $token)
    {
        /* Load main user data */
        $res = XDB::query('SELECT   eleve_id as uid, perms, hruid, name, forename, skin_params
                             FROM   account
                        LEFT JOIN   trombino USING (eleve_id)
                            WHERE   eleve_id = {?} AND hash_rss = {?}',
                        $login, $token);
        if($res->numRows()==1)
        {
            $sess = $res->fetchOneAssoc();
            return new User($sess['hruid'], $sess);
        }
        return null;
    }

    public function loggedLevel()
    {
        return AUTH_COOKIE;
    }

    public function sureLevel()
    {
        return AUTH_MDP;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
