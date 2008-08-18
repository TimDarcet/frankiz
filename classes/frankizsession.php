<?php
/*
	Copyright (C) 2008 Binet Réseau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

define('AUTH_PUBLIC', 0);//Anyone
define('AUTH_INTERNE', 1);//Connecting from inside
/*
define('AUTH_ELEVE', 2);//Connecting from eleve zone (binets, Kserts, wifi ; not pits, ...)
*/
define('AUTH_COOKIE', 5);//Has a cookie
define('AUTH_MDP', 10);//Has entered password during session

define('COOKIE_INCOMPLETE', -1);
define('COOKIE_OK', 0);
define('COOKIE_WRONG_HASH', 1);
define('COOKIE_WRONG_UID', -2);
class FrankizSession extends PlSession
{
    public function __construct()
    {
        parent::__construct();
        if(S::i('auth') < AUTH_INTERNE && est_interne()){
            S::set('auth', AUTH_INTERNE);
        }
    }

	//Tells if we have enough information to determine the current user
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

	/* checks the cookie and set user_id according in auth_by_cookie variable */

	private function tryCookie()
	{
		S::kill('cookie_uid'); //Remove previously stored id
		if(!Cookie::has('uid') || !Cookie::has('hash')){
			return COOKIE_INCOMPLETE;
		}
		$res = XDB::query('SELECT eleve_id, hash_cookie FROM compte_frankiz WHERE user_id = {?}', Cookie::i('uid'));
		if($res->numRows() == 1)
		{
			list($uid, $hash_cookie) = $res->fetchOneRow();
			if($hash_cookie == Cookie::v('hash'))
			{
				S::set('cookie_uid', $uid);
				return COOKIE_OK;
			} else {
				return COOKIE_WRONG_HASH;
			}
		}
		return COOKIE_WRONG_UID;
	}

    protected function doAuth($level)
    {
    	//If only AUTH_COOKIE is required, and we haven't checked the presence of a cookie, do it noz
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
        
		if(!Post::has('login') || !Post::has('password') || !S::has('challenge'))
		{
			return null;
		}
        
		/* So we come from an authentication form */
		$uid = $this->checkPassword(Post::v('login'), Post::v('password'));
		if(!is_null($uid))
		{
			S::set('auth', AUTH_MDP);
			S::kill('challenge');
		}
		return $uid;
	}

	private function checkPassword($login, $password)
	{
        $login=explode('.', $login, 2);

		$res = XDB::query('SELECT eleve_id, passwd FROM compte_frankiz LEFT JOIN eleves USING (eleve_id) WHERE login={?} AND promo={?}', $login[0], $login[1]);
		if(list($uid, $db_password) = $res->fetchOneRow())
		{
			if(!crypt($password, $db_password) == $db_password)
			{
				return null;
			}
			return $uid;
		}
		return null;
	}

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
		if ($level == -1)
		{
			S::set('auth', AUTH_COOKIE);
		}

		/* Load main user data */
		$res = XDB::query('SELECT eleve_id as uid, nom, prenom, perms, skin FROM compte_frankiz LEFT JOIN eleves USING (eleve_id) WHERE eleve_id = {?}', $uid);
		$sess = $res->fetchOneAssoc();
        /* store perms in $perms, for sess will be merged into $_SESSION, and $perms is a PlFlagSet */
		$perms = $sess['perms'];
		unset($sess['perms']);

		/* Load data into the real session */
		$_SESSION = array_merge($_SESSION, $sess);
		
		$this->makePerms($perms);
		
		/* Clean temp var 'cookie_uid' */
		S::kill('cookie_uid');
		return true;
	}

    public function makePerms($perm)
    {
        $flags = new PlFlagSet($perm, ',');
        $flags->addFlag(PERMS_USER);
        S::set('perms', $flags);
    }

    public function tokenAuth($login, $token)
    {
		/* Load main user data */
		$res = XDB::query('SELECT eleve_id as uid, nom, prenom, perms FROM compte_frankiz WHERE eleve_id = {?} AND token = {?}', $login, $token);
		if($res->numRows()==1)
		{
			$sess = $res->fetchOneAssoc();
			/* if no current session */
			if(!S::has('uid'))
			{
				$_SESSION = $sess;
				$this->makePerms($sess['perms']);
				return S::i('uid');
			} else if (S::i('uid') == $sess['uid']){
				return S::i('uid');
			}
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

?>
