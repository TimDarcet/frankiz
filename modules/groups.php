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

class GroupsModule extends PLModule
{
    public function handlers()
    {
        return array(
            // Listing of the groups
            'groups'                  => $this->make_hook('groups',            AUTH_PUBLIC),
            'groups/ajax/search'      => $this->make_hook('ajax_search',       AUTH_PUBLIC),
            'groups/ajax/insert'      => $this->make_hook('ajax_insert',       AUTH_COOKIE),
            'groups/ajax/rename'      => $this->make_hook('ajax_rename',       AUTH_COOKIE),
            'groups/ajax/delete'      => $this->make_hook('ajax_delete',       AUTH_COOKIE),

            // Page of a single group
            'groups/see'              => $this->make_hook('group_see',         AUTH_PUBLIC),
            'groups/ajax/users'       => $this->make_hook('group_ajax_users',  AUTH_INTERNAL, ''),
            'groups/ajax/news'        => $this->make_hook('group_ajax_news',   AUTH_PUBLIC),
            'groups/subscribe'        => $this->make_hook('group_subscribe',   AUTH_COOKIE),
            'groups/unsubscribe'      => $this->make_hook('group_unsubscribe', AUTH_COOKIE),

            // Admin page of a group
            'groups/admin'            => $this->make_hook('group_admin',             AUTH_COOKIE),
            'groups/ajax/admin/users' => $this->make_hook('group_ajax_admin_users',  AUTH_COOKIE),
            'groups/ajax/admin/rights'=> $this->make_hook('group_ajax_admin_rights', AUTH_COOKIE),

            // Common handler
            'groups/ajax/comment'     => $this->make_hook('ajax_comment',      AUTH_COOKIE),

            // Admin stuffs
            'groups/insert'           => $this->make_hook('group_insert',      AUTH_MDP),
        );
    }

    function handler_groups($page)
    {
        global $globals;
        $except = new PFC_True();

        $max = $globals->groups->limit;

        // Re-fetch user's groups
        S::user()->select(UserSelect::castes());

        // Fetch samples of other groups
        $binet = new GroupFilter(new PFC_And(new GFC_Namespace(Group::NS_BINET), $except), new GFO_Score(true));
        $binet = $binet->get(new PlLimit($max));

        $study = new GroupFilter(new PFC_And(new GFC_Namespace(Group::NS_STUDY), $except), new GFO_Score(true));
        $study = $study->get(new PlLimit($max));

        $free = new GroupFilter(new PFC_And(new GFC_Namespace(Group::NS_FREE), $except), new GFO_Score(true));
        $free = $free->get(new PlLimit($max));

        // Load associated datas
        $temp = new Collection('Group');
        $temp->merge($binet)->merge($study)->merge($free);
        $temp->select(GroupSelect::base());

        // Fetch the total count of groups
        $allf = new GroupFilter(new GFC_Visible());
        $total = $allf->getTotalCount();

        $user_binet = S::user()->castes()->groups()->filter('ns', Group::NS_BINET)->remove($binet);
        $page->assign('binet', $binet);
        $page->assign('user_binet', $user_binet);

        $user_study = S::user()->castes()->groups()->filter('ns', Group::NS_STUDY)->remove($study);
        $page->assign('study', $study);
        $page->assign('user_study', $user_study);

        $user_free = S::user()->castes()->groups()->filter('ns', Group::NS_FREE)->remove($free);
        $page->assign('free', $free);
        $page->assign('user_free', $user_free);

        $page->assign('user', S::user());
        $page->assign('total', $total);
        $page->assign('title', 'Groupes');
        $page->changeTpl('groups/groups.tpl');
        $page->addCssLink('groups.css');
    }

    function handler_ajax_search($page)
    {
        global $globals;

        $conditions = new PFC_And(new GFC_Namespace(Json::s('ns')),
                                  new PFC_OR(new GFC_Label(Json::s('piece'), GFC_Label::CONTAINS),
                                             new GFC_Name(Json::s('piece'))));

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

        $page->jsonAssign('groups', $export);

        return PL_JSON;
    }

    function handler_group_see($page, $group)
    {
        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group) {
            // Fetch the group
            $group->select(GroupSelect::base());
            $page->assign('group', $group);

            if (S::i('auth') > AUTH_PUBLIC || $group->external()) {
                $group->select(GroupSelect::see());

                $promos = S::user()->castes()->groups()->filter('ns', Group::NS_PROMO);
                $page->assign('promos', $promos);

                // Relation between the user & the group
                $page->assign('user', S::user());

                $page->assign('title', $group->label());
                $page->changeTpl('groups/group.tpl');
            } else {
                $platal->force_login($page);
            }
        } else {
            $page->assign('title', "Ce groupe n'existe pas");
            $page->changeTpl('groups/no_group.tpl');
        }
        $page->addCssLink('groups.css');
    }

    function handler_group_ajax_users($page)
    {
        $group = Json::i('gid');
        $limit = 25;

        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

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
        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

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

    function handler_group_admin($page, $group)
    {
        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group && (S::user()->hasRights($group, Rights::admin()) || S::user()->isAdmin())) {
            $group->select(GroupSelect::see());
            $page->assign('group', $group);

            if (Env::has('name') && S::user()->perms()->hasFlag('admin')) {
                $group->name(Env::t('name'));
            }

            if (Env::has('label')) {
                $group->label(Env::t('label'));
            }

            if (Env::has('description')) {
                $group->description(Env::s('description'));
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
                    $group->ns(Env::t('ns'));
                }
            }

            $promos = S::user()->castes()->groups()->filter('ns', Group::NS_PROMO);
            $page->assign('promos', $promos);

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
        $group = Json::i('gid');
        $limit = 10;

        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

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
            $export = array();
            $page->assign('defaultrights', array(Rights::admin(), Rights::member(), Rights::friend()));
            foreach ($users as $uid => $u) {
                $page->assign('user', $u);
                $page->assign('rights', (empty($users_rights[$uid])) ? array() : $users_rights[$uid]);
                $page->assign('comment', (empty($users_comments[$uid])) ? "" : $users_comments[$uid]);

                $export[$uid] = $page->filteredFetch(FrankizPage::getTplPath('groups/admin_user.tpl'));
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

        $group = Json::i('gid');
        $gf = new GroupFilter((Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group));
        $group = $gf->get(true);

        $uf = new UserFilter(new UFC_Uid(Json::i('uid')));
        $user = $uf->get(true);

        if ($group && $user) {
            if (S::user()->isMe($user) && !S::user()->isAdmin()) {
                $page->jsonAssign('msg', 'On ne peut pas changer ses propres droits');
            } else if (S::user()->hasRights($group, Rights::admin()) || S::user()->isAdmin()) {
                $group->select(GroupSelect::subscribe());
                $caste = $group->caste(new Rights(Json::s('rights')));
                if ($caste->userfilter()) {
                    $page->jsonAssign('msg', 'Ce droit est défini de manière logique.');
                } else {
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

    function handler_group_subscribe($page, $group)
    {
        S::assert_xsrf_token();

        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group) {
            $group->select(GroupSelect::subscribe());

            $group->caste(Rights::friend())->addUser(S::user());
            S::user()->select(UserSelect::castes());

            pl_redirect('groups/see/' . $group->name());
            exit;
        } else {
            $page->assign('title', "Ce groupe n'existe pas");
            $page->changeTpl('groups/no_group.tpl');
        }
    }

    function handler_group_unsubscribe($page, $group)
    {
        S::assert_xsrf_token();

        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group) {
            $group->select(GroupSelect::subscribe());

            if ($group->leavable()) {
                $group->removeUser(S::user());
                S::user()->select(UserSelect::castes());
            }

            pl_redirect('groups/see/' . $group->name());
            exit;
        } else {
            $page->assign('title', "Ce groupe n'existe pas");
            $page->changeTpl('groups/no_group.tpl');
        }
    }

    function handler_ajax_comment($page)
    {
        S::assert_xsrf_token();

        $gf = new GroupFilter(new GFC_Id(Json::i('gid')));
        $g = $gf->get(true);
        if ($g) {
            $user = (Json::has('uid')) ? new User(Json::i('uid')) : S::user();

            if ($user->isMe(S::user()) || S::user()->hasRights($g, Rights::admin())) {
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
        //TODO
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
