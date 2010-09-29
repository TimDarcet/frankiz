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

#require_once BASE_FRANKIZ."htdocs/include/minimodules.inc.php";
#require_once BASE_FRANKIZ."htdocs/include/session.inc.php";
#require_once BASE_FRANKIZ."htdocs/include/transferts.inc.php";

class ProfileModule extends PLModule
{
    public function handlers()
    {
        return array('profile'                         => $this->make_hook('profile',                 AUTH_COOKIE),
                     'profile/fkz'                     => $this->make_hook('fkz',                     AUTH_COOKIE),
                     'profile/password'                => $this->make_hook('password',                AUTH_MDP),
                     'profile/fkz/change_tol'          => $this->make_hook('fkz_change_tol',          AUTH_COOKIE),
                     'profile/fkz/mod_binets'          => $this->make_hook('fkz_mod_binets',          AUTH_COOKIE),
                     'profile/recovery'                => $this->make_hook('recovery',                AUTH_PUBLIC),
                     'profile/reseau'                  => $this->make_hook('reseau',                  AUTH_MDP),
                     'profile/reseau/demande_ip'       => $this->make_hook('demande_ip',              AUTH_COOKIE),
                     'profile/skin'                    => $this->make_hook('skin',                    AUTH_PUBLIC),
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
                     'profile/licences'                => $this->make_hook('licences',                AUTH_MDP),
                     'profile/licences/cluf'           => $this->make_hook('licences_CLUF',           AUTH_MDP),
                     'profile/licences/raison'         => $this->make_hook('licences_raison',         AUTH_MDP),
                     'profile/licences/final'          => $this->make_hook('licences_final',          AUTH_MDP),
                     'profile/minimodules'             => $this->make_hook('minimodules',             AUTH_COOKIE),
                     'profile/minimodules/ajax/layout' => $this->make_hook('ajax_minimodules_layout', AUTH_COOKIE),
                     'profile/minimodules/ajax/add'    => $this->make_hook('ajax_minimodules_add',    AUTH_COOKIE),
                     'profile/minimodules/ajax/remove' => $this->make_hook('ajax_minimodules_remove', AUTH_COOKIE)
                    );
    }

    public function handler_profile($page)
    {
        $page->assign('title', "Modification des préférences");
        $page->changeTpl('profile/index.tpl');
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
                S::user()->skin($skin);
        }

        $res = XDB::query('SELECT  s.skin_id, s.name, s.label, s.description, COUNT(a.skin) frequence
                             FROM  skins AS s
                        LEFT JOIN  account AS a ON a.skin = s.skin_id
                         GROUP BY  s.skin_id
                         ORDER BY  frequence DESC');
        $skins = $res->fetchAllAssoc();

        $page->assign('skinsList', $skins);
        $page->assign('title', "Modification de l'apparence de Frankiz");
        $page->changeTpl("profile/skins.tpl");
    }

    function handler_photo($page, $hruid)
    {
        $uf = new UserFilter(new UFC_Hruid($hruid));
        $user = $uf->getUser();
        $photo = FrankizImage::get($user->bestImage())->send();
        exit;
    }

    function handler_photo_small($page, $hruid)
    {
        $uf = new UserFilter(new UFC_Hruid($hruid));
        $user = $uf->getUser();
        $photo = FrankizImage::get($user->bestImage())->sendSmall();
        exit;
    }

    function handler_reseau($page)
    {
        global $DB_admin, $DB_trombino, $DB_xnet;

        $DB_admin->query("SELECT  ip.piece_id, ip.prise_id, ip.ip
                            FROM  prises AS ip
                   LEFT JOIN  trombino.eleves AS e USING(piece_id)
                       WHERE  e.eleve_id='{$_SESSION['uid']}'
                    ORDER BY  ip.type ASC");

        //////
        // On détermine si une IP de l'élève correspond à l'ordi depuis lequel il se connecte.
        //
        $id_ip = 0;
        $ip = array();
        $prise = array();
        $match_ip = false;
        while (list ($ksert, $prise[$id_ip], $ip[$id_ip]) = $DB_admin->next_row())
        {
            $match_ip = $match_ip || $ip[$id_ip] == $_SERVER['REMOTE_ADDR'];
            $id_ip++;
        }

        //////
        // Mise en place des variables Smarty
        //
        $page->changeTpl('profil/reseau.tpl');
        $page->assign('title', "Modification du profil réseau");
        $page->assign('xnet_mdp_changed', 0);
        $page->assign('xnet_match_ip', $match_ip);
        $page->assign('xnet_ip', array_slice($ip, 0, $id_ip));
        $page->assign('xnet_ip_current', $_SERVER['REMOTE_ADDR']);
        $page->assign('xnet_prise', $prise[0]);

        //////
        // Changement du mot de passe
        //
        if (empty($_POST['passwd']) && empty($_POST['passwd2']) || $_POST['passwd'] == '12345678' && $_POST['passwd2'] == '87654321')
        {
        }
        else if ($_POST['passwd'] != $_POST['passwd2'])
        {
            $page->trig('Les deux mots de passes rentrés ne concordent pas');
        }
        else if (strlen($_POST['passwd']) < 6)
        {
            $page->trig('Ton nouveau mot de passe est trop court');
        }
        else if (!in_array($_POST['ip_xnet'], $ip))
        {
            $page->trig("Un problème de sécurité vient de survenir. Ton mot de passe n'a pas été changé.");
        }
        else
        {
            $pass = md5($_POST['passwd']."Vive le BR");
            $DB_xnet->query("UPDATE  clients
                                SET  password='$pass'
                      WHERE  lastip='{$_POST['ip_xnet']}'");
            $this->assign('xnet_mdp_changed', 1);
        }
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
        global $globals;

        $page->changeTpl('profile/recovery.tpl');
        $page->assign('title', 'Nouveau mot de passe');
        
        // Step 1 : Ask the email
        $page->assign('step', 'ask');

        // Step 2 : Send the recovery mail
        if (Env::v('mail','') != '')
        {
            // TODO: Accept forlife too
            $uf = new UserFilter(new UFC_Bestalias(Env::v('mail')));
            $user = $uf->getUser();

            $page->assign('email', $user->bestEmail());

            $mail = new FrankizMailer('profile/recovery.mail.tpl');

            $hash = rand_url_id();
            $user->hash($hash);

            $mail->assign('hash', $hash);
            $mail->assign('uid', $user->id());
            $mail->SetFrom('web@frankiz.polytechnique.fr', 'Les Webmestres de Frankiz');
            $mail->AddAddress($user->bestEmail(), $user->displayName());

            $mail->Send($user->isEmailFormatHtml());

            $page->assign('step', 'mail');
        }

        // Step 2 : Send a new password
        if (Env::v('hash','') != '' && Env::v('uid','') != '')
        {
            $user = new User(Env::v('uid'));
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

    public function handler_rss($page)
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

    private function licences_logiciels(){
        return array('visualstudio' => 'Visual Studio .NET','winxp' => 'Windows XP Professionnel','winvista' => 'Windows Vista Business','2k3serv' => 'Windows Serveur 2003','2k3access'=>'Access 2003','2k3onenote'=>'One Note 2003','2k3visiopro'=>'Visio Professionnel 2003','win2k'=>'Windows 2000 Professionnel');
    }

    private function licences_admin(){
        global $DB_msdnaa;
        $DB_msdnaa->query("SELECT log, cle FROM cles_admin");
        $licences=array();
        while(list($log, $cle) = $DB_msdnaa->next_row()){
            $licences[$log]=$cle;
        }
        return $licences;
    }

    public function handler_licences($page)
    {
        global $DB_msdnaa;

        $page->changeTpl('profil/licences.tpl');
        $page->assign('title', "Les licences");
        $logiciels = $this->licences_logiciels();
        $cles_admin= $this->licences_admin();
        $page->assign('logiciels', $logiciels);

        // On va lister les licences possédées

        $licences=array();
        $requete="";
        foreach(array_keys($logiciels) as $logiciel){
            if(!in_array($logiciel, array_keys($cles_admin))){
                if($requete!=""){
                    $requete.= " UNION ";
                }
                $requete.=" (SELECT cle, attrib, \"$logiciel\" as logiciel FROM cles_$logiciel WHERE eleve_id='{$_SESSION['uid']}')";
            }
        }
        $DB_msdnaa->query($requete);
        while(list($cle, $attrib, $logiciel) = $DB_msdnaa->next_row()){
            $licences[]=array('cle'=>$cle, 'attrib'=>$attrib, 'nom_logiciel'=>$logiciels[$logiciel]);
        }
        $page->assign('licences', $licences);
    }

    public function handler_licences_CLUF($page){

        $logiciels = $this->licences_logiciels();

        //On a demandé une licence, affichage du CLUF.
        if(isset($_POST['refus']) || !isset($_POST['logiciel']) || !in_array($_POST['logiciel'], array_keys($logiciels))){
            $this->handler_licences($page);
        }else{
            $page->changeTpl('profil/licences_CLUF.tpl');

            $page->assign('title', "Demane de licence pour {$logiciels[$_POST['logiciel']]} : Contrat utilisateur");
            $page->assign('logiciel', $_POST['logiciel']);
            $page->assign('logiciel_nom', $logiciels[$_POST['logiciel']]);
        }
    }

    public function handler_licences_raison($page){
        global $DB_trombino;
        $logiciels = $this->licences_logiciels();

        if(isset($_POST['refus']) || !isset($_POST['accord']) || !isset($_POST['logiciel']) || !in_array($_POST['logiciel'], array_keys($logiciels))){
            $this->handler_licences($page);
        }else{//TODO : Vérif si sur platal, auquel cas oui automatique
            $DB_trombino->query("SELECT  1
                     FROM   trombino.eleves
                    WHERE   eleve_id = '{$_SESSION['uid']}' AND piece_id IS NOT NULL");
            if($DB_trombino->num_rows()>0){
                $_POST['raison']="sur le platal";
                $this->handler_licences_final($page);
            }else{
                $page->changeTpl('profil/licences_raison.tpl');
                $page->assign('title', "Demande de licence pour {$logiciels[$_POST['logiciel']]} : Raison");
                $page->assign('logiciel', $_POST['logiciel']);
                $page->assign('logiciel_nom', $logiciels[$_POST['logiciel']]);
                $page->assign('logiciel_rare', in_array($_POST['logiciel'], array("2k3serv", "2k3access", "2k3onenote", "2k3visiopro")));
            }
        }
    }

    public function handler_licences_final($page){
        global $DB_msdnaa, $DB_trombino;


        $logiciels=$this->licences_logiciels();
        $cles_admin = $this->licences_admin();

        if(isset($_POST['refus']) || !isset($_POST['raison']) || $_POST['raison']=="" || !isset($_POST['logiciel']) || !in_array($_POST['logiciel'], array_keys($logiciels))){
            $this->handler_licences($page);
        }else{
            $page->changeTpl('profil/licences_final.tpl');
            $page->assign('title', "Les Licences");

            if(!in_array($_POST['logiciel'], array_keys($cles_admin))){
                // On regarde s'il y a déjà une clé ou  une deamnde en attente pour le logiciel en question
                $DB_msdnaa->query("SELECT cle, attrib FROM cles_{$_POST['logiciel']} WHERE eleve_id='{$_SESSION['uid']}'");
                $already_has=($DB_msdnaa->num_rows() >0);

                $DB_msdnaa->query("SELECT 0 FROM valid_licence WHERE eleve_id={$_SESSION['uid']} AND logiciel={$_POST['logiciel']}");
                $already_asked=($DB_msdnaa->num_rows()>0);
            }else{
                $already_has=false;
                $already_asked=false;
                $cle=$cles_admin[$_POST['logiciel']];
            }

            $page->assign('already_asked', $already_asked);
            $page->assign('already_has', $already_has);
            $page->assign('logiciel', $_POST['logiciel']);
            $page->assign('logiciel_nom', $logiciels[$_POST['logiciel']]);

            $logiciels_domaine = array("winxp", "winvista", "win2k");

            $DB_trombino->query("SELECT mail FROM eleves WHERE eleve_id='{$_SESSION['uid']}'");
            list($email)=$DB_trombino->next_row();

            if(isset($cle)){
                $mail = new PlMailer('profil/licences_cle.mail.tpl');
                $mail->assign('nom', $_SESSION['nom']);
                $mail->assign('prenom', $_SESSION['prenom']);
                $mail->assign('cle', $cle);
                $mail->assign('pub_domaine', in_array($_POST['logiciel'], $logiciels_domaine));
                $mail->assign('logiciel_nom', $logiciels[$_POST['logiciel']]);
                $mail->addTo($email);
                $mail->send();

                $mail=new PlMailer('profil/licences_cle_admin.mail.tpl');
                $mail->assign('nom', $_SESSION['nom']);
                                $mail->assign('prenom', $_SESSION['prenom']);
                $mail->assign('promo', $_SESSION['promo']);
                $mail->assign('cle', $cle);
                $mail->assign('logiciel_nom', $logiciels[$_POST['logiciel']]);
                $mail->setFrom($email);
                $mail->send();
            }else{
    /*          $mail = new PlMailer('profil/licences_nocle.mail.tpl');
                $mail->assign('nom', $_SESSION['nom']);
                $mail->assign('prenom', $_SESSION['prenom']);
                $mail->assign('pub_domaine', in_array($_POST['logiciel'], $logiciels_domaine));
                $mail->assign('logiciel_nom', $logiciels[$_POST['logiciel']]);
                $mail->addTo($mail);
                $mail->send();
    */
                $mail=new PlMailer('profil/licences_nocle_admin.mail.tpl');
                $mail->assign('nom', $_SESSION['nom']);
                $mail->assign('prenom', $_SESSION['prenom']);
                $mail->assign('promo', $_SESSION['promo']);
                $mail->assign('logiciel_nom', $logiciels[$_POST['logiciel']]);
                $mail->setFrom($email);
                $mail->send();

                //Insertion dans la DB
                $DB_msdnaa->query("INSERT valid_licence SET raison='{$_POST['raison']}', logiciel='{$_POST['logiciel']}', eleve_id='{$_SESSION['uid']}'");

            }

        }
    }

    function handler_minimodules($page)
    {
        $iter = XDB::iterator('SELECT  m.name, m.label, m.description, COUNT(um.name) frequence
                                 FROM  minimodules AS m
                            LEFT JOIN  users_minimodules AS um ON m.name = um.name
                             GROUP BY  m.name
                             ORDER BY  frequence DESC');

        $user_minimodules  = S::user()->minimodules();
        $minimodules = array();
        while ($minimodule = $iter->next()) {
            $m = FrankizMiniModule::get($minimodule['name']);
            if ($m->checkAuthAndPerms())
            {
                $minimodules[] = array('activated' => in_array($minimodule['name'], $user_minimodules),
                                       'frequence' => $minimodule['frequence'],
                                            'name' => $minimodule['name'],
                                           'label' => $minimodule['label'],
                                     'description' => $minimodule['description']);
            }
        }

        $page->assign('title', 'Gestion des minimodules');
        $page->assign('minimodules', $minimodules);
        $page->addCssLink('profile.css');
        $page->changeTpl('profile/minimodules.tpl');
    }

    function handler_ajax_minimodules_layout($page)
    {
        $json = json_decode(Env::v('json'));

        $layout = FrankizMiniModule::emptyLayout();

        foreach(array_keys($layout) as $col)
            if (isset($json->$col))
                $layout[$col] = $json->$col;

        $page->jsonAssign();
        if (!S::user()->layoutMinimodules($layout))
            $page->jsonAssign('error', "Le réagencement des minimodules n'a pas pu se faire");
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
    }

    function handler_ajax_minimodules_remove($page)
    {
        $json = json_decode(Env::v('json'));

        $m = FrankizMiniModule::get($json->name);
        $success = S::user()->removeMinimodule($m);

        $page->jsonAssign();
        if (!$success)
            $page->jsonAssign('error', "Impossible de désactiver le minimodule");
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>