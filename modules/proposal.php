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

/* contains all admin stuff */
class ProposalModule extends PlModule
{
    function handlers()
    {
        return array(
            'proposal/news'             => $this->make_hook('news', AUTH_COOKIE),
            'proposal/activity'         => $this->make_hook('activity', AUTH_COOKIE),
            'proposal/mail'             => $this->make_hook('mail', AUTH_COOKIE),
        );
    }
    

    function handler_news($page)
    {   
        
        $title      = env::t('title', '');
        $content    = env::t('content', '');
        $end        = env::t('end', '');
        $comment    = env::t('comment', '');
        $gid        = env::i('group_news_proposal', '');
        if(env::t('origin_news_proposal') != '') $origin = env::i('origin');
        $priv       = env::has('priv')?1:0;
        if ($end != '')
        {
            $end = substr_replace($end, '-', 6, 0);
            $end = substr_replace($end, '-', 4, 0);
        }
        
        $iid = 1; //To change as soon as Riton works
        
        if (env::has('send'))
        {
            if($title == '' || $content == '' || $end == '' || $gid == '')
            {
                $page->assign('msg', 'Il manque des informations pour créer l\'annonce.');
            }
            else 
            {
                $n = new News(array(
                    'user'      => s::user(), 
                    'gid'       => $gid,
                    'iid'       => $iid,
                    'origin'    => $origin,
                    'title'     => $title,
                    'content'   => $content,
                    'end'       => $end,
                    'comment'   => $comment,
                    'priv'      => $priv,
                    'important' => $important));
                $nv = new NewsValidate($n);
                $v = new Validate(array(
                    'user'  => s::user(),
                    'gid'   => $gid,
                    'item'  => $nv,
                    'type'  => 'news'));
                $v->insert();
                $page->assign('envoye', true);
            }
        }
        
        $page->assign('title_news', $title);
        $page->assign('content', $content);
        $page->assign('end', $end);
        $page->assign('comment', $comment);
        $page->assign('priv', $priv);
        
        $page->assign('title', 'Proposer une annonce');
        $page->addCssLink('validate.css');
        $page->changeTpl('validate/prop.news.tpl');
    }
    

    function handler_activity($page)
    {   
        $title      = env::t('title', '');
        $desc       = env::t('desc', '');
        $date       = env::t('date', '');
        $begin      = env::t('begin', '00:00');
        $end        = env::t('end', '00:00');
        $gid        = env::i('group_activity_proposal', '');
        
        $number     = env::i('number', 1);
        $regular    = env::has('regular')?1:0;
        
        if ($date != '')
        {
            $date = substr_replace($date, '-', 6, 0);
            $date = substr_replace($date, '-', 4, 0);
        }
        
        // Do we want an image ?
        $iid = 1; //To change as soon as Riton works
        
        if (env::has('send'))
        {
            if($title == '' || $date == '' || $begin == '00:00' || $end == '00:00' 
                || $gid == ''  || ($regular && ($number > 5 || $number < 1)))
            {
                $page->assign('msg', 'Il manque des informations pour créer l\'activité.');
            }
            else 
            {
                $av = new ActivityValidate(new Group($gid), $iid, $title, 
                    $desc, $date, $begin, $end, $regular, $number);
                $v = new Validate(array(
                    'user'  => s::user(),
                    'gid'   => $gid,
                    'item'  => $av,
                    'type'  => 'activity'));
                $v->insert();
                $page->assign('envoye', true);
            }
        }
        
        $page->assign('title_activity', $title);
        $page->assign('desc', $desc);
        $page->assign('date', $date);
        $page->assign('regular', $regular);
        $page->assign('number', $number);
        $page->assign('begin', $begin);
        $page->assign('end', $end);
        
        $page->assign('title', 'Proposer une activité');
        $page->addCssLink('validate.css');
        $page->changeTpl('validate/prop.activity.tpl');
    }
    
    function handler_mail($page)
    {
        $subject    = Env::t('subject', '');
        $body       = Env::t('body', '');
        $no_wiki    = Env::has('no_wiki')?1:0;
        
        if (Env::has('send'))
        {
            if (Env::t('group_mail_proposal') == '')
                $page->assign('msg', 'Tu n\'as pas donné de destinataire.');
            elseif ($body == '' || $subject == '')
                $page->assign('msg', 'Ton mail est incomplet : il manque le titre ou le message.');
            else
            {
                $nv = new MailValidate(
                    new Group(Env::t('group_mail_proposal')), 
                    $subject, 
                    $body, 
                    $no_wiki);
                $el = new Validate(array(
                    'item'  =>$nv, 
                    'gid'   =>Env::t('group_mail_proposal'), 
                    'user'  =>s::user(), 
                    'type'  =>'mail'));
                $el->insert();
                $page->assign('envoye', 1);
            }
        }
        
        $page->assign('subject', $subject);
        $page->assign('body', $body);
        $page->assign('nowiki', $no_wiki);
        
        $page->assign('title', 'Envoi des mails');
        $page->addCssLink('validate.css');
        $page->changeTpl('validate/prop.mail.tpl');
    }
}    