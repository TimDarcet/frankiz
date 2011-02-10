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

class ProposalModule extends PlModule
{
    function handlers()
    {
        return array(
            'proposal/news'             => $this->make_hook('news',          AUTH_COOKIE),
            'proposal/activity'         => $this->make_hook('activity',      AUTH_COOKIE),
            'proposal/activity/ajax'    => $this->make_hook('activity_ajax', AUTH_COOKIE),
            'proposal/mail'             => $this->make_hook('mail',          AUTH_COOKIE),
            'proposal/qdj'              => $this->make_hook('qdj',           AUTH_COOKIE),
            'proposal/survey'           => $this->make_hook('survey',        AUTH_COOKIE),
        );
    }

    static function target_picker_to_caste_group($id) {
        $target_rights = Rights::restricted();
        if (Env::has('target_everybody_' . $id)) {
            $target_rights = Rights::everybody();
        }
        if (S::user()->group()->id() == Env::i('target_group_' . $id)) {
            $target_rights = Rights::restricted();
        }
        $target_group  = new Group(Env::i('target_group_' . $id));
        $target_filter = new CasteFilter(new PFC_And(new CFC_Group($target_group),
                                                     new CFC_Rights($target_rights)));
        return array($target_filter->get(true), $target_group);
    }

    function handler_news($page)
    {
        if (Env::has('send')) {
            try {
                $required_fields = array('origin_news_proposal', 'target_group_news',
                                        'title', 'news_content', 'begin', 'end');
                foreach ($required_fields as $field) {
                    if (Env::v($field, '') == '') {
                        throw new Exception("Missing field ($field)");
                    }
                }

                // Origin & Target
                if (Env::t('origin_news_proposal') == 'false') {
                    $origin = false;
                } else {
                    $origin = new Group(Env::i('origin_news_proposal'));
                }
                list($target, $target_group) = self::target_picker_to_caste_group('news');

                // Content
                $title   = Env::t('title');
                $image   = (Env::has('image')) ? new FrankizImage(Env::i('image')) : false;
                $content = Env::t('news_content');

                // Dates
                $begin = new FrankizDateTime(Env::t('begin'));
                $end   = new FrankizDateTime(Env::t('end'));

                // Meta data
                $comment = Env::t('comment', '');


                // Check credentials for origin
                if (!$origin && !S::user()->hasRights($origin, Rights::admin())) {
                    throw new Exception("Invalid credentials for origin Group");
                }

                // TODO: directly insert if having the rights
                // TODO: build objects
                $nv = new NewsValidate(array(
                    'writer'    => S::user(),
                    'target'    => $target,
                    'image'     => $image,
                    'origin'    => $origin,
                    'title'     => $title,
                    'content'   => $content,
                    'begin'     => $begin,
                    'end'       => $end,
                    'comment'   => $comment));
                $v = new Validate(array(
                    'writer'  => S::user(),
                    'group'   => $target_group,
                    'item'    => $nv,
                    'type'    => 'news'));
                $v->insert();
                $page->assign('envoye', true);
            } catch (Exception $e) {
                throw $e;
                $page->assign('msg', "Il manque des informations pour créer l'annonce.");
            }
        }

        $page->assign('title', 'Proposer une annonce');
        $page->addCssLink('validate.css');
        $page->changeTpl('validate/prop.news.tpl');
    }
    

    function handler_activity($page)
    {   
        $title      = Env::t('title', '');
        $desc       = Env::t('desc', '');
        $target     = Env::i('target_group_activity', '');
        $caste      = (Env::has('target_everybody_activity'))?'everybody':'restricted';
        $origin = (Env::t('origin_activity_proposal') != 'none')?Env::i('origin_activity_proposal', ''):false;

        $activities = new ActivityFilter(new PFC_And(new AFC_TargetGroup(S::user()->castes(Rights::admin())->groups()),
                                                     new AFC_Regular(true)));
        $activities = $activities->get();
        $activities->select(ActivitySelect::base());
        
        if (Env::has('send_new'))
        {
            if($title == '' || $target == '')
            {
                $page->assign('msg', 'Il manque des informations pour créer l\'activité.');
            }
            else 
            {
                try
                {
                    $target = new Group($target);
                    $target->select(GroupSelect::castes());
                    foreach (S::user()->rights($target) as $r) {
                        if ($r->isMe(new Rights('admin')))
                            $admin = true;
                    }
                    $begin = new FrankizDateTime(Env::t('begin'));
                    $end = new FrankizDateTime(Env::t('end'));
                    $av = new ActivityValidate(s::user(), $target->caste(new Rights($caste)), $title,
                        $desc, $begin, $end, is_null($origin)?false:new Group($origin));
                    $v = new Validate(array(
                        'writer'    => S::user(),
                        'group'     => $target,
                        'item'      => $av,
                        'type'      => 'activity'));
                    if (!$admin) {
                        $v->insert();
                        $page->assign('envoye', true);
                    }
                    else {
                        Env::set('comm', 'Validation automatique');
                        if ($v->commit()) {
                        $page->assign('valide', true);
                        }
                        else {
                            $page->assign('valide', true);
                        }
                    }

                }
                catch (Exception $e)
                {
                    $page->assign('msg', 'La date est incorrecte.');
                }
            }
        }
        
        if (Env::has('send_reg'))
        {
            $begin = Env::t('begin');
            $end = Env::t('end');
            $date = Env::t('other_date');
            $aid = Env::i('regular_activity_proposal', '');
            $a = $activities->get($aid);
            if(!$a)
            {
                $page->assign('msg', 'Tu n\'as pas le droit de créer de nouvelles instances de cette activité.');
            }
            else 
            {
                $dates = $a->next_dates(5);
		        foreach($dates as $temp)
		            foreach($temp as $dat)
		                if (Env::has($dat . '_regular_proposal'))
		                {
                            try
                            {
                                $begin_c = new FrankizDateTime($dat . ' ' . $begin . ':00');
                                $end_c = new FrankizDateTime($dat . ' ' . $end . ':00');
                                $av = new ActivityInstance(array(
                                'activity'  => $a,
                                'writer'    => S::user(),
                                'begin'     => $begin_c,
                                'end'       => $end_c,
                                'comment'   => Env::t('comment', '')));
                                $av->insert();
                            }
                            catch (Exception $e)
                            {
                                $page->assign('msg', 'Les dates sont fausses.');
                            }
		                }
                if (Env::has('other_regular_proposal'))
                {
                    try
                    {
                        $begin_c = new FrankizDateTime($date . ' ' . $begin . ':00');
                        $end_c = new FrankizDateTime($date . ' ' . $end . ':00');
                        $av = new ActivityInstance(array(
                        'activity'  => $a,
                        'writer'    => S::user(),
                        'begin'     => $begin_c,
                        'end'       => $end_c,
                        'comment'   => Env::t('comment', '')));
                        $av->insert();
                    }
                    catch (Exception $e)
                    {
                        $page->assign('msg', 'Les dates sont fausses.');
                    }
                }
                $page->assign('msg', 'Ton activité a été rajoutée.');
            }
        }
        
        if (Env::has('new_regular'))
            pl_redirect('activity/regular/new');
            
        if (Env::has('modify_regular'))
            pl_redirect('activity/regular/modify');
            
        
        $page->assign('title_activity', $title);
        $page->assign('desc', $desc);
        
        $page->assign('regular_activities', $activities);
        $page->assign('title', 'Proposer une activité');
        $page->addCssLink('validate.css');
        $page->changeTpl('validate/prop.activity.tpl');
    }
    
    function handler_activity_ajax($page)
    {   
        $aid = Env::i('aid', '');
        $a = new Activity($aid);
        $a->select(ActivitySelect::base());
        $page->assign('days', $a->next_dates(5));
        $page->assign('activity', $a);
        $page->changeTpl('validate/prop.activity.ajax.tpl', NO_SKIN);
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
                    'user'  =>S::user(), 
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

    function handler_qdj($page)
    {   
        $question = Env::t('quest');
        $answer1 = Env::t('ans1');
        $answer2 = Env::t('ans2');
        
        if (Env::has('send')) 
        {
            if($question == '' || $answer1 == '' || $answer2 == '')
            {
                $page->assign('msg', 'Il manque des informations.');
            }
            else 
            {
                $qv = new QDJValidate($question, $answer1, $answer2);
                $v = new Validate(array(
                    'user'  => S::user(),
                    'gid'   => Group::from('qdj'),
                    'item'  => $qv,
                    'type'  => 'qdj'));
                $v->insert();
                $page->assign('envoye', true);
            }
        }
        
        $page->addCssLink('validate.css');
        $page->assign('title', 'Proposition d\'une qdj');
        $page->changeTpl('validate/prop.qdj.tpl');
    }

    function handler_survey($page)
    {
        if (Env::has('send')) {
            if($title == '' || $content == '' || $end == '' || $gid == '')
            {
                $page->assign('msg', 'Il manque des informations pour créer l\'annonce.');
            }
            else 
            {
                try
                {
                    $end_c = new FrankizDateTime($end);
                    $n = new News(array(
                        'writer'    => S::user(),
                        'target'    => new Group($gid),
                        'iid'       => $iid,
                        'origin'    => new Group($origin),
                        'title'     => $title,
                        'content'   => $content,
                        'end'       => $end_c,
                        'comment'   => $comment));
                    $nv = new NewsValidate($n);
                    $v = new Validate(array(
                        'user'  => S::user(),
                        'gid'   => $gid,
                        'item'  => $nv,
                        'type'  => 'news'));
                    $v->insert();
                    $page->assign('envoye', true);
                }
                catch (Exception $e)
                {
                    $page->assign('msg', 'La date n\'est pas valide.');
                }
            }
        }

        $page->assign('title', 'Créer un sondage');
        $page->addCssLink('validate.css');
        //$page->addCssLink('surveys.css');
        $page->changeTpl('validate/prop.survey.tpl');
    }
}
