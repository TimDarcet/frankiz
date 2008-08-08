<?php
/*
	Copyright (C) 2007 Binet Réseau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
/*
	Module d'annonces

	$Id: annonces.php 1969 2007-09-29 13:02:41Z elscouta $

*/
#require_once BASE_FRANKIZ."htdocs/include/minimodules.inc.php";
#require_once BASE_FRANKIZ."htdocs/include/session.inc.php";
#require_once BASE_FRANKIZ."htdocs/include/transferts.inc.php";

class ProfilModule extends PLModule
{
	public function handlers()
	{
		return array('profil'			  => $this->make_hook('profil', 		AUTH_COOKIE),
			     'profil/fkz'                 => $this->make_hook('fkz', 			AUTH_COOKIE),
			     'profil/fkz/change_mdp'      => $this->make_hook('fkz_change_mdp', 	AUTH_MDP),
		             'profil/fkz/change_tol'      => $this->make_hook('fkz_change_tol', 	AUTH_COOKIE),
			     'profil/fkz/mod_binets'      => $this->make_hook('fkz_mod_binets', 	AUTH_COOKIE),
			     'profil/mdp_perdu'		  => $this->make_hook('mdp_perdu', 		AUTH_PUBLIC),
			     'profil/reseau'		  => $this->make_hook('reseau', 		AUTH_MDP),
			     'profil/reseau/demande_ip'   => $this->make_hook('demande_ip', 		AUTH_COOKIE),
			     'profil/skin'                => $this->make_hook('skin', 			AUTH_COOKIE),
			     'profil/skin/change_skin'    => $this->make_hook('skin_change', 		AUTH_COOKIE),
			     'profil/skin/change_params'  => $this->make_hook('skin_params', 		AUTH_COOKIE),
			     'profil/siteweb'		  => $this->make_hook('siteweb',		AUTH_MDP),
			     'profil/siteweb/download'	  => $this->make_hook('siteweb_download',	AUTH_MDP),
			     'profil/siteweb/upload'	  => $this->make_hook('siteweb_upload',		AUTH_MDP),
			     'profil/siteweb/demande_ext' => $this->make_hook('siteweb_ext',		AUTH_MDP),
			     'profil/rss'		  => $this->make_hook('rss',			AUTH_COOKIE),
			     'profil/rss/update'	  => $this->make_hook('rss_update',		AUTH_COOKIE),
			     'profil/rss/add'		  => $this->make_hook('rss_add',		AUTH_COOKIE),
			     'profil/liens_perso'	  => $this->make_hook('liens_perso',		AUTH_COOKIE),
			     'profil/liens_perso/add'	  => $this->make_hook('liens_perso_add',	AUTH_COOKIE),
			     'profil/liens_perso/del'	  => $this->make_hook('liens_perso_del',	AUTH_COOKIE),
			     'profil/licences'		  => $this->make_hook('licences',		AUTH_MDP),
			     'profil/licences/cluf'	  => $this->make_hook('licences_CLUF',		AUTH_MDP),
			     'profil/licences/raison'	  => $this->make_hook('licences_raison',	AUTH_MDP),
			     'profil/licences/final'	  => $this->make_hook('licences_final',		AUTH_MDP)
			     );
	}

	public function handler_profil(&$page)
	{
		$page->assign('title', "Modification des préférences");
		$page->changeTpl('profil/index.tpl');
	}

	public function handler_fkz(&$page)
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

	public function handler_fkz_change_mdp(&$page)
	{
		global $DB_web;

		// Modification du mot de passe
		if (empty($_POST['passwd']) && empty($_POST['passwd2']) || 
		    $_POST['passwd'] == '12345678' && $_POST['passwd2'] == '87654321') 
		   
		{
			// RAS.
		} 
		else if ($_POST['passwd'] != $_POST['passwd2']) 
		{
			$page->append("profil_fkz_results", array('type' => 'erreur',
						   	 	  'text' => 'Les mots de passe ne correspondent pas.'));
		} 
		else if(strlen($_POST['passwd']) < 8) 
		{
			$page->append("profil_fkz_results", array('type' => 'erreur',
						       		  'text' => 'Mot de passe trop court : 8 caractères minimum.'));
		} 
		else 
		{
			$_hash_shadow = hash_shadow($_POST['passwd']);
			$DB_web->query("UPDATE compte_frankiz 
			                   SET passwd = '$_hash_shadow' 
					 WHERE eleve_id = '{$_SESSION['uid']}");
	
			// Synchronisation avec le wifi
			$DB_wifi->query("UPDATE alias 
			                    SET Password='$_hash_shadow'
				      LEFT JOIN trombino.eleves as e ON(alias.Alias = e.login)
					  WHERE e.eleve_id = '{$_SESSION['uid']}' AND Method = 'TTLS'");
			$DB_wifi->query("UPDATE radcheck 
			                    SET Value='$_hash_shadow' 
				      LEFT JOIN trombino.eleves AS e ON(radcheck.UserName = e.login)
					  WHERE e.eleve_id = '{$_SESSION['user']->login}' AND Attribute = 'Crypt-Password'");

			$page->append("profil_fkz_results", array('type' => 'commentaire',
						  	    	  'text' => "Le mot de passe vient d'être changé"));
		}

		
		// Modification du cookie d'authentification
		if($_POST['cookie'] == 'oui') 
		{
			FrankizSession::activer_auth_cookie(true);
			$page->append("profil_fkz_results", 
			              array('type' => 'commentaire',
				  	    'text' => "Le cookie d'authentification a été activé"));
		}
		else
		{
			FrankizSession::activer_auth_cookie(false);
			$page->append("profil_fkz_results",
				      array('type' => 'commentaire',
				            'text' => "Le cookie d'authentification a été désactivé"));
		}
	
		$this->handler_fkz($page);
	}

	public static function handler_fkz_change_tol(&$page)
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
						BASE_URL."admin/valid_trombi.php</a></div><br><br>" .
						"Cordialement,<br>" .
						"Le Tolmestre<br>"  ;
					
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
	
	public function handler_fkz_mod_binets(&$page)
	{
		if (isset($_POST['mod_binet']))
			$this->handler_fkz_change_binet(&$page);
		if (isset($_POST['suppr_binet']))
			$this->handler_fkz_suppr_binet(&$page);
		if (isset($_POST['add_binet']))
			$this->handler_fkz_ajout_binet(&$page);

		$this->handler_fkz(&$page);
	}

	private function handler_fkz_change_binet(&$page)
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


	private function handler_fkz_suppr_binet(&$page)
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

	private function handler_fkz_ajout_binet(&$page)
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

	function handler_skin(&$page)
	{
		// Recupére la liste des mini modules et verifie leur visibilite
		$minimodule_list = FrankizMiniModule::get_minimodule_list();
		$my_minimodule_list = array();
		foreach ($minimodule_list as $id => $desc)
			$my_minimodule_list[] = array('id'          => $id,
			                              'est_visible' => $_SESSION['skin']->est_minimodule_visible($id),
						      'desc'        => $desc);

		$page->assign('liste_skins', Skin::get_skin_list());
		$page->assign('liste_minimodules', $my_minimodule_list);
		$page->assign('title', "Modification de l'apparence de Frankiz");
		$page->changeTpl("profil/skin.tpl");
	}

	function handler_skin_change(&$page)
	{
		$_SESSION['skin']->change_skin($_REQUEST['newskin']);
		FrankizSession::save_skin();

		$this->handler_skin($page);
	}

	function handler_skin_params(&$page)
	{
		$minimodule_list = FrankizMiniModule::get_minimodule_list();
		foreach (array_keys($minimodule_list) as $module)
			$_SESSION['skin']->set_minimodule_visible($module, isset($_REQUEST["vis_$module"]));
		FrankizSession::save_skin();

		$this->handler_skin($page);
	}

	function handler_reseau(&$page)
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

	public function handler_demande_ip(&$page)
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

	public function handler_mdp_perdu(&$page)
	{
		global $DB_trombino, $DB_web;
		
		$page->changeTpl('profil/mdp_perdu.tpl');
		$page->assign('title', "Mot de passe perdu");	
		$page->assign('demande', 0);

		if (!empty($_REQUEST['loginpoly']))
		{
			list($login, $promo) = explode('.', $_REQUEST['loginpoly']);
			
			$DB_trombino->query("SELECT  eleve_id, login, prenom, nom, promo, mail
			                       FROM  eleves
					      WHERE  login = '$login'
					        AND  promo = '$promo'
				           ORDER BY  promo DESC
					      LIMIT  1");

			if ($DB_trombino->num_rows() != 1)
			{
				$page->trig("Ce loginpoly n'existe pas");
				return;
			}
			
			list ($id, $login, $prenom, $nom, $promo, $email) = $DB_trombino->next_row();
			$email="xelnor_couhain@melix.net";
			$hash = nouveau_hash(); 

			$DB_web->query("SELECT 0 FROM compte_frankiz WHERE eleve_id = '$id'");
			if ($DB_web->num_rows() > 0)
			{
				$DB_web->query("UPDATE  compte_frankiz 
					           SET  hash = '$hash', 
						        hashstamp = DATE_ADD(NOW(), INTERVAL 6 HOUR) 
					         WHERE  eleve_id = '$id'");
			}
			else
			{
				$DB_web->query("INSERT INTO  compte_frankiz 
				                        SET  eleve_id = '$id',
							     passwd = '',
							     perms = '',
							     hash = '$hash',
							     hashstamp = DATE_ADD(NOW(), INTERVAL 6 HOUR)");
			}

			$mail = new PlMailer('profil/mdp_perdu.mail.tpl');
			$mail->assign('hash', $hash);
			$mail->assign('uid', $id);
			$mail->addTo("$email");
			$mail->send();
		
			$page->assign('email', $email);
			$page->assign('demande', 1);
		}
	}

	public function handler_siteweb(&$page)
	{
		$page->changeTpl('profil/siteweb.tpl');
		$page->assign('title', "Gestion du site web personnel");
	}


	public function handler_siteweb_upload(&$page)
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

	public function handler_siteweb_download(&$page)
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

	public function handler_siteweb_ext(&$page)
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

	public function handler_rss_update(&$page)
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

	public function handler_rss_add(&$page)
	{
		$_SESSION['liens_rss'][$_REQUEST['rss_lien_add']] = array('description' => $_REQUEST['rss_lien_add'],
									  'module'      => 0,
									  'sommaire'    => 0,
									  'complet'     => 0);
		FrankizSession::save_liens_rss();

		$page->assign('rss_add', 1);
		$this->handler_rss($page);
	}

	public function handler_rss(&$page)
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

	public function handler_liens_perso_add(&$page)
	{
		$page->changeTpl('profil/liens_perso.tpl');
		$page->assign('title', "Ajout d'un lien perso");
	
		$_SESSION['liens_perso'][$_REQUEST['lien_perso']] = $_REQUEST['lien_perso'];
		FrankizSession::save_liens_perso();
	}

	public function handler_liens_perso_del(&$page)
	{
		$page->changeTpl('profil/liens_perso.tpl');
		$page->assign('title', "Suppression d'un lien perso");
	
		unset($_SESSION['liens_perso'][$_REQUEST['lien_perso']]);
		FrankizSession::save_liens_perso();
	}

	public function handler_liens_perso(&$page)
	{
		$page->changeTpl('profil/liens_perso.tpl');
		$page->assign('title', "Gestion des liens persos");
	}

	private function licences_logiciels(){
		return array('visualstudio' => 'Visual Studio .NET','winxp' => 'Windows XP Professionnel','winvista' => 'Windows Vista Business','2k3serv' => 'Windows Serveur 2003','2k3access'=>'Access 2003','2k3onenote'=>'One Note	2003','2k3visiopro'=>'Visio Professionnel 2003','win2k'=>'Windows 2000 Professionnel');
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

	public function handler_licences(&$page)
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

	public function handler_licences_CLUF(&$page){
		
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

	public function handler_licences_raison(&$page){
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

	public function handler_licences_final(&$page){
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
/*				$mail = new PlMailer('profil/licences_nocle.mail.tpl');
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

}
?>
