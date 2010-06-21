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

require_once 'banana/hooks.inc.php';

class FrankizBanana extends Banana
{
    public function __construct($params = null)
    {
        global $globals;
        Banana::$msgedit_canattach = false;
        Banana::$spool_root = 'data/NNTP';
        Banana::$nntp_host = self::buildURL();
        Banana::$debug_nntp = ($globals->debug & DEBUG_BT);
        Banana::$debug_smarty = ($globals->debug & DEBUG_SMARTY);
        Banana::$feed_active = S::hasAuthToken();

        parent::__construct($params, 'NNTP', 'BananaPage');
        if (@$params['action'] == 'profile') {
            Banana::$action = 'profile';
        }
    }

    public static function buildURL($login = null)
    {
    	//if we want something a little more complex after
    	return 'nntp://frankiz.polytechnique.fr:119/';

    }

    private function fetchProfile()
    {
        // Get user profile from SQL
        $req = XDB::query("SELECT  sig, last_seen,
                                   FIND_IN_SET('threads',flags) AS threads,
                                   FIND_IN_SET('automaj',flags) AS maj,
                                   tree_unread, tree_read
                             FROM  brs_profiles
                            WHERE  uid = {?}", S::user()->id());
        if ($req->numRows()) {
            $infos = $req->fetchOneAssoc();
        } else {
            $infos = array();
        }
        if (empty($infos['tree_unread'])) {
            $infos = array(  'sig'  => S::user()->displayName(),
            		    'last_seen' => '0000-00-00',
                          'threads' => false,
                             'maj'  => false,
                      'tree_unread' => 'o',
                        'tree_read' => 'dg' );
        }
        return $infos;
    }

    public function run()
    {
        global $platal, $globals;

        // Update last unread time
        $infos = $this->fetchProfile();
        S::set('banana_last', strtotime($infos['last_seen']));
    
        if (!is_null($this->params) && isset($this->params['updateall'])) {
            $time = time();
            S::set('banana_last', $time);
        }
        
        if ($infos['maj']) {
            $time = time();
        }

        // Build user profile
        $req = XDB::query("SELECT  forum
                             FROM  brs_subs
                            WHERE  uid={?}", S::user()->id());
        Banana::$profile['headers']['From']         = S::user()->fullName() . ' <' . S::user()->bestEmail() . '>';
        Banana::$profile['signature']               = $infos['sig'];
        Banana::$profile['display'] 				= $infos['threads'];
        Banana::$profile['autoup']                  = $infos['maj'];
        Banana::$profile['lastnews']                = S::v('banana_last');
        Banana::$profile['subscribe']               = $req->fetchColumn();
        Banana::$tree_unread = $infos['tree_unread'];
        Banana::$tree_read = $infos['tree_read'];

        // Update the "unread limit"
        if (!is_null($time)) {
            XDB::execute('UPDATE  brs_profiles
                             SET  last_seen = FROM_UNIXTIME({?})
                           WHERE  uid = {?}',
                          $time, S::user()->id());
            if (XDB::affectedRows() == 0) {
                XDB::execute('INSERT INTO  brs_profiles
				      				  SET  uid = {?}, last_seen = FROM_UNIXTIME({?})',
                             S::user()->id(), $time);
            }
        }

            // Register custom Banana links and tabs
        if (!Banana::$profile['autoup']) {
            Banana::$page->registerAction('<a href="banana/updateall" > 
            							Marquer tous les messages comme lus </a>');
        }
        Banana::$page->registerPage('profile', 'Préférences', null);

        // Run Bananai
        if (Banana::$action == 'profile') {
            Banana::$page->run();
            return $this->action_updateProfile();
        } else {
            return parent::run();
        }
    }

    public function post($dest, $reply, $subject, $body)
    {
        global $globals;
        Banana::$profile['headers']['From']         = S::user()->fullName() .  ' <' . S::user()->bestEmail() . '>';
        return parent::post($dest, $reply, $subject, $body);
    }

    protected function action_saveSubs($groups)
    {
        global $globals;

        Banana::$profile['subscribe'] = array();
        XDB::execute('DELETE FROM  brs_subs WHERE  uid = {?}', S::user()->id());
        if (!count($groups)) {
            return true;
        }

        foreach ($groups as $g) {
            XDB::execute('INSERT INTO  brs_subs 
	    						  SET  forum={?}, uid={?}', $g, S::user()->id());
            Banana::$profile['subscribe'][] = $g;
        }
    }

    protected function action_updateProfile()
    {
        global $globals;
        $page =& Platal::page();

        $colors = glob('data/banana/m2*.gif');
        foreach ($colors as $key=>$path) {
            $path = basename($path, '.gif');
            $colors[$key] = substr($path, 2);
        }
        $page->assign('colors', $colors);

        if (Post::has('action') && Post::v('action') == 'Enregistrer') {
            $flags = new PlFlagSet();
            if (Post::t('bananadisplay') == '1') {
                $flags->addFlag('threads');
            }
            if (Post::t('bananaupdate') == '1') {
                $flags->addFlag('automaj');
            }
            $unread = Post::s('unread');
            $read = Post::s('read');
            if (!in_array($unread, $colors) || !in_array($read, $colors)) {
                $page->trigError('Le choix de type pour l\'arborescence est invalide');
            } else {
                $last_seen = XDB::query('SELECT  last_seen
                                           FROM  brs_profiles
                                          WHERE  uid = {?}', S::user()->id());
                if ($last_seen->numRows() > 0) {
                    $last_seen = $last_seen->fetchOneCell();
                } else {
                    $last_seen = '0000-00-00';
                }
                XDB::execute('UPDATE  brs_profiles 
			       			   	 SET  uid={?}, sig={?}, flags={?}, 
			       	      			  tree_unread={?}, tree_read={?}, last_seen={?}',
                              S::user()->id(), Post::v('bananasig'), $flags,
			     $unread, $read, $last_seen);
                $page->trigSuccess('Ton profil a été mis à jour');
            }
        }

        $infos = $this->fetchProfile();
        $page->assign('nom' ,   S::user()->fullName());
        $page->assign('mail',   S::user()->bestEmail());
        $page->assign('sig',    $infos['sig']);
        $page->assign('disp',   $infos['threads']);
        $page->assign('maj',    $infos['maj']);
        $page->assign('unread', $infos['tree_unread']);
        $page->assign('read',   $infos['tree_read']);
        return null;
    }
}



// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
