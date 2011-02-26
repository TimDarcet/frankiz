<?php
/***************************************************************************
 *  Copyright (C) 2010 Binet Réseau                                       *
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

class ActivityModule extends PLModule
{
    function handlers()
    {
        return array(
            'activity'                      => $this->make_hook('activity',             AUTH_PUBLIC),
            'activity/timetable'            => $this->make_hook('timetable',            AUTH_PUBLIC),
            "activity/rss"                  => $this->make_hook("rss",                  AUTH_PUBLIC, "user", NO_HTTPS),
            'activity/admin'                => $this->make_hook('admin',                AUTH_MDP),
            'activity/modify'               => $this->make_hook('modify',               AUTH_MDP),
            'activity/regular/new'          => $this->make_hook('new_regular',          AUTH_MDP),
            'activity/regular/modify'       => $this->make_hook('modify_regular',       AUTH_MDP),
            'activity/participants'         => $this->make_hook('participants',         AUTH_MDP),
            'activity/participants/add'     => $this->make_hook('participants_add',     AUTH_COOKIE),
            'activity/participants/del'     => $this->make_hook('participants_del',     AUTH_COOKIE),
            'activity/ajax/get'             => $this->make_hook('ajax_get',             AUTH_INTERNAL),
            'activity/ajax/timetable'       => $this->make_hook('ajax_timetable',       AUTH_PUBLIC),
            'activity/ajax/admin'           => $this->make_hook('ajax_admin',           AUTH_MDP),
            'activity/ajax/modify'          => $this->make_hook('ajax_modify',          AUTH_MDP),
        );
    }


    function handler_activity($page)
    {
        $activities = new ActivityInstanceFilter(
            new PFC_Or (new PFC_And(new AIFC_END(new FrankizDateTime(), AIFC_End::AFTER),
                                    new AIFC_User(S::user(), 'restricted')),
                        new PFC_And(new AIFC_END(new FrankizDateTime(), AIFC_End::AFTER),
                                    new AIFC_User(S::user(), 'everybody'))));

        $c = $activities->get();
        $c->select(ActivityInstanceSelect::all());
        $c->order('hour_begin');

        $split = $c->split('date');
        ksort($split);

        $page->assign('activities', $split);

        $page->assign('title', 'Activités');
        $page->addCssLink('activity.css');
        $page->changeTpl('activity/activities.tpl');
    }

    function handler_timetable($page)
    {
        $page->addCssLink('wdcalendar.css');
        $page->addCssLink('activity.css');
        $page->assign('title', 'Emploi du temps');
        $page->changeTpl('activity/timetable.tpl');
    }


    function handler_rss($page, $user = null, $hash = null)
    {
        $feed = new ActivityFeed();
        return $feed->run($page, $user, $hash);
    }

    function handler_admin($page)
    {
        $page->addCssLink('activity.css');
        $page->assign('title', 'Administration des activités');
        $page->changeTpl('activity/admin.tpl');
    }

    function handler_modify($page, $id = false)
    {
        $date = new FrankizDateTime();
        $date->sub(new DateInterval('P1D'));
        $activities = new ActivityInstanceFilter(
                            new PFC_AND(new AIFC_TargetGroup(S::user()->castes(Rights::admin())->groups()),
                                        new AIFC_END($date, AIFC_End::AFTER)));
        $c = $activities->get();
        $c->select(ActivityInstanceSelect::base());

        if (Env::has('admin_id')) {
            $id = Env::i('admin_id');
        }

        if ($id !== false)
        {
            $id = Env::i('admin_id');
	        $a = $c->get($id);
            
	        if($a === false) {
                throw new Exception("Invalid credentials");
            }

	        if (Env::has('modify'))
	        {
                S::assert_xsrf_token();
                
                try
                {
                    $begin = new FrankizDateTime(Env::t('begin'));
                    $end = new FrankizDateTime(Env::t('end'));
                    if ($a->regular())
                    {

                        $a->comment(Env::t('comment'));
                        $a->begin($begin);
                        $a->end($end);
                        $page->assign('msg', 'L\'activité a été modifiée.');
                    }
                    else
                    {
                        $a->begin($begin);
                        $a->end($end);

                        $a1 = $a->activity();
                        $a1->title(Env::t('title', $a1->title()));
                        $a1->description(Env::t('description', $a1->description()));
                        $a1->replace();
                        $page->assign('msg', 'L\'activité a été modifiée.');
                    }
                }
                catch (Exception $e)
                {
                    $page->assign('msg', 'Les dates données sont incorrectes.');
                }
	        }
            
            if (Env::has('delete'))
            {
                S::assert_xsrf_token();
                
                $c->remove($a);
                if ($a->regular())
                {
                    $a->delete();
                }
                else
                {
                    $a1 = $a->activity();
                    $a1->delete();
                    $a->delete();
                }
                $page->assign('delete', true);
            }
            $page->assign('id', $id);
	        $page->assign('activity', $a);
        }
        $page->assign('activities', $c);

        $page->assign('title', 'Modifier les activités en cours');
        $page->addCssLink('activity.css');
        $page->changeTpl('activity/modify.tpl');
    }

    function handler_new_regular($page)
    {
        $title          = Env::t('title', '');
        $description    = Env::t('description', '');
        $default_begin  = Env::t('begin', '00:00');
        $default_end    = Env::t('end', '00:00');
        $days           = Env::v('days');
        $target         = Env::i('target_group_activity', '');
        $caste          = (Env::has('target_everybody_activity'))?'everybody':'restricted';
        
        if (Env::has('send'))
        {
            S::assert_xsrf_token();
            
            if($title == '' || is_null($days) || $default_begin == '00:00' || 
                $default_end == '00:00' || $target == '' ||
                !(preg_match( '`^\d{2}:\d{2}$`' , $default_begin) && strtotime($default_begin) !== false
                        && preg_match( '`^\d{2}:\d{2}$`' , $default_end) && strtotime($default_end) !== false))
            {
                $page->assign('msg', 'Il manque des informations pour créer l\'activité. 
                    Attention les heures ne peuvent pas rester de la forme 00:00.');
            }
            else 
            {
                $days = implode(',', $days);
                $target = new Group($target);
                $target->select(GroupSelect::castes());
                
                if (!S::user()->hasRights($target, Rights::admin())) {
                    throw new Exception("Invalid credentials");
                }
                
                $a = new Activity(array(
                    'target'        => $target->caste(new Rights($caste)),
                    'origin'        => $target,
                    'title'         => $title,
                    'description'   => $description,
                    'days'          => $days,
                    'default_begin' => $default_begin,
                    'default_end'   => $default_end));
                $a->insert();
                $page->assign('envoye', true);
            }
        }
    
    
        $page->assign('title_activity', $title);
        $page->assign('description', $description);
        $page->assign('begin', $default_begin);
        $page->assign('end', $default_end);
        $page->assign('priv', $priv);
        
        $page->assign('title', 'Créer une activité régulière');
        $page->changeTpl('activity/new_regular_activity.tpl');
    }
    

    function handler_modify_regular($page, $aid = false)
    {
        $activities = new ActivityFilter(new PFC_And(new AFC_TargetGroup(S::user()->castes(Rights::admin())->groups()),
                                                     new AFC_Regular(true)));
        $c = $activities->get();
        $c->select(ActivitySelect::base());

        if (Env::has('aid')) {
            $aid = Env::i('aid');
        }

        if ($aid)
        {
	        $a = $c->get($aid);
            
            if ($a === false) {
                throw new Exception("Invalid credentials");
            }
            
	        if (Env::has('modify')) {
                S::assert_xsrf_token();

                if (preg_match( '`^\d{2}:\d{2}$`' , Env::t('begin')) && strtotime(Env::t('begin')) !== false
                    && preg_match( '`^\d{2}:\d{2}$`' , Env::t('end')) && strtotime(Env::t('end')) !== false) {
                    $a->title(Env::t('title', $a->title()));
                    $a->description(Env::t('description', $a->description()));
                    $a->days(implode(',', Env::v('days', $a->days())));
                    $a->default_begin(Env::t('begin') . ':00');
                    $a->default_end(Env::t('end') . ':00');
                    $page->assign('msg', 'L\'activité a été modifiée.');
                }
                else {
                    $page->assign('msg', 'Les dates données sont incorrectes.');
                }
	        }
            $page->assign('aid', $aid);
	        $page->assign('activity', $a);
        }

        $page->assign('activities', $c);

        $page->assign('title', 'Modifier une activité régulière');
        $page->changeTpl('activity/modify_regular.tpl');
    }

    function handler_participants($page)
    {
        $activities = new ActivityInstanceFilter(
            new PFC_Or (new PFC_And(new AIFC_END(new FrankizDateTime(), AIFC_End::AFTER),
                                    new AIFC_User(S::user(), 'restricted')),
                        new PFC_And(new AIFC_END(new FrankizDateTime(), AIFC_End::AFTER),
                                    new AIFC_User(S::user(), 'everybody'))));
        $c = $activities->get();
        $c->select(ActivityInstanceSelect::all());
        
        if (Env::has('participants_id'))
        {
            $id = Env::i('participants_id');
	        $a = $c->get($id);
            
            if ($a === false) {
                throw new Exception("Invalid credentials");
            }
            
            if (Env::has('mail')) {
                S::assert_xsrf_token();
                
                if (Env::t('mail_body') != '' && s::user()->id() == $a->writer()->id()) {
                    $mail = new FrankizMailer();
                    $mail->subject('[Mail groupé] Activité ' . $a->title() . ' du ' . $a->date() . ' à ' . $a->hour_begin());
                    $mail->body(Env::t('mail_body'));
                    $mail->setFrom(S::user()->bestEmail(), S::user()->displayName());
                    $mail->toUserFilter(new UserFilter(new UFC_ActivityInstance($a->id())));
                    $mail->sendLater(false);
                }
                else
                    $page->assign('msg', 'Votre mail n\'est pas rempli.');
            }
            $page->assign('user', s::user());
            $page->assign('id', $id);
	        $page->assign('activity', $a);
        }
        $page->assign('activities', $c);

        $page->assign('title', 'Participants à une activité');
        $page->addCssLink('activity.css');
        $page->changeTpl('activity/participants.tpl');
    }

    function handler_participants_add($page, $id)
    {
        S::assert_xsrf_token();

        $a = new ActivityInstance($id);
        $a->select(ActivityInstanceSelect::base());

        if (!S::user()->hasRights($a->target()->group(), 
                                  ($a->target()->rights())?Rights::restricted():Rights::everybody())) {
            throw new Exception("Invalid credentials");
        }
        S::assert_xsrf_token();

        $a->add_participants(S::user()->id());
        $page->jsonAssign('participant', array('displayName' => s::user()->displayName(),
                                                        'id' => s::user()->id()));
        $page->jsonAssign('success', true);
        return PL_JSON;
    }

    function handler_participants_del($page, $id)
    {
        S::assert_xsrf_token();

        $a = new ActivityInstance($id);
        $a->select(ActivityInstanceSelect::base());

        if (!S::user()->hasRights($a->target()->group(), 
                                  ($a->target()->rights())?Rights::restricted():Rights::everybody())) {
            throw new Exception("Invalid credentials");
        }
        S::assert_xsrf_token();
        
        $a->delete_participants(S::user()->id());
        $page->jsonAssign('participant', array('id' => s::user()->id()));
        $page->jsonAssign('success', true);
        return PL_JSON;
    }

    function handler_ajax_get($page)
    {
        $json = json_decode(Env::v('json'));
        if (isset($json->date))
        {
            $date = new FrankizDateTime($json->date);
            $date->setTime(0,0);
            $date_n = new FrankizDateTime($json->date);
            date_add($date_n, date_interval_create_from_date_string('1 day'));
            $date_n->setTime(0,0);
            
            $activities = new ActivityInstanceFilter(
                new PFC_And(new PFC_Or (new AIFC_User(S::user(), 'restricted'),
                                        new AIFC_User(S::user(), 'everybody')),
                            new AIFC_Period($date, $date_n)));

            $c = $activities->get();
            $c->select(ActivityInstanceSelect::all());
            $c->order('hour_begin', false);
            $result = array();

            foreach($c as $a)
            {
                $page->assign('day', $date);
                $page->assign('activity', $a);
                $result[] = $page->fetch(FrankizPage::getTplPath('minimodules/activity/single.tpl'));
            }

            $page->jsonAssign('success', true);
            $page->jsonAssign('activities', $result);
        }

        else if (isset($json->ids))
        {
            $c = new Collection('ActivityInstance');
            foreach ($json->ids as $id)
            {
                $c->add($id);
            }
            $c->select(ActivityInstanceSelect::all());
            
            $activities = array();
            foreach ($c as $a)
            {
                if (!S::user()->hasRights($a->target()->group(), 
                                          ($a->target()->rights())?Rights::restricted():Rights::everybody())) {
                    throw new Exception("Invalid credentials");
                }
                
                $activities[$a->id()] = $a->export();
            }
            $page->jsonAssign('success', true);
            $page->jsonAssign('activities', $activities);
        }
        return PL_JSON;
    }

    function handler_ajax_admin($page)
    {
        $json = json_decode(Env::v('json'));
        $id = $json->id;
        if (isset($json->admin))
        {
            $a = new ActivityInstance($id);
            $a->select(ActivityInstanceSelect::base());
            
            if (!S::user()->hasRights($a->target()->group(), Rights::admin())) {
                throw new Exception("Invalid credentials");
            }
            
            $page->assign('activity', $a);
            if ($a->regular())
                $result = $page->fetch(FrankizPage::getTplPath('activity/modify_instance_regular.tpl'));
            else
                $result = $page->fetch(FrankizPage::getTplPath('activity/modify_punctual.tpl'));
                
            $page->jsonAssign('success', true);
            $page->jsonAssign('activity', $result);
        }
        elseif (isset($json->regular))
        {
            $a = new Activity($id);
            $a->select(ActivitySelect::base());
            
            if (!S::user()->hasRights($a->target()->group(), Rights::admin())) {
                throw new Exception("Invalid credentials");
            }
            
            $page->assign('activity', $a);
            $result = $page->fetch(FrankizPage::getTplPath('activity/modify_regular_activity.tpl'));
            
            $page->jsonAssign('success', true);
            $page->jsonAssign('activity', $result);
        }
        elseif(isset($json->participants))
        {
            $a = new ActivityInstance($id);
            $a->select(ActivityInstanceSelect::all());

            if (!S::user()->hasRights($a->target()->group(), 
                                      ($a->target()->rights())?Rights::restricted():Rights::everybody())) {
                throw new Exception("Invalid credentials");
            }
            
            $page->assign('activity', $a);
            $page->assign('user', s::user());
            $result = $page->fetch(FrankizPage::getTplPath('activity/participants_activity.tpl'));
            
            $page->jsonAssign('success', true);
            $page->jsonAssign('activity', $result);
        }

        return PL_JSON;
    }

    function handler_ajax_timetable($page)
    {
        $day = env::t("showdate");
        $type = env::t("viewtype");

        $page->jsonAssign('day', $day);
        $phpTime = strtotime($day);
        switch($type){
		  case "month":
		      $st = mktime(0, 0, 0, date("m", $phpTime), 1, date("Y", $phpTime));
		      $et = mktime(0, 0, -1, date("m", $phpTime)+1, 1, date("Y", $phpTime));
		      break;
		  case "week":
		      $monday  =  date("d", $phpTime) - date('N', $phpTime) + 1;
		      $st = mktime(0,0,0,date("m", $phpTime), $monday, date("Y", $phpTime));
		      $et = mktime(0,0,-1,date("m", $phpTime), $monday+7, date("Y", $phpTime));
		      break;
		  case "day":
		      $st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));
		      $et = mktime(0, 0, -1, date("m", $phpTime), date("d", $phpTime)+1, date("Y", $phpTime));
		      break;
		}

        $date = new FrankizDateTime(date('Y-m-d H:i:s', $st));
        $date_n = new FrankizDateTime(date('Y-m-d H:i:s', $et));
        $activities = new ActivityInstanceFilter(
            new PFC_And(new PFC_Or (new AIFC_User(S::user(), 'restricted'),
                                    new AIFC_User(S::user(), 'everybody')),
                        new AIFC_Period($date, $date_n)));

        $c = $activities->get();
        $c->select(ActivityInstanceSelect::all());

        $page->jsonAssign('issort', true);
        $page->jsonAssign('start', date("m/d/Y H:i", $st));
        $page->jsonAssign('end', date("m/d/Y H:i", $et));
        $page->jsonAssign('error', null);

        $events = array();
        foreach ($c as $e)
        {
            $events[] = array(
                $e->id(),
                $e->title(),
                $e->begin()->format("m/d/Y H:i"),
                $e->end()->format("m/d/Y H:i"),
                0,
                $e->begin()->format("m/d/Y") != $e->end()->format("m/d/Y"), //more than one day event
                //$row->InstanceType,
                $e->regular(),//Recurring event,
                ($e->activity()->id() % 15) - 1,
                0, //editable
                $e->description(),
                ''
              );
        }
        $page->jsonAssign('events', $events);
        return PL_JSON;
    }

    function handler_ajax_modify($page, $type)
    {
        $json = json_decode(Env::v('json'));
        if ($type == 'instance')
        {
            $id = $json->admin_id;
            $ai = new ActivityInstance($id);
            $ai->select(ActivityInstanceSelect::base());

            if (!S::user()->hasRights($ai->target()->group(), Rights::admin())) {
                throw new Exception("Invalid credentials");
            }
            S::assert_xsrf_token();

            try
            {
                $begin = new FrankizDateTime($json->begin);
                $end = new FrankizDateTime($json->end);
                
                if ($ai->regular())
                {
                    $ai->comment($json->comment);
                    $ai->begin($begin);
                    $ai->end($end);
                }
                else
                {
                    $ai->begin($begin);
                    $ai->end($end);

                    $a = $ai->activity();
                    $a->title($json->title);
                    $a->description($json->description);
                }
                
                $page->jsonAssign('success', true);
            }
            catch (Exception $e)
            {
                $page->jsonAssign('success', false);
            }
        }
        else if ($type == 'regular')
        {
            $id = $json->aid;
            $a = new Activity($id);
            $a->select(ActivitySelect::base());
            
            if (!S::user()->hasRights($a->target()->group(), Rights::admin())) {
                throw new Exception("Invalid credentials");
            }
            S::assert_xsrf_token();
            
            if (preg_match( '`^\d{2}:\d{2}:\d{2}$`' , $json->begin) && strtotime($json->begin) !== false
                && preg_match( '`^\d{2}:\d{2}:\d{2}$`' , $json->end) && strtotime($json->end) !== false)
            {
                $a->title($json->title);
                $a->description($json->description);
                $key = 'days[]';
                $days = unflatten($json->$key);
                $a->days(implode(',', $days));
                $a->default_begin($json->begin);
                $a->default_end($json->end);
                $page->jsonAssign('success', true);
            }
            else
            {
                $page->jsonAssign('success', false);
            }
        }
        return PL_JSON;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
