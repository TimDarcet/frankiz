<?php
/*
	Copyright (C) 2004 Binet Réseau
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
	Page permettant de modifier son profil dans le trombino et quelques paramètres
	pour le site web.
	
	TODO modification de sa photo et de ses binets.
	
	$Log$
	Revision 1.37  2004/12/15 06:06:08  kikx
	/me tres fatigué

	Revision 1.36  2004/12/15 06:04:46  kikx
	Pour ne pas avoir des messages de commentaires ambigué
	
	Revision 1.35  2004/12/15 05:12:28  falco
	typo
	
	Revision 1.34  2004/12/14 00:52:02  kikx
	Envoie les demandes de changement au nom du mec qui demande ... pour faire plaisir au gens ...
	
	Revision 1.33  2004/12/07 14:39:26  schmurtz
	Bugs et orthographe
	
	Revision 1.32  2004/11/29 17:27:32  schmurtz
	Modifications esthetiques.
	Nettoyage de vielles balises qui trainaient.
	
	Revision 1.31  2004/11/27 08:03:53  pico
	/tmp/cvsh9cLqQ
	
	Revision 1.30  2004/11/24 18:12:27  kikx
	Séparation de la page du site web et du profil personnel
	
	Revision 1.29  2004/11/22 23:07:28  kikx
	Rajout de lines vers les pages perso
	
	Revision 1.28  2004/11/22 21:17:12  kikx
	merci pico !!!!!!!!!!!!!!!!!!!!!!!!!!!
	
	Revision 1.27  2004/11/22 21:04:54  kikx
	Pour le debug de Pico
	
	Revision 1.26  2004/11/22 19:10:39  kikx
	Mise en place de note pour le profil
	permet de mieux expliquer comment cela fonctionne
	
	Revision 1.25  2004/11/22 18:59:31  kikx
	Pour gérer son site perso
	
	Revision 1.24  2004/11/07 22:41:03  pico
	Ne cherche plus à uploader d'image qd on lui demande pas
	
	Revision 1.23  2004/10/29 16:12:53  kikx
	Diverse correction sur les envoie des mail en HTML
	
	Revision 1.22  2004/10/25 10:35:50  kikx
	Page de validation (ou pas) des modif de trombi
	
	Revision 1.21  2004/10/22 15:18:33  kikx
	Correction du bug d'affichage de l'image trombino quand l'utilisateur a demander la modification
	
	Revision 1.20  2004/10/22 15:07:40  kikx
	Juste pour la standardisation
	
	Revision 1.19  2004/10/22 14:44:40  kikx
	Permet l'affichage dans le profil de l'image que l'on souhaite modifier (bug encore sur l'affichage)
	
	Revision 1.18  2004/10/22 11:13:32  kikx
	Autorise de modifier son image trombi : reste a faire la apge de validation coté admin
	
	Revision 1.17  2004/10/21 22:43:11  kikx
	Bug fix et mise en place de la possibilité de modifier la photo du trombino
	
	Revision 1.16  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.15  2004/10/21 22:17:16  kikx
	Juste pour Schmurtz
	
	Revision 1.14  2004/10/20 20:37:27  kikx
	Possibilité par l'utilisateur de se rajouter un binet
	
	Revision 1.13  2004/10/20 19:51:17  kikx
	Structure pour se rajouter des binets
	
	Revision 1.12  2004/10/20 18:47:07  kikx
	Pour rajouter des lignes non selectionnables dans une liste
	
	Revision 1.11  2004/10/20 11:02:10  kikx
	Permet la suppression des binets dans son profil
	
	Revision 1.10  2004/10/19 20:22:04  schmurtz
	Rajout de messages de reussite dans la page profil
	Fusion des formulaires changement de mot de passe et activation du cookie
	
	Revision 1.9  2004/09/15 23:20:07  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.8  2004/09/15 21:42:21  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_MAIL);



// Récupération d'une image
if((isset($_REQUEST['image']))&&($_REQUEST['image'] == "true") && ($_REQUEST['image'] != "")){
	require_once("../include/global.inc.php");
	header('content-type: image/jpeg');
	readfile(BASE_DATA."trombino/a_valider_".$_REQUEST['id']);	
	exit;
}

$message="";

// Données sur l'utilisateur
$DB_trombino->query("SELECT eleves.nom,prenom,surnom,mail,login,promo,sections.nom,cie,piece_id FROM eleves INNER JOIN sections USING(section_id) WHERE eleve_id='{$_SESSION['user']->uid}'");
list($nom,$prenom,$surnom,$mail,$login,$promo,$section,$cie,$casert) = $DB_trombino->next_row();

if(isset($_POST['changer_frankiz'])) {
	// Modification du mot de passe
	if($_POST['passwd']=='12345678' && $_POST['passwd2']=='87654321' || empty($_POST['passwd']) && empty($_POST['passwd2'])) {
		// ne rien faire, on garde l'ancien mot de passe
	} else if($_POST['passwd'] != $_POST['passwd2']) {
		ajoute_erreur(ERR_MDP_DIFFERENTS);
	} else if(strlen($_POST['passwd']) < 8) {
		ajoute_erreur(ERR_MDP_TROP_PETIT);
	} else {
		$DB_web->query("UPDATE compte_frankiz SET passwd='".md5($_POST['passwd'])."' WHERE eleve_id='{$_SESSION['user']->uid}'");
		$message.="<commentaire>Le mot de passe vient d'être changé.</commentaire>";
	}

	// Modification du cookie d'authentification
	if($_POST['cookie'] == 'oui') {
		// on rajoute le cookie
		$cookie = array('hash'=>nouveau_hash(),'uid'=>$_SESSION['user']->uid);
		$DB_web->query("UPDATE compte_frankiz SET hash='{$cookie['hash']}' WHERE eleve_id='{$_SESSION['user']->uid}'");
		SetCookie("auth",base64_encode(serialize($cookie)),time()+3*365*24*3600,"/");
		$_COOKIE['auth'] = "blah";  // hack permetttant de faire marcher le test d'existance du cookie
									// utilisé quelques ligne plus bas sans devoir recharger la page.
		$message.="<commentaire>Le cookie d'authentification a été activé.</commentaire>";
	} else {
		// on supprime le cookie
		SetCookie("auth","",0,"/");
		unset($_COOKIE['auth']);	// hack, cf. au dessus.
		$message.="<commentaire>Le cookie d'authentification a été désactivé.</commentaire>";
	}

// Modification de la fiche du trombino
} else if(isset($_POST['changer_trombino'])) {
	if(strlen($_POST['surnom']) < 2 && !empty($_POST['surnom']))
		ajoute_erreur(ERR_SURNOM_TROP_PETIT);
		
	if($_POST['email'] == "$login@poly" || $_POST['email'] == "$login@poly.polytechnique.fr")
		$_POST['email'] = "";
	if(!ereg("^[a-zA-Z0-9_+.-]+@[a-zA-Z0-9.-]+$",$_POST['email']) && !empty($_POST['email']))
		ajoute_erreur(ERR_EMAIL_NON_VALIDE);
	
	if(aucune_erreur()) {
		$surnom = $_POST['surnom'];
		$mail = $_POST['email'];
		$DB_trombino->query("UPDATE eleves SET surnom='$surnom',mail=".(empty($mail)?"NULL":"'$mail'")." WHERE eleve_id='{$_SESSION['user']->uid}'");
		$message.="<commentaire>L'email et le surnom ont été modifiés.</commentaire>";
	}
	
	//===================================
	// Modification de l'image trombino
	//--------------------------------------------
	if ($_FILES['file']['tmp_name']!='') {

		// On verifie d'abord que la personne n'a pas demander le changement de sa photo trombino
		//------------------------------------
	
		if (file_exists(DATA_DIR_LOCAL."trombino/a_valider_{$_SESSION['user']->uid}")) {
			$message .= "<warning>Tu avais déjà demandé une modification de photo, seule la demande que tu viens de poster sera prise en compte.</warning>";
		} else {
			$tempo = explode("profil",$_SERVER['REQUEST_URI']) ;

			$contenu = "$nom $prenom ($promo) a demandé la modification de son image trombino <br><br>".
				"Pour valider ou non cette demande va sur la page suivante : <br>".
				"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_trombi.php'>".
				"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_trombi.php</a></div><br><br>" .
				"Très BR-ement<br>" .
				"L'automate :)<br>"  ;
				
			couriel(WEBMESTRE_ID,"[Frankiz] Modification de l'image trombi de $nom $prenom",$contenu,$_SESSION['user']->uid);
			$message .= "<commentaire>Ta demande de changement de photo a été prise en compte et sera validée dans les meilleurs délais.</commentaire>";

		}
		
		$img = $_FILES['file']['tmp_name'] ;
		$image_types = Array ("image/bmp","image/jpeg","image/pjpeg","image/gif","image/x-png","image/png");
	
			//récupere les données de l'images
			//--------------------------------------
			
		$type_img =  $_FILES["file"]["type"];
		
		$fp = fopen($img,"rb"); // (b est pour lui dire que c'est bineaire !)
		$size = filesize($img) ;
		$dim = getimagesize($img) ;
		$data = fread($fp,$size);
		fclose($fp);
		$data = addslashes($data);
	
			//
			// On verifie que le truc télécharger est une image ...
			//--------------------------------------
		if (in_array (strtolower ($type_img), $image_types)) {
			$filename =$_SESSION['user']->uid ;
			move_uploaded_file($_FILES['file']['tmp_name'], DATA_DIR_LOCAL.'trombino/a_valider_'.$filename) ;
		} else {
			$message .= "<warning>Ton image n'est pas au bon format, ou est trop grande.</warning>" ;
		}
	}
}

// Modification d'un commentaire sur un binet
if (isset($_POST['mod_binet'])) {
	foreach($_POST['commentaire'] as $key=>$val)
		$DB_trombino->query("UPDATE membres SET remarque='$val' WHERE eleve_id='{$_SESSION['user']->uid}' AND binet_id='$key'");
	$message .= "<commentaire>Modification de la partie binets effectuée avec succès.</commentaire>";
}

// Suppression d'un binet
if (isset($_POST['suppr_binet'])) {
	$count =0 ;
	if (isset($_POST['elements'])) {
		$ids = "" ;
		foreach($_POST['elements'] as $id => $on) {
			if($on='on') $ids .= (empty($ids) ? "" : ",") . "'$id'";
			$count ++ ;
		}
	}
	if ($count>=1) {
		$DB_trombino->query("DELETE FROM membres WHERE binet_id IN ($ids) AND  eleve_id='{$_SESSION['user']->uid}'");
		$message .= "<commentaire>Suppression de $count binet(s).</commentaire>";
	} else {
		$message .= "<warning>Aucun binet n'est séléctionné. Aucun binet n'a donc été supprimé de la liste de tes binets.</warning>";
	}
}

// Ajout d'un binet
if (isset($_POST['add_binet'])) {
	if ($_POST['liste_binet'] != 'default') {
		$DB_trombino->query("INSERT INTO membres SET eleve_id='{$_SESSION['user']->uid}',binet_id='{$_POST['liste_binet']}'");
		$message .= "<commentaire>Binet correctement ajouté</commentaire>";
	} else {
		$message .= "<warning>Aucun binet séléctionné. Aucun binet n'a donc été ajouté à la liste de tes binets.</warning>";
	}
}


// Génération du la page XML
require "../include/page_header.inc.php";

?>
<page id="profil" titre="Frankiz : modification du profil">
	<h1>Modification de son profil</h1>
<?php
		if(!empty($message))
			echo "$message\n";
		if(a_erreur(ERR_MDP_DIFFERENTS))
			echo "<warning>Les valeurs des deux champs de mot de passe n'étaient pas identiques. Le mot de passe n'a pas été modifié.</warning>\n";
		if(a_erreur(ERR_MDP_TROP_PETIT))
			echo "<warning>Il faut mettre un mot de passe plus long (au moins 8 caractères). Le mot de passe n'a pas été modifié.</warning>\n";
		if(a_erreur(ERR_SURNOM_TROP_PETIT))
			echo "<warning>Il faut mettre un surnom plus long (au moins 2 caractères). Le surnom n'a pas été modifié.</warning>\n";
		if(a_erreur(ERR_EMAIL_NON_VALIDE))
			echo "<warning>L'email n'est pas valide. L'adresse email n'a pas été modifié.</warning>\n";
?>
	<formulaire id="mod_frankiz" titre="Modification du compte Frankiz" action="profil/profil.php">
		<?
		if (isset($_REQUEST['hash'])) {
		?>
			<note>Remplace vite ton mot de passe</note>
		<?	
		}else{ 
		?>
			<note>Ne pas toucher ou laisser vide pour conserver l'ancien mot de passe</note>
		<?
		}
		?>
		<champ id="passwd" titre="Mot de passe" valeur="12345678"/>
		<champ id="passwd2" titre="Retaper le mot de passe" valeur="87654321"/>
		<note>L'authentification par cookie permet de se connecter automatiquement lorsque tu accèdes à frankiz. N'active pas cette authentification si tu te connectes sur un ordinateur qui n'est pas le tien.</note>
		<choix id="cookie" titre="Utiliser l'authentification par cookie" type="combo"
				valeur="<?php echo empty($_COOKIE['auth'])? 'non' : 'oui' ?>">
			<option titre="Activé" id="oui"/>
			<option titre="Désactivé" id="non"/>
		</choix>
		<bouton id="changer_frankiz" titre="Enregistrer"/>
	</formulaire>
	
	<formulaire id="mod_trombino" titre="Changement de la fiche trombino" action="profil/profil.php">
		<champ id="nom" titre="Nom" valeur="<?php echo $nom.' '.$prenom ?>" modifiable="non"/>
		<champ id="loginpoly" titre="Login poly" valeur="<?php echo $login ?>" modifiable="non"/>
		<champ id="promo" titre="Promo" valeur="<?php echo $promo ?>" modifiable="non"/>
		<champ id="section" titre="Section" valeur="<?php echo $section.' (compagnie '.$cie.')' ?>" modifiable="non"/>
		<champ id="casert" titre="Kazert" valeur="<?php echo $casert ?>" modifiable="non"/>
		<champ id="surnom" titre="Surnom" valeur="<?php echo $surnom ?>"/>
		<champ id="email" titre="Email" valeur="<?php echo empty($mail) ? $login.'@poly.polytechnique.fr' : $mail ?>"/>
		<?php if (file_exists(DATA_DIR_LOCAL."trombino/a_valider_{$_SESSION['user']->uid}")): ?>
			<note>Cette image trombino n'a pas encore été validé par le BR</note>
			<image source="profil/profil.php?image=true&amp;id=<?=$_SESSION['user']->uid?>" texte="photo" height="95" width="80"/>
		<?php else: ?>
			<image source="trombino.php?image=true&amp;login=<?=$login?>&amp;promo=<?=$promo?>" texte="photo" height="95" width="80"/>
		<?php endif; ?>
		<note>Tu peux personnaliser le trombino en changeant ta photo (elle ne doit pas dépasser 200Ko)</note>
		<fichier id="file" titre="Nouvelle photo" taille="200000"/>

		<bouton id="changer_trombino" titre="Changer"/>
	</formulaire>
	
	<liste id="liste_binet" selectionnable="oui" action="profil/profil.php" titre="Mes Binets">
		<entete id="binet" titre="Binet"/>
		<entete id="commentaire" titre="Commentaire"/>
		
		<?
		$DB_trombino->query("SELECT membres.remarque,membres.binet_id,binets.nom FROM membres INNER JOIN binets USING(binet_id) WHERE eleve_id='{$_SESSION['user']->uid}' ORDER BY binets.nom ASC");
		while (list($remarque,$binet_id,$nom) = $DB_trombino->next_row()): ?>
			<element id="<?=$binet_id?>">
				<colonne id="binet"><? echo $nom?> :</colonne>
				<colonne id="commentaire"><champ id="commentaire[<? echo $binet_id?>]" titre="" valeur="<? echo $remarque?>"/></colonne>
			</element>
		<? endwhile; ?>
		
		<note>Si tu viens d'adhérer à un binet, n'hésite pas à le montrer et inscrit-y toi sur le TOL</note>
		<element id="<?=$binet_id?>" selectionnable="non">
			<colonne id="binet">Rajouter un binet</colonne>
			<colonne id="commentaire">
				<choix id="liste_binet"  type="combo" valeur="Ajout">
					<option titre="" id="default"/>
<?
					$DB_trombino->query("SELECT nom,binet_id FROM binets  ORDER BY nom ASC");
					while (list($nom_binet,$binet_id) = $DB_trombino->next_row())
						echo "<option titre=\"$nom_binet\" id=\"$binet_id\"/>\n";
?>
				</choix>
				<bouton id='add_binet' titre='Ajouter'/>
			</colonne>
		</element>
		
		<bouton id='suppr_binet' titre='Supprimer' onClick="return window.confirm('Es-tu sûr de vouloir supprimer ce binet ?')"/>
		<bouton id='mod_binet' titre='Enregistrer les commentaires'/>
	</liste>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
