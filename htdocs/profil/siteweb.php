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
		
	$Id$

*/

require_once "../include/global.inc.php";
require_once "../include/transferts.inc.php";

demande_authentification(AUTH_FORT);

$message="";

// Données sur l'utilisateur
$DB_trombino->query("SELECT eleves.nom,prenom,surnom,mail,login,promo,sections.nom,cie,piece_id FROM eleves LEFT JOIN sections USING(section_id) WHERE eleve_id='".$_SESSION['user']->uid."'");
list($nom,$prenom,$surnom,$mail,$login,$promo,$section,$cie,$casert) = $DB_trombino->next_row();

// Modification de la partie "Page perso"

if (isset($_POST['up_page'])) {
	if ((isset($_FILES['file'])) &&($_FILES['file']['name']!='')) {
		$chemin = BASE_PAGESPERSOS."$login-$promo/" ;
		deldir($chemin);
		mkdir ($chemin) ;
		
		$filename = $chemin.$_FILES['file']['name'];
		move_uploaded_file($_FILES['file']['tmp_name'], $filename);
		unzip($filename, $chemin , true);
		$message .= "<commentaire>Ton site personnel vient d'être mis à jour.</commentaire>" ;
	}
}
if(isset($_REQUEST['download_type'])){
	$chemin = BASE_PAGESPERSOS."$login-$promo" ;
	if (is_dir($chemin)) {
		download($chemin,$_REQUEST['download_type'],"PERSO-$login-$promo-".time());
		exit();
	} else {
		$message .= "<warning>Il n'y a aucun fichier sur ton site web.</warning>" ;
	}
}
if(isset($_POST['ext'])){
	$DB_valid->query("SELECT id FROM valid_pageperso WHERE eleve_id='{$_SESSION['user']->uid}'") ;
	$un = $DB_valid->num_rows() ;
	$DB_web->query("SELECT site_id FROM sites_eleves WHERE eleve_id='{$_SESSION['user']->uid}'") ;
	$deux = $DB_valid->num_rows() ;
	
	// On verifie que la personne n'a pas dejà demandé d'avoir un site accessible de l'ext
	if( $un==0 && $deux==0 ) {
		$DB_valid->query("INSERT INTO valid_pageperso SET eleve_id='{$_SESSION['user']->uid}'") ;
		
		$tempo = explode("profil",$_SERVER['REQUEST_URI']) ;

		$contenu = "$nom $prenom ($promo) a demandé que sa page perso apparaisse sur la liste des sites personnels <br><br>".
			"Pour valider ou non cette demande va sur la page suivante : <br>".
			"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_pageperso.php'>".
			"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_pageperso.php</a></div><br><br>" .
			"Cordialement,<br>" .
			"Le Webmestre de Frankiz<br>"  ;
			
		couriel(WEBMESTRE_ID,"[Frankiz] Demande de page perso de $nom $prenom",$contenu,$_SESSION['user']->uid);
		$message .= "<commentaire>Ta demande d'accessibilité depuis l'extérieur a été prise en compte et sera validée dans les meilleurs délai.</commentaire>" ;

	} else if($un != 0) {
		$message .="<warning>Tu as déjà demandé que ton site soit accessible depuis l'extérieur. Ta demande sera validée dans les meilleurs délai.</warning>" ;
	} else {
		$message .="<warning>Ton site est déjà accessible depuis l'extérieur.</warning>" ;
	}
}

// Génération du la page XML
require "../include/page_header.inc.php";
 
?>
<page id="profil" titre="Frankiz : modification du profil">
	<h1>Site Web Personnel</h1>
	<?php echo $message ?>
	
	<formulaire id="mod_pageperso" titre="Ton site web" action="profil/siteweb.php">
		<note>Tu peux soumettre des archives .zip, .tar.gz, .tar ou .tar.bz2 qui seront décompressées (ou tout autre fichier qui sera mis tel quel) <br/>
		Tu remplaceras l'intégralité de ton site perso. <br/>Attention tu es limité à 10Mo.</note>
		<fichier id="file" titre="Ton site" taille="10000000"/>
		<?php
		if (is_dir(BASE_PAGESPERSOS.$login."-".$promo)){
		?>
			<note>Nous te conseillons de sauvegarder ton site avant d'uploader le nouveau en cas de problème.</note>
			<lien titre="Télécharger en .zip" url="profil/siteweb.php?download_type=zip" /><br/>
			<lien titre="Télécharger en .tar.gz" url="profil/siteweb.php?download_type=tar.gz" /><br/>
			<note>
				Si tu souhaites que ton site apparaisse sur la liste des sites élèves visibles de l'extérieur, clique sur le bouton "Extérieur", cette demande est soumise à validation.<br/>
				Dans tous les cas, ton site sera listé sur la liste des sites perso accessibles pour les gens loggués.
			</note>
			<bouton id="ext" titre="Extérieur"/>
		<?php
		}
		?>
		<bouton id="up_page" titre="Upload"/>
	</formulaire>
	<?php
	function parcours_arbo1($rep) {
		if( $dir = opendir($rep) ) {
			while( FALSE !== ($fich = readdir($dir)) ) {
				if ($fich != "." && $fich != "..") {
					$chemin = "$rep/$fich";
					if (is_dir($chemin)) {
						echo "<noeud titre=\"".htmlentities($fich)."\">";
							parcours_arbo1($chemin);
						echo "</noeud>" ;
					} else {
						echo "<feuille titre=\"".htmlentities($fich)."\"></feuille>";
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
