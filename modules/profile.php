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

class ProfileModule extends PLModule
{
    public function handlers()
    {
        return array('profile/account'                 => $this->make_hook('account',                 AUTH_MDP),
                     'profile/mails'                   => $this->make_hook('mails',                   AUTH_COOKIE),
                     'profile/password'                => $this->make_hook('password',                AUTH_MDP),
                     'profile/recovery'                => $this->make_hook('recovery',                AUTH_PUBLIC),
                     'profile/network'                 => $this->make_hook('network',                 AUTH_COOKIE),
                     'profile/skin'                    => $this->make_hook('skin',                    AUTH_PUBLIC),
                     'profile/skin/unsmartphone'       => $this->make_hook('skin_unsmartphone',       AUTH_PUBLIC),
                     'profile/skin/resmartphone'       => $this->make_hook('skin_resmartphone',       AUTH_PUBLIC),
                     'profile/rss'                     => $this->make_hook('rss',                     AUTH_COOKIE),
                     'profile/rss/update'              => $this->make_hook('rss_update',              AUTH_COOKIE),
                     'profile/rss/add'                 => $this->make_hook('rss_add',                 AUTH_COOKIE),
                     'profile/minimodules'             => $this->make_hook('minimodules',             AUTH_COOKIE),
                     'profile/minimodules/ajax/layout' => $this->make_hook('ajax_minimodules_layout', AUTH_COOKIE),
                     'profile/minimodules/ajax/add'    => $this->make_hook('ajax_minimodules_add',    AUTH_COOKIE),
                     'profile/minimodules/ajax/remove' => $this->make_hook('ajax_minimodules_remove', AUTH_COOKIE)
                    );
    }

    public function handler_account($page)
    {
        $err = array();
        $msg = array();

        S::user()->select(UserSelect::login());

        if (Env::has('change_profile')) {

            if (Env::has('image')) {
                $group = Group::from('tol')->select(GroupSelect::castes());

                $image = new ImageFilter(new PFC_And(new IFC_Id(Env::i('image')), new IFC_Temp()));
                $image = $image->get(true);
                if (!$image) {
                    throw new Exception("This image doesn't exist anymore");
                }
                $image->select(FrankizImageSelect::caste());
                $image->label(S::user()->fullName());
                $image->caste($group->caste(Rights::everybody()));
                $tv = new TolValidate($image, S::user());
                $v = new Validate(array(
                    'writer' => S::user(),
                    'group'  => $group,
                    'item'   => $tv,
                    'type'   => 'tol'));
                $v->insert();
                $msg[] = 'La demande de changement de photo tol a été prise en compte.
                    Les tolmestres essaieront de te la valider au plus tôt.';
            }

            S::user()->nickname(Env::t('nickname'));
            S::user()->email(Env::t('bestalias'));
            S::user()->cellphone(new Phone(Env::t('cellphone')));
            S::user()->email_format((Env::t('format')=='text') ? User::FORMAT_TEXT : User::FORMAT_HTML);
            S::user()->comment(Env::t('comment'));
        }

        if (Env::has('options')) {
            $groups = new Collection('Group');
            $gids = explode(';', Env::s('promo'));
            if (count($gids) > 0) {
                $groups->add($gids);
            }
            $groups->select(GroupSelect::base());
            S::user()->defaultFilters($groups);
        }

        if (!empty($err)) {
            $page->assign('err', $err);
        }
        if (!empty($msg)) {
            $page->assign('msg', $msg);
        }
        $page->assign('user', S::user());
        $page->addCssLink('profile.css');
        $page->assign('title', "Changement du profil");
        $page->changeTpl('profile/account.tpl');
    }

    public function handler_mails($page)
    {
        // TODO: use the forlife corresponding to x.edu instead of the hruid
        $forlife = S::user()->login();

        try {
            $xorgIsRegistered = xorgAPI::isRegistered($forlife);
        } catch (xorgUnkonwnUserException $e) {
            $xorgIsRegistered = null;
        }
        $page->assign('xorgRegistered', $xorgIsRegistered);
        $page->assign('user', S::user());
        $page->assign('title', 'Mes mails');
        $page->changeTpl('profile/mails.tpl');
    }

    public function handler_password($page)
    {
        $err = array();
        $msg = array();

        if (Env::has('new_passwd')) {
            if (Env::s('new_passwd1') != Env::s('new_passwd2')) {
                $err[] = 'Les mots de passe donnés sont incohérents.';
            } else if(strlen(Env::s('new_passwd1')) < 6) {
                $msg[] = 'Le mot de passe est trop court.';
            } else {
                S::user()->password(Env::s('new_passwd1'));
                $msg[] = 'Le mot de passe a été changé avec succès.';
            }
        }

        $page->assign('err', $err);
        $page->assign('msg', $msg);

        $page->addCssLink('profile.css');
        $page->assign('title', 'Modification du mot de passe');
        $page->changeTpl('profile/password.tpl');
    }


    function handler_skin($page)
    {
        if (Env::v('skin', '') != '')
        {
            S::set('skin', Env::v('skin'));
            if (S::logged())
                S::user()->skin(Env::v('skin'));
        }

        $res = XDB::query('SELECT  s.name, s.label, s.description, COUNT(a.skin) frequency
                             FROM  skins AS s
                        LEFT JOIN  account AS a ON a.skin = s.name
                            WHERE  s.visibility = 1
                         GROUP BY  s.name
                         ORDER BY  frequency DESC');
        $skins = $res->fetchAllAssoc();

        $total = 0;
        foreach ($skins as $skin)
            $total += $skin['frequency'];

        $page->assign('total', $total);
        $page->assign('skinsList', $skins);
        $page->assign('title', "Modification de l'habillage");
        $page->addCssLink('profile.css');
        $page->changeTpl("profile/skins.tpl");
    }

    function handler_skin_unsmartphone($page, $url)
    {
        S::set('skin', S::user()->skin());
        pl_redirect($url);
        exit;
    }

    function handler_skin_resmartphone($page, $url)
    {
        global $globals;

        S::set('skin', $globals->smartphone_skin);
        pl_redirect($url);
        exit;
    }

    function handler_network($page)
    {
        $rooms = S::user()->rooms();
        $rooms->select(RoomSelect::ips());
        $page->assign('rooms', $rooms);

        $page->assign('title', "Mes données réseau");
        $page->addCssLink('profile.css');
        $page->changeTpl("profile/network.tpl");
    }

    public function handler_recovery($page)
    {
        global $globals;

        $page->addCssLink('profile.css');
        $page->changeTpl('profile/recovery.tpl');
        $page->assign('title', 'Nouveau mot de passe');
        
        // Step 1 : Ask the email
        $page->assign('step', 'ask');

        // Step 2 : Send the recovery mail
        if (Env::t('mail','') != '')
        {
            // TODO: Accept forlife too
            list($forlife, $domain) = explode('@', Env::t('mail'), 2);
            $uf = new UserFilter(new UFC_Forlife($forlife, $domain));
            $user = $uf->get(true);
            if (!$user) {
                $page->assign('error', 'true');
                return;
            }
            $user->select(UserSelect::base());

            $page->assign('email', Env::t('mail'));
            $mail = new FrankizMailer('profile/recovery.mail.tpl');

            $hash = rand_url_id();
            $user->hash($hash);

            $mail->assign('hash', $hash);
            $mail->assign('uid', $user->id());
            $mail->SetFrom($globals->mails->web, 'Les Webmestres de Frankiz');
            $mail->AddAddress($user->bestEmail(), $user->displayName());
            $mail->subject('[Frankiz] Changement de mot de passe');

            $mail->Send($user->isEmailFormatHtml());

            $page->assign('step', 'mail');
        }

        // Step 2 : Send a new password
        if (Env::v('hash','') != '' && Env::v('uid','') != '')
        {
            $user = new User(Env::v('uid'));
            $user->select(UserSelect::base());
            if (Env::v('hash') == $user->hash())
            {
                // TODO: log the session opening
                $mail = new FrankizMailer('profile/recovery_new.mail.tpl');
                $new = rand_url_id();
                $user->hash('');
                $user->password($new);
                $mail->assign('new_password', $new);
                $mail->SetFrom($globals->mails->web, 'Les Webmestres de Frankiz');
                $mail->AddAddress($user->bestEmail(), $user->displayName());
                $mail->subject('[Frankiz] Nouveau mot de passe');

                $mail->Send($user->isEmailFormatHtml());
                $page->assign('step', 'password');
            } else {
                $page->assign('step', 'expired');
            }
        }
    }

    function handler_rss($page)
    {
        if (Env::v('act_rss') == 'Activer') {
            $hash_rss = rand_url_id(16);
            S::user()->hash_rss($hash_rss);
            $page->assign('success', true);
        }

        $page->assign('user', S::user());
        $page->assign('title', 'Flux RSS');
        $page->addCssLink('profile.css');
        $page->changeTpl('profile/filrss.tpl');
    }

    function handler_minimodules($page)
    {
        $iter = XDB::iterator('SELECT  m.name, m.label, m.description, COUNT(um.name) frequency
                                 FROM  minimodules AS m
                            LEFT JOIN  users_minimodules AS um ON m.name = um.name
                             GROUP BY  m.name
                             ORDER BY  frequency DESC');

        $user_minimodules  = S::user()->minimodules();
        $minimodules = array();
        while ($minimodule = $iter->next()) {
            $m = FrankizMiniModule::get($minimodule['name'], false);
            if ($m!== false && $m->checkAuthAndPerms())
            {
                $minimodules[] = array('activated' => in_array($minimodule['name'], $user_minimodules),
                                       'frequency' => $minimodule['frequency'],
                                            'name' => $minimodule['name'],
                                           'label' => $minimodule['label'],
                                     'description' => $minimodule['description']);
            }
        }

        $totalf = new UserFilter(null);
        $total = $totalf->getTotalCount();

        $page->assign('title', 'Gestion des minimodules');
        $page->assign('total', $total);
        $page->assign('minimodules', $minimodules);
        $page->addCssLink('profile.css');
        $page->changeTpl('profile/minimodules.tpl');
    }

    function handler_ajax_minimodules_layout($page)
    {
        $layout = FrankizMiniModule::emptyLayout();

        foreach(array_keys($layout) as $col) {
            if (Json::has($col)) {
                $layout[$col] = Json::v($col);
            }
        }

        if (!S::user()->layoutMinimodules($layout)) {
            $page->jsonAssign('error', "Le réagencement des minimodules n'a pas pu se faire");
        }

        return PL_JSON;
    }

    function handler_ajax_minimodules_add($page)
    {
        $json = json_decode(Env::v('json'));

        $m = FrankizMiniModule::get($json->name);
        $row = S::user()->addMinimodule($m);

        if ($row === false) {
            $page->jsonAssign('error', "Impossible d'activer le minimodule");
        } else {
            $page->jsonAssign('name', $m->name());
            $page->jsonAssign('css' , FrankizPage::getCssPath($m->css()));
            $page->assign('minimodule', $m);
            $page->jsonAssign('html', $page->fetch(FrankizPage::getTplPath('minimodule.tpl')));
        }

        return PL_JSON;
    }

    function handler_ajax_minimodules_remove($page)
    {
        $json = json_decode(Env::v('json'));

        $m = FrankizMiniModule::get($json->name);
        $success = S::user()->removeMinimodule($m);

        if (!$success) {
            $page->jsonAssign('error', "Impossible de désactiver le minimodule");
        }

        return PL_JSON;
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
