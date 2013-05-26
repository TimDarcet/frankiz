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

class GroupsModule extends PLModule
{
    public function handlers()
    {
        return array(
            // Listing of the groups
            'groups'                  => $this->make_hook('groups',            AUTH_PUBLIC),
            'groups/ajax/search'      => $this->make_hook('ajax_search',       AUTH_PUBLIC),

            // Page of a single group
            'groups/see'              => $this->make_hook('group_see',         AUTH_PUBLIC),
            'groups/ajax/users'       => $this->make_hook('group_ajax_users',  AUTH_INTERNAL, ''),
            'groups/ajax/news'        => $this->make_hook('group_ajax_news',   AUTH_INTERNAL, ''),
            'groups/ajax/open'        => $this->make_hook('group_ajax_open',   AUTH_INTERNAL, ''),
            'groups/subscribe'        => $this->make_hook('group_subscribe',   AUTH_COOKIE),
            'groups/unsubscribe'      => $this->make_hook('group_unsubscribe', AUTH_COOKIE),

            // Admin page of a group
            'groups/admin'            => $this->make_hook('group_admin',             AUTH_COOKIE),
            'groups/ajax/admin/users' => $this->make_hook('group_ajax_admin_users',  AUTH_COOKIE),
            'groups/ajax/admin/rights'=> $this->make_hook('group_ajax_admin_rights', AUTH_COOKIE),

            // Common handler
            'groups/ajax/comment'     => $this->make_hook('ajax_comment', AUTH_COOKIE),

            // Admin stuffs
            'groups/insert'           => $this->make_hook('group_insert', AUTH_MDP),
        );
    }

    function handler_groups($page)
    {
        global $globals;
        if(S::i('auth')>=AUTH_INTERNAL)
            $restrict = new PFC_True();
        else
            $restrict = new GFC_External(true);

        $max = $globals->groups->limit;

        // Re-fetch user's groups
        S::user()->select(UserSelect::castes());

        // Fetch samples of other groups
        $binet = new GroupFilter(new PFC_And(new GFC_Namespace(Group::NS_BINET), $restrict), new GFO_Score(true));
        $binet = $binet->get(new PlLimit($max));

        $course = new GroupFilter(new PFC_And(new GFC_Namespace(Group::NS_COURSE), $restrict), new GFO_Score(true));
        $course = $course->get(new PlLimit($max));

        $free = new GroupFilter(new PFC_And(new GFC_Namespace(Group::NS_FREE), $restrict), new GFO_Score(true));
        $free = $free->get(new PlLimit($max));

        // Load associated datas
        $temp = new Collection('Group');
        $temp->merge($binet)->merge($course)->merge($free);
        $temp->select(GroupSelect::base());

        $user_binet = S::user()->castes()->groups()->filter('ns', Group::NS_BINET)->remove($binet);
        $page->assign('binet', $binet);
        $page->assign('user_binet', $user_binet);

        $user_course = S::user()->castes()->groups()->filter('ns', Group::NS_COURSE)->remove($course);
        $page->assign('course', $course);
        $page->assign('user_course', $user_course);

        $user_free = S::user()->castes()->groups()->filter('ns', Group::NS_FREE)->remove($free);
        $page->assign('free', $free);
        $page->assign('user_free', $user_free);

        $page->assign('user', S::user());
        $page->assign('title', 'Groupes');
        $page->changeTpl('groups/groups.tpl');
        $page->addCssLink('groups.css');
    }

    function handler_ajax_search($page)
    {
        global $globals;

        if(S::i('auth')>=AUTH_INTERNAL)
            $restrict = new PFC_True();
        else
            $restrict = new GFC_External(true);

        $conditions = new PFC_And(new GFC_Namespace(Json::s('ns')),
                                  new PFC_OR(new GFC_Label(Json::s('piece'), GFC_Label::CONTAINS),
                                             new GFC_Name(Json::s('piece'))), $restrict);

        $desc = Json::b('desc', true);
        if (Json::s('order') == 'name') {
            $order = new GFO_Name($desc);
        } else {
            $order = new GFO_Score($desc);
        }
        $own = new GroupFilter(new PFC_And($conditions, new GFC_User(S::user()->id())), $order);
        $all = new GroupFilter($conditions, $order);

        if (Json::b('html')) {
            // Groups Module
            $own = $own->get();
            $all = $all->get(new PlLimit($globals->groups->limit));
        } else {
            // group_picker
            $own = $own->get(new PlLimit($globals->groups->all_limit));
            $all = $all->get(new PlLimit($globals->groups->own_limit));
        }


        $all->merge($own)->select(GroupSelect::base());
        $all->order(Json::s('order'), $desc);

        if (Json::b('html')) {
            $export = array();

            $page->assign('user', S::user());
            foreach ($all as $g) {
                $page->assign('group', $g);
                $export[$g->id()] = $page->fetch(FrankizPage::getTplPath('groups/line.tpl'));
            }
        } else {
            $export = $all->export(null, true);

            foreach ($all as $g) {
                $export[$g->id()]['rights'] = S::user()->rights($g, true);
            }
        }

        $page->jsonAssign('groups', array_values($export));

        return PL_JSON;
    }

    function handler_group_see($page, $group = null)
    {
        global $platal;
        $page->addCssLink('groups.css');
        try {
            $group = Group::from($group);
        } catch (ItemNotFoundException $e) {
            $page->assign('title', "Ce groupe n'existe pas");
            $page->changeTpl('groups/no_group.tpl');
            return;
        }

        // Fetch the group
        $group->select(GroupSelect::base());
        $page->assign('group', $group);

        // Check rights
        if (S::i('auth') <= AUTH_PUBLIC && !$group->external()) {
            $platal->force_login($page);
            return;
        }
        $group->select(GroupSelect::see());
        $page->assign('roomMaster', $group->isRoomMaster());

        // Relation between the user & the group
        $page->assign('user', S::user());

        if ($group->ns() != 'user') {
            $caste = $group->caste(Rights::member());
            if (!is_null($caste))
                $page->assign('member_allowed', $caste->userfilter());
        }
        $page->assign('title', $group->label());
        $page->changeTpl('groups/group.tpl');
    }

    function handler_group_ajax_users($page)
    {
        $group = Json::i('gid');
        $limit = 25;

        $group = Group::fromId($group);

        $users = false;
        if ($group) {
            $users = array();

            $group->select(GroupSelect::castes());

            $order = new UFO_Name(UFO_Name::LASTNAME);

            $filters = new PFC_True();
            if (strlen(Json::t('promo')) > 0) {
                $filters = new UFC_Group(explode(';', Json::v('promo')));
            }

            $uf = new UserFilter(new PFC_And(new UFC_Caste($group->caste(Rights::admin())), $filters), $order);
            $admins = $uf->get(new PlLimit($limit, (Json::i('admin_page', 1) - 1) * $limit));
            $admins_total = $uf->getTotalCount();

            $uf = new UserFilter(new PFC_And(new UFC_Caste(array($group->caste(Rights::member()), $group->caste(Rights::logic()))), $filters), $order);
            $members = $uf->get(new PlLimit($limit, (Json::i('member_page', 1) - 1) * $limit));
            $members_total = $uf->getTotalCount();

            $uf = new UserFilter(new PFC_And(new UFC_Caste($group->caste(Rights::friend())), $filters), $order);
            $friends = $uf->get(new PlLimit($limit, (Json::i('friend_page', 1) - 1) * $limit));
            $friends_total = $uf->getTotalCount();

            $all = new Collection('User');
            $all->safeMerge(array($admins, $members, $friends));
            $all->select(UserSelect::base());

            $admins_export = $admins->export(User::EXPORT_MICRO, true);
            $members_export = $members->export(User::EXPORT_MICRO, true);
            $friends_export = $friends->export(User::EXPORT_MICRO, true);

            if ($all->count() > 0) {
                $iter = XDB::iterRow('SELECT  uid, comment
                                        FROM  users_comments
                                       WHERE  gid = {?} AND uid IN {?}',
                                              $group->id(), $all->ids());

                while (list($uid, $comment) = $iter->next()) {
                    if ($admins_export[$uid]) {
                        $admins_export[$uid]['comments'] = $comment;
                    }
                    if ($members_export[$uid]) {
                        $members_export[$uid]['comments'] = $comment;
                    }
                    if ($friends_export[$uid]) {
                        $friends_export[$uid]['comments'] = $comment;
                    }
                }
            }

            $users['admin'] = array('total' => $admins_total, 'users' => $admins_export);
            $users['member'] = array('total' => $members_total, 'users' => $members_export);
            $users['friend'] = array('total' => $friends_total, 'users' => $friends_export);
        }

        $page->jsonAssign('limit', $limit);
        $page->jsonAssign('users', $users);
        return PL_JSON;
    }

    function handler_group_ajax_news($page, $group)
    {
        $group = Group::fromId($group);
        $news = false;
        if ($group) {
            $news = array();

            $group->select(GroupSelect::castes());

            $filters = new UFC_Group(explode(';', Json::v('promo')));

            $onlybefore = new NFC_Begin(new FrankizDateTime(), NFC_Begin::BEFORE);
            if (S::user()->hasRights($group, Rights::admin()) || S::user()->isWeb()) {
                $onlybefore = new PFC_True();
            }

            $nf = new NewsFilter(new PFC_And(new NFC_Origin($group),
                                             new NFC_CanBeSeen(S::user()),
                                             $onlybefore),
                                 new NFO_Begin(true));
            $ns = $nf->get(new PlLimit(10))->select(NewsSelect::head());

            foreach($ns as $nid => $n) {
                $page->assign('news', $n);
                $news[$nid] = $page->fetch(FrankizPage::getTplPath('groups/news.tpl'));
            }
        }

        $page->jsonAssign('news', $news);
        return PL_JSON;
    }

    function handler_group_ajax_open($page, $gid, $rid, $state = null)
    {
        S::assert_xsrf_token();
        $room = new Room($rid);
        $group = Group::fromId($gid, false);
        if ($group) {
            $group->select(GroupSelect::premises());
            if ($group->isRoomMaster()) {
                $room->open($state);
            }
        }
        return PL_JSON;
    }

    function handler_group_admin($page, $group = null)
    {
        $group = Group::fromId($group);

        if ($group && (S::user()->hasRights($group, Rights::admin()) || S::user()->isWeb())) {
            $group->select(GroupSelect::see());
            $page->assign('group', $group);

            if (Env::has('name') && Env::t('name') != '' && S::user()->isAdmin()) {
                S::logger()->log("groups/admin",
                                 array("gid" => $group->id(),
                                  "old_name" => $group->name(),
                                  "new_name" => Env::t('name')));
                $group->name(Env::t('name'));
            }

            if (Env::has('update') && S::user()->isAdmin()) {
                $group->external(Env::has('external'));
                $group->leavable(Env::has('leavable'));
                $group->visible(Env::has('visible'));
            }

            if (Env::has('label')) {
                $group->label(Env::t('label'));
            }

            if (Env::has('update')) {
                $group->description(Env::t('description'));
                $group->web(Env::t('web'));
                $group->wikix(Env::t('wikix'));
                $group->mail(Env::t('mail'));
            }

            if (Env::has('image')) {
                $image = new ImageFilter(new PFC_And(new IFC_Id(Env::i('image')), new IFC_Temp()));
                $image = $image->get(true);
                if (!$image) {
                    throw new Exception("This image doesn't exist anymore");
                }
                $image->select(FrankizImageSelect::caste());
                $image->label($group->label());
                $image->caste($group->caste(Rights::everybody()));
                $group->image($image);
            }

            if (S::user()->isWeb()) {
                $nss = XDB::fetchColumn('SELECT ns FROM groups GROUP BY ns');
                $page->assign('nss', $nss);
                if (Env::has('ns')) {
                    S::logger()->log("groups/admin",
                                     array("gid" => $group->id(),
                                        "old_ns" => $group->ns(),
                                        "new_ns" => Env::t('ns')));
                    $group->ns(Env::t('ns'));
                }

                if (Env::has('add_room')) {
                    $r = Room::fromId(Env::t('rid'), false);
                    if($r) {
                        $r->select(RoomSelect::base());
                        $group->addRoom($r);
                    } else {
                        $err[] = "La chambre entrée n'existe pas.";
                    }
                }

                if (Env::has('del_room')) {
                    $r = Room::fromId(Env::t('rid'), false);
                    if($r) {
                        $group->removeRoom($r);
                    } else {
                        $err[] = "La chambre entrée n'existe pas.";
                    }
                }
            }

            $page->assign('title', 'Administration de "' . $group->label() . '"');
            $page->addCssLink('groups.css');
            $page->changeTpl('groups/admin.tpl');
        } else {
            $page->assign('title', "Ce groupe n'existe pas ou vous n'en êtes pas administrateur");
            $page->changeTpl('groups/no_group.tpl');
        }
    }

    function handler_group_ajax_admin_users($page)
    {
        $group = Group::fromId(Json::i('gid'));
        $limit = 10;
        $total = 0;
        $users = false;
        if ($group) {
            $users = array();

            $order = new UFO_Name(UFO_Name::LASTNAME);

            $filters = array();
            $rights = Rights::everybody();
            if (Json::s('rights', '') != '') {
                $rights = new Rights(Json::s('rights'));
            }
            $filters[] = new UFC_Group($group, $rights);
            if (Json::t('promo', '') != '') {
                $filters[] = new UFC_Group(explode(';', Json::v('promo')));
            }
            if (Json::t('name', '') != '') {
                $filters[] = new UFC_Name(Json::t('name'), UFC_Name::LASTNAME|UFC_Name::FIRSTNAME|UFC_Name::NICKNAME, UFC_Name::CONTAINS);
            }

            $uf = new UserFilter(new PFC_And($filters), $order);
            $users = $uf->get(new PlLimit($limit, (Json::i('page', 1) - 1) * $limit));
            $total = $uf->getTotalCount();

            $export = array();
            if ($users->count() > 0) {
                $users->select(UserSelect::base());

                /*
                 * Fetching rights
                 */
                $users_rights = $group->selectRights($users);

                /*
                 * Fetching comments
                 */
                $users_comments = array();
                $iter = XDB::iterRow('SELECT  uid, comment
                                        FROM  users_comments
                                       WHERE  gid = {?} AND uid IN {?}',
                                              $group->id(), $users->ids());

                while (list($uid, $comment) = $iter->next()) {
                    $users_comments[$uid] = $comment;
                }

                /*
                 * Exporting
                 */
                $visible_rights = array(Rights::admin(), Rights::member(), Rights::friend());
                $page->assign('defaultrights', $visible_rights);
                foreach ($users as $uid => $u) {
                    $visible = false;
                    foreach($visible_rights as $right)
                        foreach($users_rights[$uid] as $right_user)
                            if($right_user->isMe($right)) $visible = true;
                    if(!$visible) continue;

                    $page->assign('user', $u);
                    $page->assign('rights', (empty($users_rights[$uid])) ? array() : $users_rights[$uid]);
                    $page->assign('comment', (empty($users_comments[$uid])) ? "" : $users_comments[$uid]);

                    $export[$uid] = $page->filteredFetch(FrankizPage::getTplPath('groups/admin_user.tpl'));
                }
            }
        }

        $page->jsonAssign('limit', $limit);
        $page->jsonAssign('total', $total);
        $page->jsonAssign('users', $export);
        return PL_JSON;
    }

    function handler_group_ajax_admin_rights($page)
    {
        S::assert_xsrf_token();

        $group = Group::fromId(Json::i('gid'));
        $user = User::fromId(Json::i('uid'));
        if ($group && $user) {
            if (S::user()->isMe($user) && !S::user()->isAdmin()) {
                $page->jsonAssign('msg', 'On ne peut pas changer ses propres droits');
            } else if (S::user()->hasRights($group, Rights::admin()) || S::user()->isWeb()) {
                $group->select(GroupSelect::subscribe());
                $rights = new Rights(Json::s('rights'));
                $caste = $group->caste($rights);
                if ($caste->userfilter()) {
                    $page->jsonAssign('msg', 'Ce droit est défini de manière logique.');
                } else {

                    // Log the event if involving admin rights
                    if ($rights->isMe(Rights::admin())) {
                        S::logger()->log('groups/admin/rights',
                                         array('gid' => $group->id(),
                                               'uid' => $user->id(),
                                               'cid' => $caste->id(),
                                               'add' => Json::b('add')));
                    }

                    if (Json::b('add')) {
                        $caste->addUser($user);
                    } else {
                        $caste->removeUser($user);
                    }
                }
            }
        }

        return PL_JSON;
    }

    function handler_group_subscribe($page, $group, $right = 'friend')
    {
        S::assert_xsrf_token();
        try {
            $group = Group::from($group);
        } catch (ItemNotFoundException $e) {
            $page->assign('title', "Ce groupe n'existe pas");
            $page->changeTpl('groups/no_group.tpl');
            return;
        }

        if ($right == 'friend') {
            $group->select(GroupSelect::subscribe());

            $group->caste(Rights::friend())->addUser(S::user());
            S::user()->select(UserSelect::castes());

            pl_redirect('groups/see/' . $group->name());
            exit;
        } elseif ($right == 'member') {
            $group->select(GroupSelect::subscribe());

            if ($group->caste(Rights::member())->userfilter()) {
                throw new Exception('You can\'t apply to this group');
            }

            $iv = new MemberValidate(S::user(), $group);
            $v = new Validate(array(
                'writer'  => S::user(),
                'group'   => $group,
                'item'    => $iv,
                'type'    => 'member'));
            $v->insert();
            $page->assign('title', "Demande de statut de membre");
            $page->changeTpl('groups/member.tpl');
        }
    }

    function handler_group_unsubscribe($page, $group)
    {
        S::assert_xsrf_token();
       try {
            $group = Group::from($group);
        } catch (ItemNotFoundException $e) {
            $page->assign('title', "Ce groupe n'existe pas");
            $page->changeTpl('groups/no_group.tpl');
            return;
        }

        $group->select(GroupSelect::subscribe());

        if ($group->leavable()) {
            $group->removeUser(S::user());
            S::user()->select(UserSelect::castes());
        }

        pl_redirect('groups/see/' . $group->name());
        exit;
    }

    function handler_ajax_comment($page)
    {
        S::assert_xsrf_token();

        $g = Group::fromId(Json::i('gid'));
        if ($g) {
            $user = (Json::has('uid')) ? new User(Json::i('uid')) : S::user();

            if ($user->isMe(S::user()) || S::user()->hasRights($g, Rights::admin()) || S::user()->isWeb()) {
                $comments = Json::t('comments');
                $user->comments($g, $comments);
                $page->jsonAssign('uid', $user->id());
            }
        } else {
            $page->jsonAssign('error', "Ce groupe n'existe pas");
        }

        return PL_JSON;
    }

    function handler_group_insert($page)
    {
        $group = new Group();
        $group->insert();
        $group->caste(Rights::admin())->addUser(S::user());
        S::logger()->log("groups/insert", array('gid' => $group->id()));
        pl_redirect('groups/admin/' . $group->name());
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
