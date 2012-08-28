<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
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

class ProposalModule extends PlModule
{
    function handlers()
    {
        return array(
            'proposal/remove'           => $this->make_hook('remove',        AUTH_MDP),
            'proposal/news'             => $this->make_hook('news',          AUTH_COOKIE),
            'proposal/activity'         => $this->make_hook('activity',      AUTH_COOKIE),
            'proposal/activity/ajax'    => $this->make_hook('activity_ajax', AUTH_COOKIE),
            'proposal/mail'             => $this->make_hook('mail',          AUTH_COOKIE),
            'proposal/qdj'              => $this->make_hook('qdj',           AUTH_COOKIE),
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

    function handler_remove($page, $id = null)
    {
        S::assert_xsrf_token();

        $val = Validate::fromId($id, false);
        if ($val === false) {
            $page->trigError("This item doesn't exist");
            return;
        }

        $val->select(ValidateSelect::validate());

        if ($val->writer()->id() != S::user()->id()) {
            throw new Exception("Invalid crendentials");
        }

        S::logger()->log('proposal/remove',
                         array('type' => $val->type(),
                             'writer' => $val->writer()->id(),
                              'group' => $val->group()->id(),
                            'created' => $val->created()->toDb(),
                               'item' => $val->itemToDb()));
        $val->item()->sendmailcancel(S::user());
        $val->clean();

        pl_redirect(Env::v('url'));
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
                    $origin->select(GroupSelect::base());
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
                if ($origin !== false && !S::user()->hasRights($origin, Rights::admin())) {
                    if (S::user()->hasRights($origin, Rights::restricted())) {
                        $valid_origin = true;
                    }
                    else {
                        throw new Exception("Invalid credentials for origin Group");
                    }
                }
                else {
                    $valid_origin = false;
                }

                $target->select(CasteSelect::validate());
                $nv = new NewsValidate(array(
                    'writer'        => S::user(),
                    'target'        => $target,
                    'image'         => $image,
                    'origin'        => $origin,
                    'title'         => $title,
                    'content'       => $content,
                    'begin'         => $begin,
                    'end'           => $end,
                    'comment'       => $comment,
                    'valid_origin'  => $valid_origin));
                $v = new Validate(array(
                    'writer'  => S::user(),
                    'group'   => ($valid_origin)?$origin:$target_group,
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
        $desc       = Env::t('activity_description', '');

        $activities = new ActivityFilter(new PFC_And(new AFC_TargetGroup(S::user()->castes(Rights::admin())->groups()),
                                                     new AFC_Regular(true)));
        $activities = $activities->get();
        $activities->select(ActivitySelect::base());

        if (Env::has('send_new'))
        {
            $required_fields = array('origin_activity_proposal', 'target_group_activity',
                                    'title', 'begin', 'end');
            foreach ($required_fields as $field) {
                if (Env::v($field, '') == '') {
                    throw new Exception("Missing field ($field)");
                }
            }

            try
            {
                // Origin & Target
                if (Env::t('origin_activity_proposal') == 'false') {
                    $origin = false;
                } else {
                    $origin = new Group(Env::i('origin_activity_proposal'));
                }
                list($target, $target_group) = self::target_picker_to_caste_group('activity');

                $begin = new FrankizDateTime(Env::t('begin'));
                $end = new FrankizDateTime(Env::t('end'));

                if ($origin !== false && !S::user()->hasRights($origin, Rights::admin())) {
                    if (S::user()->hasRights($origin, Rights::restricted())) {
                        $valid_origin = true;
                        $origin->select(GroupSelect::base());
                    }
                    else {
                        throw new Exception("Invalid credentials for origin Group");
                    }
                }
                else {
                    $valid_origin = false;
                }

                $target->select(CasteSelect::validate());

                $av = new ActivityValidate(array(
                    'writer'        => S::user(),
                    'target'        => $target,
                    'title'         => $title,
                    'description'   => $desc,
                    'begin'         => $begin,
                    'end'           => $end,
                    'origin'        => $origin,
                    'valid_origin'  => $valid_origin));

                if($origin !== false || S::user()->group()->id() != $target->group()->id()) {
                    $v = new Validate(array(
                        'writer'    => S::user(),
                        'group'     => ($valid_origin)?$origin:$target_group,
                        'item'      => $av,
                        'type'      => 'activity'));
                    $v->insert();
                } else {
                    $av->commit();
                    $page->assign('auto', true);
                }
                $page->assign('envoye', true);
            }
            catch (Exception $e)
            {
                $page->assign('msg', $e->getMessage() . 'La date est incorrecte.');
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
                                'comment'   => Env::t('activity_comment', '')));
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
                        'comment'   => Env::t('activity_comment', '')));
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

        $page->assign('title_activity', $title);
        $page->assign('desc', $desc);

        $page->assign('regular_activities', $activities);
        $page->assign('title', 'Proposer une activité');
        $page->addCssLink('validate.css');
        $page->changeTpl('validate/prop.activity.tpl');
    }

    function handler_activity_ajax($page)
    {
        $a = Activity::fromId(Env::i('aid'), false);
        if ($a) {
            $a->select(ActivitySelect::base());
            $page->assign('days', $a->next_dates(5));
        }
        $page->assign('activity', $a);
        $page->changeTpl('validate/prop.activity.ajax.tpl', NO_SKIN);
    }

    function handler_mail($page)
    {
        $subject = Env::t('subject', '');
        $body    = Env::t('mail_body', '');
        $no_wiki = Env::has('no_wiki');

        // Retrieve the years on_platal of each formation
        $formations = Formation::selectAll(FormationSelect::on_platal());

        if (Env::has('send')) {
            try {
                $required_fields = array(
                    'subject' => 'Il faut donner un sujet à ton mail',
                    'mail_body' => 'Tu ne veux pas envoyer de mail vide à tous. Si ?');
                foreach ($required_fields as $field => $msg) {
                    if (Env::v($field, '') == '') {
                        throw new Exception($msg);
                    }
                }

                if (Env::t('origin_mail_proposal') == 'false') {
                    $origin = false;
                } else {
                    $origin = new Group(Env::i('origin_mail_proposal'));
                }

                if ($origin !== false && !S::user()->hasRights($origin, Rights::admin())) {
                    throw new Exception("Invalid credentials for origin Group");
                }

                if (Env::t('type_mail_proposal') == 'group') {
                    // Mail to a group
                    list($temp, $target_group) = self::target_picker_to_caste_group('mail');
                    $target = new Collection('Caste');
                    $target->add($temp);
                    $target_group->select(GroupSelect::validate());

                    $nv = new MailValidate(array(
                        'writer'    => S::user(),
                        'type_mail' => Env::t('type_mail_proposal'),
                        'origin'    => $origin,
                        'targets'   => $target,
                        'subject'   => $subject,
                        'body'      => $body,
                        'nowiki'    => $no_wiki,
                        'formation' => $target_group));
                    $el = new Validate(array(
                        'item'      => $nv,
                        'group'     => $target_group,
                        'writer'    => S::user(),
                        'type'      => 'mail'));
                    $el->insert();
                } elseif (Env::t('type_mail_proposal') == 'promo') {
                    // Target group is a Collection of formation groups, which validate requests
                    $target_group = new Collection('Group');

                    // Group promos by formation
                    $promos = unflatten(Env::v('promos'));
                    $promosByFormation = array();
                    foreach ($promos as $formation_promo) {
                        $formation_promo = trim($formation_promo);
                        if (!$formation_promo) {
                            continue;
                        }
                        if (!preg_match('/^([0-9]+)_([0-9]+)$/', $formation_promo, $matches)) {
                            throw new Exception("Oops, mauvais format de destinataire.");
                        }
                        $formid = (integer)$matches[1];
                        $promo = (integer)$matches[2];
                        if (isset($promosByFormation[$formid]))
                            $promosByFormation[$formid][] = $promo;
                        else
                            $promosByFormation[$formid] = array($promo);
                    }

                    if (empty($promosByFormation)) {
                        throw new Exception("Il faut indiquer au moins un destinataire.");
                    }

                    foreach ($promosByFormation as $formid => $promos) {
                        // Now, $promos are the list of promos of formation $formid
                        $form = $formations->get($formid);

                        // Study group are the people the mail is sent to, array of CasteFilterCondition
                        $cfc_study_groups = array();
                        foreach ($promos as $promo) {
                            if (!$form->hasPlatalYear($promo)) {
                                throw new Exception("Mauvaise promo " . $promo . " pour " . $form->label() . ".");
                            }
                            $cfc_study_groups[] = new CFC_Group(
                                $form->getGroupForPromo($promo),
                                Rights::restricted());
                        }
                        $target = new CasteFilter(new PFC_Or($cfc_study_groups));
                        $target = $target->get();
                        $target->select(CasteSelect::validate());

                        // $target_group is the group which validates this email
                        $target_group = $form->getGroup();
                        $target_group->select(GroupSelect::validate());

                        $nv = new MailValidate(array(
                            'writer'    => S::user(),
                            'type_mail' => Env::t('type_mail_proposal'),
                            'origin'    => $origin,
                            'targets'   => $target,
                            'subject'   => $subject,
                            'body'      => $body,
                            'nowiki'    => $no_wiki,
                            'formation' => $target_group));
                        $el = new Validate(array(
                            'item'      => $nv,
                            'group'     => $target_group,
                            'writer'    => S::user(),
                            'type'      => 'mail'));
                        $el->insert();
                    }
                }
                $page->assign('envoye', true);
            } catch (Exception $e) {
                $page->trigError($e->getMessage());
            }
        }

        $page->assign('subject', $subject);
        $page->assign('body', $body);
        $page->assign('nowiki', $no_wiki);
        $page->assign('formations', $formations);

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
                    'writer'    => S::user(),
                    'group'     => Group::from('qdj'),
                    'item'      => $qv,
                    'type'      => 'qdj'));
                $v->insert();
                $page->assign('envoye', true);
            }
        }

        $page->addCssLink('validate.css');
        $page->assign('title', 'Proposition d\'une qdj');
        $page->changeTpl('validate/prop.qdj.tpl');
    }
}
