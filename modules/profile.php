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
                     'profile/fkz'                     => $this->make_hook('fkz',                     AUTH_COOKIE),
                     'profile/mails'                   => $this->make_hook('mails',                   AUTH_COOKIE),
                     'profile/password'                => $this->make_hook('password',                AUTH_MDP),
                     'profile/fkz/change_tol'          => $this->make_hook('fkz_change_tol',          AUTH_COOKIE),
                     'profile/fkz/mod_binets'          => $this->make_hook('fkz_mod_binets',          AUTH_COOKIE),
                     'profile/recovery'                => $this->make_hook('recovery',                AUTH_PUBLIC),
                     'profile/network'                 => $this->make_hook('network',                 AUTH_COOKIE),
                     'profile/reseau/demande_ip'       => $this->make_hook('demande_ip',              AUTH_COOKIE),
                     'profile/skin'                    => $this->make_hook('skin',                    AUTH_PUBLIC),
                     'profile/skin/unsmartphone'       => $this->make_hook('skin_unsmartphone',       AUTH_PUBLIC),
                     'profile/skin/resmartphone'       => $this->make_hook('skin_resmartphone',       AUTH_PUBLIC),
                     'profile/photo'                   => $this->make_hook('photo',                   AUTH_COOKIE),
                     'profile/photo/small'             => $this->make_hook('photo_small',             AUTH_COOKIE),
                     'profile/siteweb/upload'          => $this->make_hook('siteweb_upload',          AUTH_MDP),
                     'profile/siteweb/demande_ext'     => $this->make_hook('siteweb_ext',             AUTH_MDP),
                     'profile/rss'                     => $this->make_hook('rss',                     AUTH_COOKIE),
                     'profile/rss/update'              => $this->make_hook('rss_update',              AUTH_COOKIE),
                     'profile/rss/add'                 => $this->make_hook('rss_add',                 AUTH_COOKIE),
                     'profile/liens_perso'             => $this->make_hook('liens_perso',             AUTH_COOKIE),
                     'profile/liens_perso/add'         => $this->make_hook('liens_perso_add',         AUTH_COOKIE),
                     'profile/liens_perso/del'         => $this->make_hook('liens_perso_del',         AUTH_COOKIE),
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
                $tv = new TolValidate($image);
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
            S::user()->bestEmail(Env::t('bestalias'));
            S::user()->cellphone(new Phone(Env::t('cellphone')));
            S::user()->email_format((Env::t('format')=='text') ? User::FORMAT_TEXT : User::FORMAT_HTML);
            S::user()->comment(Env::t('comment'));
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

    public function handler_fkz($page)
    {
        global $DB_trombino;

        $DB_trombino->query("SELECT eleves.nom, prenom, surnom, login, promo,
                        mail, piece_id, sections.nom as section, cie, commentaire
                               FROM eleves
                      LEFT JOIN sections USING(section_id)
                      WHERE eleve_id = {$_SESSION['uid']}");
        list ($nom, $prenom, $surnom, $login, $promo, $mail, $casert, $section, $cie, $commentaire)
            = $DB_trombino->next_row();

        $page->assign('profil_fkz_nom', $nom);
        $page->assign('profil_fkz_prenom', $prenom);
        $page->assign('profil_fkz_surnom', $surnom);
        $page->assign('profil_fkz_loginpoly', $login);
        $page->assign('profil_fkz_promo', $promo);
        $page->assign('profil_fkz_email', $mail ? $mail : "$login@poly.polytechnique.fr");
        $page->assign('profil_fkz_casert', $casert);
        $page->assign('profil_fkz_section', $section);
        $page->assign('profil_fkz_compagnie', $cie);
        $page->assign('profil_fkz_comment', $commentaire);

        $DB_trombino->query("SELECT binet_id, binets.nom, membres.remarque
                               FROM binets
                  LEFT JOIN membres USING(binet_id)
                      WHERE membres.eleve_id = {$_SESSION['uid']}");
        $binets = array();
        while (list($id, $nom, $commentaire) = $DB_trombino->next_row())
        {
            $binets[] = array('id' => $id,
                              'nom' => $nom,
                      'commentaire' => $commentaire);
        }
        $page->assign('profil_fkz_binets', $binets);

        $DB_trombino->query("SELECT binet_id, nom
                               FROM binets
                   ORDER BY nom ASC");
        $binets_tous = array();
        while (list($id, $nom) = $DB_trombino->next_row())
        {
            $binets[] = array('id' => $id,
                      'nom' => $nom);
        }
        $page->assign('profil_fkz_binets_tous', $binets);

        $page->assign('title', "Modification du profil Frankiz");
        $page->changeTpl('profil/fkz.tpl');
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
        $page->assign('recovery', Env::v('hash','') != '');
        $page->assign('changed', false);

        if (Env::has('new_password'))
        {
            S::user()->password(Env::v('new_password'));
            $page->assign('changed', true);
        }

        $page->assign('title', 'Modification du mot de passe');
        $page->changeTpl('profile/password.tpl');
    }

    public static function handler_fkz_change_tol($page)
    {
        global $DB_trombino;

        if (strlen($_POST['surnom']) < 2 && !empty($_POST['surnom']))
        {
            $page->append("profil_tol_results",
                      array('type' => 'erreur',
                            'text' => "Le surnom choisi est trop court."));
            $erreur = true;
        }
        if (strlen($_POST['surnom']) > 32)
        {
            $page->append("profil_tol_results",
                      array('type' => 'erreur',
                            'text' => "Le surnom choisi est trop long."));
            $erreur = true;
        }
        if(!ereg("^[a-zA-Z0-9_+.-]+@[a-zA-Z0-9.-]+$",$_POST['email']) && !empty($_POST['email']))
        {
            $page->append("profil_tol_results",
                      array('type' => 'erreur',
                            'text' => "Email non valide."));
            $erreur = true;
        }

        if (!$erreur)
        {
            if ($_POST['email'] == "$login@poly" || $_POST['email'] == "$login@poly.polytechnique.fr")
                $mail = "NULL";
            else
                $mail = "'{$_POST['email']}'";

            $DB_trombino->query("UPDATE eleves
                                    SET surnom = '{$_POST['surnom']}', mail = $mail
                          WHERE eleve_id='{$_SESSION['uid']}'");

            $page->append("profil_tol_results",
                      array('type' => 'commentaire',
                            'text' => "L'email et le surnom ont été modifiés."));
        }

        if ($_FILE['file']['tmp_name'] != '')
        {
            $original_size = getimagesize($_FILES['file']['tmp_name']);
            if ($original_size && $original_size[0] <= 300 && $original_size[1] <= 400)
            {
                if (file_exists($filename))
                {
                    $page->append("profil_tol_results",
                              array('type' => 'warning',
                                    'text' => "Tu avais déja demandé une modification de photo, seule la demande que tu viens de poster sera prise en compte."));
                }
                else
                {
                    $page->append("profil_tol_results",
                              array('type' => 'commentaire',
                                    'text' => "Ta demande de changement de photo a été prise en compte et sera validée dans les meilleurs délais."));
                    $contenu = "$nom $prenom ($promo) a demandé la modification de son image trombino <br><br>".
                        "Pour valider ou non cette demande va sur la page suivante : <br>".
                        "<div align='center'><a href='".BASE_URL."admin/valid_trombi.php'>".
                        BASE_URL."admin/valid_trombi.php</a></div><br><br>".
                        "Cordialement,<br>".
                        "Le Tolmestre<br>";

                    couriel (TROMBINOMEN_ID,
                             "[Frankiz] Modification de l'image trombi de $nom $prenom",
                         $contenu,
                         $_SESSION['uid']);
                }
                move_uploaded_file($_FILES['file']['tmp_name'], $filename) ;
            }
            else
            {
                $page->append("profil_tol_results",
                              array('type' => 'erreur',
                                'text' => "Ton image n'est pas au bon format, ou est trop grande."));
            }
        }

        $this->handler_fkz($page);
    }

    public function handler_fkz_mod_binets($page)
    {
        if (isset($_POST['mod_binet']))
            $this->handler_fkz_change_binet($page);
        if (isset($_POST['suppr_binet']))
            $this->handler_fkz_suppr_binet($page);
        if (isset($_POST['add_binet']))
            $this->handler_fkz_ajout_binet($page);

        $this->handler_fkz($page);
    }

    private function handler_fkz_change_binet($page)
    {
        global $DB_trombino;

        foreach ($_POST['commentaire'] as $key => $val)
        {
            $DB_trombino->query("UPDATE membres
                                    SET remarque = '$val'
                              WHERE eleve_id = '{$_SESSION['uid']}' AND binet_id = '$key'");
        }
        $DB_trombino->query("UPDATE eleves
                                SET commentaire = '{$_POST['perso']}'
                      WHERE eleve_id='{$_SESSION['uid']}'");

        $page->append("profil_tol_results",
                  array('type' => 'commentaire',
                        'text' => "Modification de la partie binets effectuée avec succès."));

    }

    private function handler_fkz_suppr_binet($page)
    {
        global $DB_trombino;

        $count = 0;
        if (isset($_POST['elements']))
        {
            $ids = "";
            foreach ($_POST['elements'] as $id => $on) {
                if ($on == 'on')
                    $ids .= (empty($ids) ? "" : ",") . "'$id'";
                $count++;
            }
        }

        if ($count>=1)
        {
            $DB_trombino->query("DELETE FROM membres
                                       WHERE binet_id IN ($ids) AND eleve_id='{$_SESSION['uid']}'");
            $page->append('fkz_tol_results',
                          array('type' => 'commentaire',
                            'text' => "Suppression de $count binet(s)"));
        }
        else
        {
            $page->append('fkz_tol_results',
                      array('type' => 'commentaire',
                            'text' => "Aucun binet n'est sélectionné. Aucun binet n'a donc été supprimé de la liste de tes binets."));
        }
    }

    private function handler_fkz_ajout_binet($page)
    {
        global $DB_trombino;

        if ($_POST['liste_binet'] != 'default')
        {
            $DB_trombino->query("REPLACE INTO membres
                                          SET eleve_id='{$_SESSION['uid']}',binet_id='{$_POST['liste_binet']}'");
            $page->append('fkz_tol_results',
                      array('type' => 'commentaire',
                            'text' => 'Binet correctement ajouté'));
        }
        else
        {
            $page->append('fkz_tol_results',
                      array('type' => 'warning',
                            'text' => "Aucun binet sélectionné. Aucun binet n'a donc été ajouté à la liste de tes binets."));
        }
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

    function handler_photo($page, $hruid)
    {
        $uf = new UserFilter(new UFC_Hruid($hruid));
        $user = $uf->getUser()->select(User::SELECT_BASE);
        $user->image()->select(FrankizImage::SELECT_FULL)->send();
    }

    function handler_photo_small($page, $hruid)
    {
        $uf = new UserFilter(new UFC_Hruid($hruid));
        $user = $uf->getUser()->select(User::SELECT_BASE);
        $user->image()->select(FrankizImage::SELECT_SMALL)->send();
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

    public function handler_demande_ip($page)
    {
        global $DB_valid;

        //////
        // Vérification que l'utilisateur n'a pas déja une demande en attente
        //
        $DB_valid->query("SELECT 0 FROM valid_ip WHERE eleve_id = '{$_SESSION['uid']}'");
        $demande_en_cours = ($DB_valid->num_rows() > 0);

        //////
        // Mise en place des variables Smarty
        //
        $page->changeTpl('profil/demande_ip.tpl');
        $page->assign('title', "Demande de nouvelle adresse IP");
        $page->assign('demande_en_cours', $demande_en_cours);
        $page->assign('nouvelle_demande', 0);

        //////
        // Traitement d'une demande éventuelle
        //
        if (!empty($_POST['demander']) && !$demande_en_cours)
        {
            $DB_valid->query("INSERT  valid_ip
                                 SET  type = '{$_POST['type']}',
                              raison = '{$_POST['raison']}',
                          eleve_id = '{$_SESSION['uid']}'");

            if ($_POST['type'] == 1)
                $raison = "J'ai installé un 2ème ordinateur dans mon casert et je souhaite avoir une nouvelle adresse IP pour cette machine.";
            else
                $raison = $_POST['raison'];

            $mail = new PlMailer('profil/demande_ip.mail.tpl');
            $mail->assign('nom', $_SESSION['nom']);
            $mail->assign('prenom', $_SESSION['prenom']);
            $mail->assign('raison', $raison);
            $mail->send();

            $page->assign('nouvelle_demande', 1);
        }
    }

    public function handler_recovery($page)
    {
        $page->addCssLink('profile.css');
        $page->changeTpl('profile/recovery.tpl');
        $page->assign('title', 'Nouveau mot de passe');
        
        // Step 1 : Ask the email
        $page->assign('step', 'ask');

        // Step 2 : Send the recovery mail
        if (Env::v('mail','') != '')
        {
            // TODO: Accept forlife too
            $uf = new UserFilter(new UFC_Bestalias(Env::v('mail')));
            $user = $uf->get(true);
            if (!$user) {
                $page->assign('error', 'true');
                return;
            }
            $user->select(User::SELECT_BASE);

            $page->assign('email', $user->bestEmail());
            $mail = new FrankizMailer('profile/recovery.mail.tpl');

            $hash = rand_url_id();
            $user->hash($hash);

            $mail->assign('hash', $hash);
            $mail->assign('uid', $user->id());
            $mail->SetFrom('web@frankiz.polytechnique.fr', 'Les Webmestres de Frankiz');
            $mail->AddAddress($user->bestEmail(), $user->displayName());
            $mail->subject('[Frankiz] Changement de mot de passe');

            $mail->Send($user->isEmailFormatHtml());

            $page->assign('step', 'mail');
        }

        // Step 2 : Send a new password
        if (Env::v('hash','') != '' && Env::v('uid','') != '')
        {
            $user = new User(Env::v('uid'));
            $user->select(array(User::SELECT_BASE => array()));
            if (Env::v('hash') == $user->hash())
            {
                // TODO: log the session opening
                $mail = new FrankizMailer('profile/recovery_new.mail.tpl');
                $new = rand_url_id();
                $user->hash('');
                $user->password($new);
                $mail->assign('new_password', $new);
                $mail->SetFrom('web@frankiz.polytechnique.fr', 'Les Webmestres de Frankiz');
                $mail->AddAddress($user->bestEmail(), $user->displayName());
                $mail->subject('[Frankiz] Nouveau mot de passe');
    
                $mail->Send($user->isEmailFormatHtml());
                $page->assign('step', 'password');
            } else {
                $page->assign('step', 'expired');
            }
        }
    }

    public function handler_siteweb($page)
    {
        $page->changeTpl('profil/siteweb.tpl');
        $page->assign('title', "Gestion du site web personnel");
    }

    public function handler_siteweb_upload($page)
    {
        $page->changeTpl('profil/siteweb.tpl');
        $page->assign('title', 'Upload de site web');

        if (!isset($_FILES['file']) || !$_FILES['file']['name'])
            return;

        $chemin = BASE_PAGESPERSOS."{$_SESSION['loginpoly']}-{$_SESSION['promo']}";
        deldir($chemin, WEBPERSO_USER);

        $filename = "/tmp/{$_SESSION['loginpoly']}-{$_SESSION['promo']}-{$_FILES['file']['name']}";
        move_uploaded_file($_FILES['file']['tmp_name'], $filename);
        chmod($filename, 0640);
        chgrp($filename, WEBPERSO_GROUP);
        unzip($filename, $chemin, true, WEBPERSO_USER);

        $page->assign('siteweb_updated', 1);
    }

    public function handler_siteweb_download($page)
    {
        global $platal, $globals;

        $download_type = $platal->argv[1];
        $chemin = "{$globals->paths->pagespersos}/{$_SESSION['loginpoly']}-{$_SESSION['promo']}";

        if (is_dir($chemin))
        {
            download($chemin, $download_type, "siteweb-{$_SESSION['loginpoly']}-{$_SESSION['promo']}");
            return PL_NOT_FOUND; // Peut mieux faire...
        }
        else
            $page->trig("Il n'y a aucun fichier sur ton site web");

        $page->changeTpl('profil/siteweb.tpl');
        $page->assign('title', "Echec du téléchargement");
    }

    public function handler_siteweb_ext($page)
    {
        global $DB_valid, $DB_web;

        $page->changeTpl('profil/siteweb.tpl');
        $page->assign('title', "Demande d'acces extérieur");

        $DB_valid->query("SELECT id FROM valid_pageperso WHERE eleve_id='{$_SESSION['uid']}'");
        if ($DB_valid->num_rows() > 0)
        {
            $page->trig("Tu as déja demandé que ton site soit accessible depuis l'extérieur. Ta demande sera validée dans les meilleurs délais.");
            return;
        }

        $DB_web->query("SELECT site_id FROM sites_eleves WHERE eleve_id='{$_SESSION['uid']}'");
        if ($DB_valid->num_rows() > 0)
        {
            $page->trig("Ton site est déja accessible depuis l'extérieur.");
            return;
        }

        $DB_valid->query("INSERT INTO valid_pageperso SET eleve_id='{$_SESSION['uid']}'");

        $mail = new PlMailer('profil/siteweb_ext.mail.tpl');
        $mail->assign('nom', $_SESSION['nom']);
        $mail->assign('prenom', $_SESSION['prenom']);
        $mail->send();

        $page->assign('demande_ext', 1);
    }

    public function handler_rss_update($page)
    {
        for ($i = 0; $i < $_REQUEST['nbr_rss']; $i++)
        {
            if (!isset($_REQUEST["rss_lien_$i"]))
                break;

            $rss_lien = $_REQUEST["rss_lien_$i"];

            $_SESSION['liens_rss'][$rss_lien]['module'] = !empty($_REQUEST["rss_module_$i"]);
            $_SESSION['liens_rss'][$rss_lien]['sommaire'] = !empty($_REQUEST["rss_sommaire_$i"]);
            $_SESSION['liens_rss'][$rss_lien]['complet'] = !empty($_REQUEST["rss_complet_$i"]);

            if (!empty($_REQUEST["rss_del_$i"]))
                unset ($_SESSION['liens_rss'][$rss_lien]);
        }
        FrankizSession::save_liens_rss();

        $page->assign('rss_update', 1);
        $this->handler_rss($page);
    }

    public function handler_rss_add($page)
    {
        $_SESSION['liens_rss'][$_REQUEST['rss_lien_add']] = array('description' => $_REQUEST['rss_lien_add'],
                                      'module'      => 0,
                                      'sommaire'    => 0,
                                      'complet'     => 0);
        FrankizSession::save_liens_rss();

        $page->assign('rss_add', 1);
        $this->handler_rss($page);
    }

    public function handler_rss_old($page)
    {
        global $DB_web;

        $DB_web->query("SELECT  url, description
                  FROM  liens_rss");

        $nodelete = array();
        while (list($url, $description) = $DB_web->next_row())
        {
            $nodelete[$url] = 1;

            if (!is_array($_SESSION['liens_rss'][$url]))
                $_SESSION['liens_rss'][$url] = array();

            if (!isset($_SESSION['liens_rss'][$url]['module']))
                $_SESSION['liens_rss'][$url]['module'] = 0;

            if (!isset($_SESSION['liens_rss'][$url]['sommaire']))
                $_SESSION['liens_rss'][$url]['sommaire'] = 0;

            if (!isset($_SESSION['liens_rss'][$url]['complet']))
                $_SESSION['liens_rss'][$url]['complet'] = 0;

            $_SESSION['liens_rss'][$url]['description'] = $description;
        }

        $page->changeTpl('profil/rss.tpl');
        $page->assign('title', "Gestion des flux rss");
        $page->assign('nodelete', $nodelete);
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

    public function handler_liens_perso_add($page)
    {
        $page->changeTpl('profil/liens_perso.tpl');
        $page->assign('title', "Ajout d'un lien perso");

        $_SESSION['liens_perso'][$_REQUEST['lien_perso']] = $_REQUEST['lien_perso'];
        FrankizSession::save_liens_perso();
    }

    public function handler_liens_perso_del($page)
    {
        $page->changeTpl('profil/liens_perso.tpl');
        $page->assign('title', "Suppression d'un lien perso");

        unset($_SESSION['liens_perso'][$_REQUEST['lien_perso']]);
        FrankizSession::save_liens_perso();
    }

    public function handler_liens_perso($page)
    {
        $page->changeTpl('profil/liens_perso.tpl');
        $page->assign('title', "Gestion des liens persos");
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
