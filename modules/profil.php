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
require_once BASE_FRANKIZ."htdocs/include/minimodules.inc.php";
require_once BASE_FRANKIZ."htdocs/include/session.inc.php";

class ProfilModule extends PLModule
{
	public function handlers()
	{
		return array('profil'			 => $this->make_hook('profil', AUTH_COOKIE),
			     'profil/fkz'                => $this->make_hook('fkz', AUTH_COOKIE),
			     'profil/fkz/change_mdp'     => $this->make_hook('fkz_change_mdp', AUTH_MDP),
		             'profil/fkz/change_tol'     => $this->make_hook('fkz_change_tol', AUTH_COOKIE),
			     'profil/fkz/mod_binets'     => $this->make_hook('fkz_mod_binets', AUTH_COOKIE),
			     'profil/reseau'		 => $this->make_hook('reseau', AUTH_MDP),
			     'profil/reseau/demande_ip'  => $this->make_hook('demande_ip', AUTH_COOKIE),
			     'profil/skin'               => $this->make_hook('skin', AUTH_COOKIE),
			     'profil/skin/change_skin'   => $this->make_hook('skin_change', AUTH_COOKIE),
			     'profil/skin/change_params' => $this->make_hook('skin_params', AUTH_COOKIE));
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
		$page->assign('profil_fkz_cie', $cie);
		$page->assign('profil_fkz_commentaire', $commentaire);
		

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
			$mail->assign('base', BASE_URL);
			$mail->send();

			$page->assign('nouvelle_demande', 1);
		}
	}
}
?>