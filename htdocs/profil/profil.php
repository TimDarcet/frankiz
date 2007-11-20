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
	
	$Id$

*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_MAIL);



// Récupération d'une image
if((isset($_REQUEST['image']))&&($_REQUEST['image'] == "true") && ($_REQUEST['image'] != "")){
	require_once("../include/global.inc.php");
	$size = getimagesize(BASE_DATA."trombino/a_valider_".$_REQUEST['id']);
	header("Content-type: {$size['mime']}");
	readfile(BASE_DATA."trombino/a_valider_".$_REQUEST['id']);
	exit;
}

$message="";

// Données sur l'utilisateur
$DB_trombino->query("SELECT eleves.nom,prenom,surnom,mail,portable,login,promo,sections.nom,cie,piece_id,commentaire FROM eleves LEFT JOIN sections USING(section_id) WHERE eleve_id='{$_SESSION['user']->uid}'");
list($nom,$prenom,$surnom,$mail,$portable,$login,$promo,$section,$cie,$casert,$commentaire) = $DB_trombino->next_row();

if(isset($_POST['changer_frankiz'])) {
	// Modification du mot de passe
	if($_POST['passwd']=='12345678' && $_POST['passwd2']=='87654321' || empty($_POST['passwd']) && empty($_POST['passwd2'])) {
		// ne rien faire, on garde l'ancien mot de passe
	} else if($_POST['passwd'] != $_POST['passwd2']) {
		ajoute_erreur(ERR_MDP_DIFFERENTS);
	} else if(strlen($_POST['passwd']) < 8) {
		ajoute_erreur(ERR_MDP_TROP_PETIT);
	} else {
		$_hash_shadow = hash_shadow($_POST['passwd']);
		$DB_web->query("UPDATE compte_frankiz SET passwd='$_hash_shadow' WHERE eleve_id='{$_SESSION['user']->uid}'");

		// Synchronisation avec le wifi
		$DB_wifi->query("UPDATE alias SET Password='$_hash_shadow' WHERE Alias='{$_SESSION['user']->login}' AND Method='TTLS';");
		$DB_wifi->query("UPDATE radcheck SET Value='$_hash_shadow' WHERE UserName='{$_SESSION['user']->login}' AND Attribute='Crypt-Password';");

		$message.="<commentaire>Le mot de passe vient d'être changé.</commentaire>";
	}

	// Modification du cookie d'authentification
	if($_POST['cookie'] == 'oui') {
		// on rajoute le cookie (le hash est le même que celui créé lors de l'authentification
		// initiale par mail)
		$DB_web->query("SELECT hash FROM compte_frankiz WHERE eleve_id='{$_SESSION['user']->uid}'");
		list($new_hash) = $DB_web->next_row();
		$cookie = array('hash'=>$new_hash,'uid'=>$_SESSION['user']->uid);
		
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
	if(strlen($_POST['surnom']) > 32)
		ajoute_erreur(ERR_SURNOM_TROP_LONG);	
	if($_POST['email'] == "$login@poly" || $_POST['email'] == "$login@poly.polytechnique.fr")
		$_POST['email'] = "";
	if(!ereg("^[a-zA-Z0-9_+.-]+@[a-zA-Z0-9.-]+$",$_POST['email']) && !empty($_POST['email']))
		ajoute_erreur(ERR_EMAIL_NON_VALIDE);
	if(strlen($_POST['portable']) < 8 && !empty($_POST['portable']))
		ajoute_erreur(ERR_PORTABLE_TROP_PETIT);
	if(strlen($_POST['portable']) > 14)
		ajoute_erreur(ERR_PORTABLE_TROP_LONG);
	
	if(aucune_erreur()) {
		$surnom = $_POST['surnom'];
		$mail = $_POST['email'];
		$portable = $_POST['portable'];
		
		$DB_trombino->query("UPDATE eleves SET portable='$portable',surnom='$surnom',mail=".(empty($mail)?"NULL":"'$mail'")." WHERE eleve_id='{$_SESSION['user']->uid}'");
		$message.="<commentaire>L'email, le surnom et le portable ont été modifiés.</commentaire>";
	}
	
	//===================================
	// Modification de l'image trombino
	//--------------------------------------------
	if ($_FILES['file']['tmp_name']!='') {

		// On verifie d'abord que la personne n'a pas demander le changement de sa photo trombino
		//------------------------------------
		if (file_exists(DATA_DIR_LOCAL."trombino/a_valider_{$_SESSION['user']->uid}")) {
			$deuxieme_demande= true;
		}else{
			$deuxieme_demande= false;
		}
		
		// Vérif taille et déplacemet de l'image
		$img = $_FILES['file']['tmp_name'] ;
	
			//récupere les données de l'images
			//--------------------------------------
		$type_img =  $_FILES["file"]["type"];
		
		$fp = fopen($img,"rb"); // (b est pour lui dire que c'est bineaire !)
		$size = filesize($img) ;
		$data = fread($fp,$size);
		fclose($fp);
		$data = addslashes($data);
	
			//
			// On verifie que le truc télécharger est une image ...
			// + bonne taille !
			//-------------------------------------

		if (($original_size = getimagesize($_FILES['file']['tmp_name']))&&($original_size[0]<=300)&&($original_size[1]<=400)) {
			$filename =$_SESSION['user']->uid ;
			move_uploaded_file($_FILES['file']['tmp_name'], DATA_DIR_LOCAL.'trombino/a_valider_'.$filename) ;
			if ($deuxieme_demande) {
				$message .= "<warning>Tu avais déjà demandé une modification de photo, seule la demande que tu viens de poster sera prise en compte.</warning>";
			} else {
				$tempo = explode("profil",$_SERVER['REQUEST_URI']) ;
	
				$contenu = "$nom $prenom ($promo) a demandé la modification de son image trombino <br><br>".
					"Pour valider ou non cette demande va sur la page suivante : <br>".
					"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_trombi.php'>".
					"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_trombi.php</a></div><br><br>" .
					"Cordialement,<br>" .
					"Le Tolmestre<br>"  ;
					
				couriel(TROMBINOMEN_ID,"[Frankiz] Modification de l'image trombi de $nom $prenom",$contenu,$_SESSION['user']->uid);
				
				$message .= "<commentaire>Ta demande de changement de photo a été prise en compte et sera validée dans les meilleurs délais.</commentaire>";
			}
		} else {
			$message .= "<warning>Ton image n'est pas au bon format, ou est trop grande.</warning>" ;
		}
		
	}
}

// Modification d'un commentaire sur un binet
if (isset($_POST['mod_binet'])) {
	foreach($_POST['commentaire'] as $key=>$val)
		$DB_trombino->query("UPDATE membres SET remarque='$val' WHERE eleve_id='{$_SESSION['user']->uid}' AND binet_id='$key'");
	$DB_trombino->query("UPDATE eleves SET commentaire='{$_POST['perso']}' WHERE eleve_id='{$_SESSION['user']->uid}'");
	$commentaire = $_POST['perso'];
	$message .= "<commentaire>Modification de la partie binets effectuée avec succès.</commentaire>";
}

// Suppression d'un binet
if (isset($_POST['suppr_binet'])) {
	$count =0 ;
	if (isset($_POST['elements'])) {
		$ids = "" ;
		foreach($_POST['elements'] as $id => $on) {
			if($on=='on') $ids .= (empty($ids) ? "" : ",") . "'$id'";
			$count ++ ;
		}
	}
	if ($count>=1) {
		$DB_trombino->query("DELETE FROM membres WHERE binet_id IN ($ids) AND  eleve_id='{$_SESSION['user']->uid}'");
		$message .= "<commentaire>Suppression de $count binet(s).</commentaire>";
	} else {
		$message .= "<warning>Aucun binet n'est sélectionné. Aucun binet n'a donc été supprimé de la liste de tes binets.</warning>";
	}
}

// Ajout d'un binet
if (isset($_POST['add_binet'])) {
	if ($_POST['liste_binet'] != 'default') {
		$DB_trombino->query("REPLACE INTO membres SET eleve_id='{$_SESSION['user']->uid}',binet_id='{$_POST['liste_binet']}'");
		$message .= "<commentaire>Binet correctement ajouté</commentaire>";
	} else {
		$message .= "<warning>Aucun binet sélectionné. Aucun binet n'a donc été ajouté à la liste de tes binets.</warning>";
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
		if(a_erreur(ERR_SURNOM_TROP_LONG))
			echo "<warning>Il faut mettre un surnom moins long (au maximum 32 caractères). Le surnom n'a pas été modifié.</warning>\n";
		if(a_erreur(ERR_EMAIL_NON_VALIDE))
			echo "<warning>L'email n'est pas valide. L'adresse email n'a pas été modifié.</warning>\n";
		if(a_erreur(ERR_PORTABLE_TROP_PETIT))
			echo "<warning>Un portable a besoin d'au moins 8 chifres pout être identifié. Le portable n'a pas été modifié.</warning>\n";
		if(a_erreur(ERR_PORTABLE_TROP_LONG))
			echo "<warning>Le champ \"Portable\" n'accepte plus que 14 caractères. Le portable n'a pas été modifié.</warning>\n";
?>
	<formulaire id="mod_frankiz" titre="Modification du compte Frankiz" action="profil/profil.php">
		<?php
		if (isset($_REQUEST['hash'])) {
		?>
			<note>Remplace vite ton mot de passe</note>
		<?php	
		}else{ 
		?>
			<note>Ne pas toucher ou laisser vide pour conserver l'ancien mot de passe</note>
		<?php
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
		<champ id="portable" titre="Portable" valeur="<?php echo $portable ?>"/>
		<?php if (file_exists(DATA_DIR_LOCAL."trombino/a_valider_{$_SESSION['user']->uid}")): ?>
			<note>Cette image trombino n'a pas encore été validée par le BR</note>
			<image source="profil/profil.php?image=true&amp;id=<?php echo $_SESSION['user']->uid; ?>" texte="photo" height="95" width="80"/>
		<?php else: ?>
			<image source="trombino.php?image=true&amp;login=<?php echo $login; ?>&amp;promo=<?php echo $promo; ?>" texte="photo" height="95" width="80"/>
		<?php endif; ?>
		<note>Tu peux personnaliser le trombino en changeant ta photo. Attention, elle ne doit pas dépasser 200Ko ou 300x400 pixels. Les TOLmestres te rappellent que cette photo doit permettre de te reconnaître facilement. Propose donc plutôt une photo sur laquelle tu es seul, et où on voit bien ton visage.</note>
		<fichier id="file" titre="Nouvelle photo" taille="200000"/>

		<bouton id="changer_trombino" titre="Changer"/>
	</formulaire>
	
	<liste id="liste_binet" selectionnable="oui" action="profil/profil.php" titre="Mes Binets">
		<entete id="binet" titre="Binet"/>
		<entete id="commentaire" titre="Commentaire"/>
		
		<?php
		$DB_trombino->query("SELECT membres.remarque,membres.binet_id,binets.nom FROM membres LEFT JOIN binets USING(binet_id) WHERE eleve_id='{$_SESSION['user']->uid}' ORDER BY binets.nom ASC");
		while (list($remarque,$binet_id,$nom) = $DB_trombino->next_row()): ?>
			<element id="<?php echo $binet_id; ?>">
				<colonne id="binet"><?php echo $nom?> :</colonne>
				<colonne id="commentaire"><champ id="commentaire[<?php echo $binet_id?>]" titre="" valeur="<?php echo $remarque?>"/></colonne>
			</element>
		<?php endwhile; ?>
		
		<note>Si tu viens d'adhérer à un binet, n'hésite pas à le montrer et inscris le sur le TOL</note>
		<element id="-1" selectionnable="non">
			<colonne id="binet">Rajouter un binet</colonne>
			<colonne id="commentaire">
				<choix id="liste_binet"  type="combo" valeur="Ajout">
					<option titre="" id="default"/>
<?php
					$DB_trombino->query("SELECT nom,binet_id FROM binets  ORDER BY nom ASC");
					while (list($nom_binet,$binet_id) = $DB_trombino->next_row())
						echo "<option titre=\"$nom_binet\" id=\"$binet_id\"/>\n";
?>
				</choix>
				<bouton id='add_binet' titre='Ajouter'/>
			</colonne>
		</element>

		<element id="-2" selectionnable="non">
			<colonne id="binet">Autres commentaires</colonne>
			<colonne id="commentaire">
				<zonetext id="perso" titre="Commentaire perso" type="moyen"><?php echo $commentaire;?></zonetext>
			</colonne>
		</element>
		<bouton id='suppr_binet' titre='Supprimer' onClick="return window.confirm('Es-tu sûr de vouloir supprimer ce binet ?')"/>
		<bouton id='mod_binet' titre='Enregistrer les commentaires'/>
	</liste>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
