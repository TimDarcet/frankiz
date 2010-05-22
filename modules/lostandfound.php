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

class LostandfoundModule extends PLModule
{
    function handlers()
    {
        return array(
            'laf' => $this->make_hook('laf', 0)
            );
    }

    function handler_laf(&$page, $id = 0) 
    {
	    if (Env::has('obj_pong'))
	    {
	        if (S::logged()) {
	            XDB::execute("INSERT INTO   laf
                                  SET   uid = {?}, found = NOW(), description = {?}, context = {?}",
                                  S::user()->id(), Env::t('obj_pong'), Env::t('desc_pong'));
                $page->assign('message', 'Pense à supprimer l\'objet une fois rendu à son propriétaire.');
	        }
	        else {
	            $page->assign('not_logged', 'true');
	        }
	    }
	    
	    if (Env::has('obj_ping'))
	    {
	    
	        if (S::logged())
	        {
	            XDB::execute("INSERT INTO   laf
                                  SET   uid = {?}, lost = NOW(), description = {?}, context = {?}",
                                  S::user()->id(), Env::t('obj_ping'), Env::t('desc_ping'));
                $page->assign('message', 'Pense à supprimer l\'objet dès que celui-ci est retrouvé.');
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
            $res = XDB::query("SELECT * FROM laf WHERE ISNULL(found) AND description " .
                        XDB::formatWildcards(XDB::WILDCARD_CONTAINS, Env::t('ping_obj')) . " ORDER BY lost DESC LIMIT 10");
            $losts = $res->fetchAllRow();
        }
        else
        {
            $res = XDB::query("SELECT * FROM laf WHERE ISNULL(found) ORDER BY lost DESC LIMIT 10");
            $losts = $res->fetchAllRow();
        }
        
        if (Env::has('pong_obj'))
        {
            $res = XDB::query("SELECT * FROM laf WHERE ISNULL(lost) AND description " .
                        XDB::formatWildcards(XDB::WILDCARD_CONTAINS, Env::t('pong_obj')) . " ORDER BY found DESC LIMIT 10");
            $losts = $res->fetchAllRow();
        }
        else
        {
            $res = XDB::query("SELECT * FROM laf WHERE ISNULL(lost) ORDER BY found DESC LIMIT 10");
            $founds = $res->fetchAllRow();
        }
        
        
        $page->addCssLink('laf.css');

        $page->assign('title', 'Objets Trouvés');
        $page->assign('losts', $losts);
        $page->assign('founds', $founds);
        $page->changeTpl('lostandfound/laf.tpl');
    }
    
    function manage_pong(&$page, $id) {
        if (S::logged()) {
            $res = XDB::query("SELECT * FROM laf WHERE oid = {?}", $id)->fetchAllRow();
            $res = $res[0];
            $us = S::user()->displayName();
            $body = 'L\'objet : '.$res[4].' appartient à '.$us.' ('.S::user()->bestEmail().").\nPense à le supprimer de la liste des objets trouvés.\n\nLes webs.";
            
            $user = plUser::getWithUID($res[1]);
            $message = 'Un message a été envoyé à '.$user->displayName().' ('.$user()->bestEmail().') pour lui signaler que tu es le propriétaire de cet objet.';
            $page->assign('message', $message);
        
	        $mymail = new PlMailer();
        	$mymail->setFrom(S::user()->bestEmail());
	        $mymail->addTo($user->bestEmail());
	        $mymail->setSubject('objet trouvé');
    	    $mymail->setTxtBody($body);
	        $mymail->send();
        }
        else {
            $page->assign('not_logged', 'true');
        }
    }
    
function manage_ping(&$page, $id) {
        if (S::logged()) {
            $res = XDB::query("SELECT * FROM laf WHERE oid = {?}", $id)->fetchAllRow();
            $res = $res[0];
            $us = S::user()->displayName();
            $body = 'Ton objet : '.$res[4].' a été retrouvé par '.$us.' ('.S::user()->bestEmail().")\n.Pense à le supprimer de la liste des objets perdus.\n\nLes webs.";
            
            $user = plUser::getWithUID($res[1]);            
            $message = 'Un message a été envoyé à '.$user->displayName().' ('.$user->bestEmail().') pour lui signaler que tu as retrouvé son objet.';
            $page->assign('message', $message);
        
	        $mymail = new PlMailer();
    	    $mymail->setFrom(S::user()->bestEmail());
    	    $mymail->addTo($user->bestEmail());
	        $mymail->setSubject('objet perdu');
	        $mymail->setTxtBody($body);
    	    $mymail->send();
        }
        else {
            $page->assign('not_logged', 'true');
        }
    }
    
    function adder_pong($obj_pong, $desc_pong)
    {
	            
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
