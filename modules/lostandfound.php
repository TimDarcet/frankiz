<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
 *  http://br.binets.fr/                                                   *
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

class LostandfoundModule extends PLModule
{
    function handlers()
    {
        return array(
            'laf' => $this->make_hook('laf', AUTH_PUBLIC)
            );
    }

    function handler_laf($page, $id = 0) 
    {
	    if (Env::has('trouve') && Env::has('obj'))
	    {
	        if (S::logged()) {
	            XDB::execute("INSERT INTO   laf
                                  SET   uid = {?}, found = NOW(), description = {?}, context = {?}",
                                  S::user()->id(), Env::t('obj'), Env::t('desc'));
                $page->assign('message', 'Pense à supprimer l\'objet une fois rendu à son propriétaire.');
        		require_once 'banana/hooks.inc.php';
        		$body = 'L\'objet ' . Env::t('obj') . ' a été retrouvé';
        		if (Env::t('desc') != '')
        		{
        			$body .= ' dans les circonstances suivantes : ' . Env::t('desc');
        		}
        		$body .= '.\n\n' . S::user()->displayName() . '\n\n\n'
        					. 'Ceci est un message automatique, merci de le signaler sur frankiz une fois l\'objet rendu.';
       	 		send_message('br.pa', 'pong ' . Env::t('obj'), $body);
	        }
	        else {
	            $page->assign('not_logged', 'true');
	        }
	    }
	    
	    if (Env::has('perdu') && Env::has('obj'))
	    {
	    
	        if (S::logged())
	        {
	            XDB::execute("INSERT INTO   laf
                                  SET   uid = {?}, lost = NOW(), description = {?}, context = {?}",
                                  S::user()->id(), Env::t('obj'), Env::t('desc'));
                $page->assign('message', 'Pense à supprimer l\'objet dès que celui-ci est retrouvé.');
                require_once 'banana/hooks.inc.php';
        		$body = 'L\'objet ' . Env::t('obj') . ' a été perdu';
        		if (Env::t('desc') != '')
        		{
        			$body .= ' dans les circonstances suivantes : ' . Env::t('desc');
        		}
        		$body .= '.\n\n' . S::user()->displayName() . '\n\n\n'
        					. 'Ceci est un message automatique, merci de le signaler sur frankiz une fois l\'objet retrouvé.';
       	 		send_message('br.pa', 'ping ' . Env::t('obj'), $body);
	        }
	        else
	        {
	            $page->assign('not_logged', 'true');
	        }
	    }
	    
        if (Env::has('pong'))
        {
            $this->manage_pong($page, $id);
        }
    
        if (Env::has('del_pong'))
        {
            if (S::logged())
	        {
                $res = XDB::query("SELECT * FROM laf WHERE oid = {?}", $id)->fetchAllRow();
                $res = $res[0];
                if(S::user()->id() == $res[1])
                {
                    XDB::execute("UPDATE laf SET lost = NOW() WHERE oid = {?}", $id);
                    $page->assign('message', 'L\'objet a été supprimé de la base.');
                }
                else
                {
                    $page->assign('message', 'Seul celui qui a trouvé l\'objet peut le supprimer de la liste.');
                }
	        }
	        else
	        {
	            $page->assign('no-logged', 'true');
	        }
        }
        
        if (Env::has('ping'))
        {
            if (S::logged()) {
                $this->manage_ping($page, $id);
            }
            else {
                $page->assign('not_logged', 'true');
            }
        }
    
        if (Env::has('del_ping'))
        {
            if (S::logged())
	        {
                $res = XDB::query("SELECT * FROM laf WHERE oid = {?}", $id)->fetchAllRow();
                $res = $res[0];
                if(S::user()->id() == $res[1])
                {
                    XDB::execute("UPDATE laf SET found = NOW() WHERE oid = {?}", $id);
                    $page->assign('message', 'L\'objet a été supprimé de la base.');
                }
                else
                {
                    $page->assign('message', 'Seul celui qui a perdu l\'objet peut le supprimer de la liste.');
                }
	        }
	        else
	        {
	            $page->assign('not_logged', 'true');
	        }
        }
        
        if (Env::has('ping_obj'))
        {
            $res = XDB::query("SELECT * FROM laf WHERE ISNULL(found) 
            				AND description " . XDB::formatWildcards(XDB::WILDCARD_CONTAINS, Env::t('ping_obj')) . "
            				OR context " . XDB::formatWildcards(XDB::WILDCARD_CONTAINS, Env::t('ping_obj')) . "
            				ORDER BY lost DESC LIMIT 30");
            $lost = $res->fetchAllRow();
            $page->assign('query', 'ping');
        }
        else
        {
            $res = XDB::query("SELECT * FROM laf WHERE ISNULL(found) ORDER BY lost DESC LIMIT 30");
            $lost = $res->fetchAllRow();
        }
        
        if (Env::has('pong_obj'))
        {
            $res = XDB::query("SELECT  *
                                 FROM  laf
                                WHERE  ISNULL(lost)
            				      AND  description " . XDB::formatWildcards(XDB::WILDCARD_CONTAINS, Env::t('pong_obj')) . "
            				       OR  context " . XDB::formatWildcards(XDB::WILDCARD_CONTAINS, Env::t('pong_obj')) . "
            				 ORDER BY  found
            			   DESC LIMIT  30");
            $found = $res->fetchAllRow();
            $page->assign('query', 'pong');
        }
        else
        {
            $res = XDB::query("SELECT * FROM laf WHERE ISNULL(lost) ORDER BY found DESC LIMIT 30");
            $found = $res->fetchAllRow();
        }
        
        $page->addCssLink('laf.css');

        $page->assign('uid', s::user()->id());
        $page->assign('title', 'Objets Trouvés');
        $page->assign('lost', $lost);
        $page->assign('found', $found);
        $page->changeTpl('lostandfound/laf.tpl');
    }
    
    function manage_pong($page, $id) {
        if (S::logged()) {
            $res = XDB::query("SELECT uid, description FROM laf WHERE oid = {?}", $id)->fetchOneAssoc();

            $user = new User($res['uid']);
            $user->select(array(User::SELECT_BASE => array()));
            $message = 'Un message a été envoyé à '.$user->displayName().' ('.$user->bestEmail().') pour lui signaler que tu es le propriétaire de cet objet.';
            $page->assign('message', $message);

	        $mail = new FrankizMailer("lostandfound/mail.tpl");
            $mail->assign('object', $res);
            $mail->assign('user', S::user());
            $mail->assign('type', 'pong');
        	$mail->SetFrom(S::user()->bestEmail(), S::user()->displayName());
	        $mail->AddAddress($user->bestEmail(), $user->displayName());
	        $mail->subject('[Frankiz] Objet trouvé');
	        $mail->send(false);
        }
        else {
            $page->assign('not_logged', 'true');
        }
    }
    
    function manage_ping($page, $id) {
        if (S::logged()) {
            $res = XDB::query("SELECT uid, description FROM laf WHERE oid = {?}", $id)->fetchOneAssoc();
            
            $user = new User($res['uid']);
            $user->select(array(User::SELECT_BASE => array()));
            $message = 'Un message a été envoyé à '.$user->displayName().' ('.$user->bestEmail().') pour lui signaler que tu as retrouvé son objet.';
            $page->assign('message', $message);

	        $mail = new FrankizMailer("lostandfound/mail.tpl");
            $mail->assign('object', $res);
            $mail->assign('user', S::user());
            $mail->assign('type', 'ping');
        	$mail->SetFrom(S::user()->bestEmail(), S::user()->displayName());
	        $mail->AddAddress($user->bestEmail(), $user->displayName());
	        $mail->subject('[Frankiz] Objet perdu');
	        $mail->send(false);
        }
        else {
            $page->assign('not_logged', 'true');
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
