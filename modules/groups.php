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
            'groups'                  => $this->make_hook('groups',            AUTH_PUBLIC),
            'groups/ajax/search'      => $this->make_hook('ajax_search',       AUTH_PUBLIC),
            'groups/ajax/insert'      => $this->make_hook('ajax_insert',       AUTH_COOKIE),
            'groups/ajax/rename'      => $this->make_hook('ajax_rename',       AUTH_COOKIE),
            'groups/ajax/delete'      => $this->make_hook('ajax_delete',       AUTH_COOKIE),
            'groups/see'              => $this->make_hook('group_see',         AUTH_PUBLIC),
            'groups/ajax/users'       => $this->make_hook('group_ajax_users',  AUTH_INTERNAL, ''),
            'groups/admin'            => $this->make_hook('group_admin',       AUTH_PUBLIC),
            'groups/subscribe'        => $this->make_hook('group_subscribe',   AUTH_COOKIE),
            'groups/unsubscribe'      => $this->make_hook('group_unsubscribe', AUTH_COOKIE),
            'groups/insert'           => $this->make_hook('group_insert',      AUTH_COOKIE),
            'groups/ajax/comment'     => $this->make_hook('ajax_comment',      AUTH_COOKIE),
        );
    }

    function handler_groups($page)
    {
        $except = new PFC_True();

        $max = 10;

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
        $conditions = new PFC_And(new GFC_Namespace(Json::s('ns')),
                                  new PFC_OR(new GFC_Label(Json::s('token'), GFC_Label::CONTAINS),
                                             new GFC_Name(Json::s('token'))));

        if ($json->order == 'name') {
            $order = new GFO_Name(true);
        } else {
            $order = new GFO_Score(true);
        }
        $own = new GroupFilter(new PFC_And($conditions, new GFC_User(S::user()->id())), $order);
        $all = new GroupFilter($conditions, $order);
        $own = $own->get(new PlLimit(5));
        $all = $all->get(new PlLimit(5));

        $all->merge($own)->select(GroupSelect::base());
        $all->order($json->order);

        $page->jsonAssign('success', true);
        $page->jsonAssign('groups', $all->export());

        return PL_JSON;
    }

    function handler_group_see($page, $group)
    {
        global $globals;

        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group) {
            // Fetch the group
            $group->select(GroupSelect::base());
            $page->assign('group', $group);

            if (S::i('auth') > AUTH_PUBLIC || $group->external()) {
                $group->select(GroupSelect::see());

                // Current promos ?
                foreach (json_decode($globals->core->promos) as $promo) {
                    $groupes_names[] = 'promo_' . $promo;
                }
                $promos = new Collection('Group');
                $promos->add($groupes_names)->select(GroupSelect::base());
                $page->assign('promos', $promos);

                // Relation between the user & the group
                $page->assign('user', S::user());

                // Fetch the news
                /*$nf = new NewsFilter(new PFC_And(new NFC_Origin($group),
                                                 new NFC_Target(S::user()->castes())), new NFO_End(true));
                $news = $nf->get()->select();
                $page->assign('news', $news);*/
                $page->assign('news', array());
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

        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        $users = false;
        if ($group) {
            $users = array();

            if (strlen(Json::t('promo')) > 0) {
                $group->select(GroupSelect::castes());

                $filters = new UFC_Group(explode(';', Json::v('promo')));

                $uf = new UserFilter(new PFC_And(new UFC_Caste($group->caste(Rights::admin())), $filters));
                $admins = $uf->get();

                $uf = new UserFilter(new PFC_And(new UFC_Caste(array($group->caste(Rights::member()), $group->caste(Rights::logic()))), $filters));
                $members = $uf->get();

                $uf = new UserFilter(new PFC_And(new UFC_Caste($group->caste(Rights::friend())), $filters));
                $friends = $uf->get();

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

                $users['admin'] = $admins_export;
                $users['member'] = $members_export;
                $users['friend'] = $friends_export;
            }
        }

        $page->jsonAssign('users', $users);
        return PL_JSON;
    }

    function handler_group_admin($page, $group)
    {
        $filter = (Group::isId($group)) ? new GFC_Id($group) : new GFC_Name($group);
        $gf = new GroupFilter($filter);
        $group = $gf->get(true);

        if ($group && S::user()->hasRights($group, Rights::admin())) {
            $group->select(GroupSelect::see());
            $page->assign('group', $group);

            if (Env::has('name') && S::user()->perms->hasFlag('admin')) {
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

            $page->assign('title', 'Administration de "' . $group->label() . '"');
            $page->addCssLink('groups.css');
            $page->changeTpl('groups/admin.tpl');
        } else {
            $page->assign('title', "Ce groupe n'existe pas ou vous n'en êtes pas administrateur");
            $page->changeTpl('groups/no_group.tpl');
        }
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
            $comments = Json::t('comments');
            S::user()->comments($g, $comments);
            $page->jsonAssign('uid', S::user()->id());
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
