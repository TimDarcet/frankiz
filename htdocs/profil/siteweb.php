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
	Pour gerer son site web perso
		
	$Log$
	Revision 1.3  2004/11/24 22:29:52  kikx
	blu

	Revision 1.2  2004/11/24 22:12:57  schmurtz
	Regroupement des fonctions zip unzip deldir et download dans le meme fichier
	
	Revision 1.1  2004/11/24 18:12:27  kikx
	Séparation de la page du site web et du profil personnel
	
	
*/

require_once "../include/global.inc.php";
require_once "../include/transferts.inc.php";

demande_authentification(AUTH_FORT);

$message="";

// Données sur l'utilisateur
$DB_trombino->query("SELECT eleves.nom,prenom,surnom,mail,login,promo,sections.nom,cie,piece_id FROM eleves INNER JOIN sections USING(section_id) WHERE eleve_id='".$_SESSION['user']->uid."'");
list($nom,$prenom,$surnom,$mail,$login,$promo,$section,$cie,$casert) = $DB_trombino->next_row();

// Modification de la partie "Page perso"

if (isset($_POST['up_page'])) {
	if ((isset($_FILES['file'])) &&($_FILES['file']['name']!='')) {
		$chemin = BASE_PAGESPERSOS."$login-$promo/" ;
		deldir($chemin);
		mkdir ($chemin) ;
		
		$filename = $chemin.$_FILES['file']['name'];
		move_uploaded_file($_FILES['file']['tmp_name'], BASE_PAGESPERSOS.$_FILES['file']['name']);
		unzip(BASE_PAGESPERSOS.$_FILES['file']['name'] , $chemin , true);
		$message .= "<commentaire>Ton site personnel vient d'être mis à jour</commentaire>" ;
	}
}
if(isset($_REQUEST['download_type'])){
	$chemin = BASE_PAGESPERSOS."$login-$promo" ;
	if (is_dir($chemin)) {
		download($chemin,$_REQUEST['download_type'],"PERSO-$login-$promo-".time());
		exit();
	} else {
		$message .= "<warning>Tu n'as jamais upoadé de site personnel</warning>" ;
	}
}
if(isset($_POST['ext'])){
	$DB_valid->query("SELECT id FROM valid_pageperso WHERE eleve_id='".$_SESSION['user']->uid."'") ;
	$un = $DB_valid->num_rows() ;
	$DB_web->query("SELECT site_id FROM sites_eleves WHERE eleve_id='".$_SESSION['user']->uid."'") ;
	$deux = $DB_valid->num_rows() ;
	
	// On verifie que la personne n'a pas dejà demandé d'avoir un site accessible de l'ext
	if (($un==0)&&($deux==0)) {
		$DB_valid->query("INSERT INTO valid_pageperso SET eleve_id='".$_SESSION['user']->uid."'") ;
		
		$tempo = explode("profil",$_SERVER['REQUEST_URI']) ;

		$contenu = "$nom $prenom ($promo) a demandé que sa page perso apparaisse sur la liste des sites personnels <br><br>".
			"Pour valider ou non cette demande va sur la page suivante : <br>".
			"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_pageperso.php'>".
			"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_pageperso.php</a></div><br><br>" .
			"Très BR-ement<br>" .
			"L'automate :)<br>"  ;
			
		couriel(WEBMESTRE_ID,"[Frankiz] Demande de page perso de $nom $prenom",$contenu);
		$message .= "<commentaire>Ta demande a été prise en compte et sera validée dans les meilleurs délai... Merci</commentaire>" ;

	} else {
		$message .="<warning>Tu as déjà demandé d'être sur cette liste</warning>" ;
	}
}

// Génération du la page XML
require "../include/page_header.inc.php";
 
?>
<page id="profil" titre="Frankiz : modification du profil">
	<h1>Site Web Personnel</h1>
	<? echo $message ?>
	
	<formulaire id="mod_pageperso" titre="Ton site web" action="profil/siteweb.php">
		<note>Tu peux soumettre des .zip, des .tar.gz, .tar, .tar.bz2. Tu remplaceras ainsi l'intégralité de ton site perso. Attention tu es limité à 10Mo.</note>
		<fichier id="file" titre="Ton site" taille="10000000000"/>
		<?
		if (is_dir(BASE_PAGESPERSOS.$login."-".$promo)){
		?>
			<note>Nous te conseillons de sauvegarder ton site avant d'uploader le nouveau en cas de problème.</note>
			<lien titre="Télécharger en .zip" url="profil/profil.php?download_type=zip" />
			<lien titre="Télécharger en .tar.gz" url="profil/profil.php?download_type=tar.gz" />
			<note>Si tu souhaites que ton site apparaisse sur la liste des sites élèves, clique sur le bouton "Exterieur"</note>
			<bouton id="ext" titre="Exterieur"/>
		<?
		}
		?>
		<bouton id="up_page" titre="Upload"/>
	</formulaire>
	<?
	function parcours_arbo1($rep) {
		if( $dir = opendir($rep) ) {
			while( FALSE !== ($fich = readdir($dir)) ) {
				if ($fich != "." && $fich != "..") {
					$chemin = "$rep/$fich";
					if (is_dir($chemin)) {
						echo "<noeud titre=\"$fich\">";
							parcours_arbo1($chemin);
						echo "</noeud>" ;
					} else {
						echo "<feuille titre=\"$fich\"></feuille>";
					}
				}
			}
		}
	}
	if (is_dir(BASE_PAGESPERSOS.$login."-".$promo)){
		echo "<h2>Gestion des fichiers du site perso</h2>";
		
		echo "<arbre>";
		echo "<noeud titre=\"/\">" ;
		
		$arbo = parcours_arbo1(BASE_PAGESPERSOS.$login."-".$promo);
		echo "</noeud>" ;
		echo "</arbre>";
	}
	?>

</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
