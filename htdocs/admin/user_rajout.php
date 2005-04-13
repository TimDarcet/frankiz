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
	Ajout d'un utilisateur !
	
	$Log$
	Revision 1.4  2005/04/13 17:09:58  pico
	Passage de tous les fichiers en utf8.

	Revision 1.3  2005/01/27 15:23:17  pico
	La boucle locale est considérée comme interne
	Tests de photos normalement plus cools.
	Après le reste.... je sais plus
	
	Revision 1.2  2005/01/18 23:24:42  pico
	Ajout fonction tdb
	Modif taille images trombi
	
	Revision 1.1  2004/12/17 16:30:31  kikx
	Interface pour ajouter un seul et unique utilisateurs ... utiles pour les PITs et les PI qui ne sont pas dans le tol !
	

*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('trombino'))
	rediriger_vers("/gestion/");

// On vérifie que la personne envoie bien l'id sinon ca sert a rien ...

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_user" titre="Frankiz : rajout des utilisateurs">
<?

// Modification de la partie "compte FrankizII"
$erreur=0 ;
if (isset($_POST['ajout'])) {
	if (($_POST['nom']!="") &&($_POST['prenom']!="") &&($_POST['login']!="") &&($_POST['date_nais']!="") &&($_POST['sexe']!="") &&($_POST['piece_id']!="") &&($_POST['section']!="") &&($_POST['cie']!="") &&($_POST['promo']!="") &&($_POST['mail']!="")&&($_FILES['file']['tmp_name']!='')) {
	
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
			//--------------------------------------
		if (($original_size = getimagesize($_FILES['file']['tmp_name']))&&($original_size[0]<=300)&&($original_size[1]<=400)) {
			$filename = BASE_PHOTOS.$_REQUEST['promo']."/".$_REQUEST['login']."_original.jpg" ;
			$filename2 = BASE_PHOTOS.$_REQUEST['promo']."/".$_REQUEST['login'].".jpg" ;
			move_uploaded_file($_FILES['file']['tmp_name'], $filename) ;
			copy($filename,$filename2) ;
			
			$DB_trombino->query("INSERT INTO eleves SET  nom='".$_POST['nom']."', prenom='".$_POST['prenom']."' ,surnom='".$_POST['surnom']."' ,date_nais='".$_POST['date_nais']."' ,sexe='".$_POST['sexe']."' ,piece_id='".$_POST['piece_id']."' ,section_id='".$_POST['section']."' ,cie='".$_POST['cie']."' ,promo='".$_POST['promo']."' ,login='".$_POST['login']."' ,mail='".$_POST['mail']."'");
			?>
			<commentaire>Utilisateur rajouté</commentaire>
			<?
		} else {
		?>
			<warning>Ton image n'est pas au bon format, ou est trop grande.</warning>
		<?
			$erreur=1 ;
		}
	
	} else {
		?>
		<warning>Données manquantes</warning>
		<?
		$erreur = 1 ;
	}
}
if ((!isset($_POST['ajout']))&&($erreur==0)) {
// Modification de ses variables génériques
?>
<warning> ATTENTION : Normalement ceci n'arrive quasiment jamais de rajouter une unique personne dans le trombino ... Seuls les élèves (+ promo), les PI (+ promo de rattachement), Zaza (+Kes), et les pits (+Capitaine) en font parti c'est tout !!!!!!!!!!</warning>
 
	<formulaire id="" titre="Général" action="admin/user_rajout.php">

		<champ id="nom" titre="Nom*" valeur="<? if (isset($_POST['nom'])) echo $_POST['nom']?>"/>
		<champ id='prenom' titre='Prénom*' valeur='<? if (isset($_POST['prenom'])) echo $_POST['prenom']?>'/>
		<champ id='surnom' titre='Surnom' valeur='<? if (isset($_POST['surnom'])) echo $_POST['surnom']?>'/>
		<champ id='login' titre='Login*' valeur='<? if (isset($_POST['login'])) echo $_POST['login']?>'/>
		<note>Bien respecter le formatage si vous voulez pas avoir d'erreur</note>
		<champ id='date_nais' titre='Date de naissance*' valeur='<? if (isset($_POST['date_nais'])) echo $_POST['date_nais'] ; else echo "0000-00-00"?>'/>
		<choix titre="Sexe*" id="sexe" type="combo" valeur="<? if (isset($_POST['sexe'])) echo $_POST['sexe']?>">
				<option titre="Homme" id="1"/>
				<option titre="Femme" id="2"/>
		</choix>
		<note>Avant de remplir cette case, vérifie la syntaxe de la piece <lien url="admin/ip.php" titre="ici"/></note>
		<champ id='piece_id' titre='Ksert*' valeur='<? if (isset($_POST['piece_id'])) echo $_POST['piece_id']?>'/>
		<choix titre="Section" id="section" type="combo" valeur="<? if (isset($_POST['section'])) echo $_POST['section']?>">
<?php
			$DB_trombino->query("SELECT section_id,nom FROM sections ORDER BY nom ASC");
			while( list($section_id,$section_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$section_nom\" id=\"$section_id\"/>\n";
?>
		</choix>
		<choix titre="Compagnie" id="cie" type="combo" valeur="<? if (isset($_POST['cie'])) echo $_POST['cie']?>">
<?php
			$DB_trombino->query("SELECT DISTINCT cie FROM eleves ORDER BY cie ASC");
			while( list($cie) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$cie\" id=\"$cie\"/>\n";
?>
		</choix>
		<choix titre="Promo" id="promo" type="combo" valeur="<? if (isset($_POST['promo'])) echo $_POST['promo']?>">
<?php
			$DB_trombino->query("SELECT DISTINCT promo FROM eleves ORDER BY promo DESC");
			while( list($promo) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$promo\" id=\"$promo\"/>\n";
?>
		</choix>

		<champ id='mail' titre='Mail*' valeur='<? if (isset($_POST['mail'])) echo $_POST['mail']?>'/>
		<note>Pour mettre la photo original en JPG !!! : 300x400 max et 200ko max :)</note>
		<fichier id="file" titre="Photo original"/>
		<bouton id='test' titre='Tester'/>
		<bouton id='ajout' titre='Rajouter' onClick="return window.confirm('Voulez vous vraiment ajouter cette personne au trombino ? Normalement ceci n'arrive quasiment jamais donc soyez sur de vous ...')"/>
	</formulaire>
<?
}
?>
</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
