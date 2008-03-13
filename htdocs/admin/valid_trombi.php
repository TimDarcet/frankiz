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
	Page qui permet aux admins de valider une activité
	
	$Id$

*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin')&&!verifie_permission('trombino'))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_FRANKIZ."htdocs/include/page_header.inc.php";
$message ="" ;
?>
<page id="valid_trombi" titre="Frankiz : Valide une modification d'image trombino">
<h1>Validation des modifications des photos trombi</h1>

<?php
// On traite les différents cas de figure d'enrigistrement et validation d'affiche :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;
	if ($temp[0]=='valid') {
		if (file_exists(DATA_DIR_LOCAL."trombino/a_valider_{$temp[1]}")) {
			
			$DB_trombino->query("SELECT prenom,nom,promo,login FROM eleves WHERE eleve_id={$temp[1]}") ;
			list($prenom,$nom,$promo,$login) = $DB_trombino->next_row() ;
			
			//Log l'action de l'admin
			log_admin($_SESSION['uid'],"validé l'image trombi de $prenom $nom") ;
			
			rename(DATA_DIR_LOCAL."trombino/a_valider_{$temp[1]}",BASE_PHOTOS."$promo/$login.jpg") ;
			
			$message .= "<commentaire> Image validée pour $prenom $nom</commentaire>" ;
			
			$contenu = "Ton image trombino est validée.<br>".
				   "Si tu as l'impression que ta photo n'a pas changé, n'oublie pas de recharger ".
				   "le cache de ton navigateur (Ctrl+F5 en général).<br><br>".
			           "Cordialement,<br>" .
				   "Le Tolmestre<br>"  ;
			couriel($temp[1],"[Frankiz] Ton image trombino est validée",$contenu,TROMBINOMEN_ID);
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?php			
		}

	}
	if ($temp[0]=='suppr') {
		if (file_exists(DATA_DIR_LOCAL."trombino/a_valider_{$temp[1]}")) {
			

			$DB_trombino->query("SELECT prenom,nom,promo,login FROM eleves WHERE eleve_id={$temp[1]}") ;
			list($prenom,$nom,$promo,$login) = $DB_trombino->next_row() ;
			
			//Log l'action de l'admin
			log_admin($_SESSION['uid']," refusé l'image trombi de $prenom $nom") ;
			
			unlink(DATA_DIR_LOCAL."trombino/a_valider_{$temp[1]}") ;
			
			$message .= "<warning> Image non  validée pour $prenom $nom</warning>" ;
			
			$contenu = "Ton image trombino n'est pas validée pour la raison suivante ;<br>".
			$_POST['refus']."<br><br>".
			"Cordialement,<br>" .
			"Le Tolmestre<br>"  ;
			couriel($temp[1],"[Frankiz] Ton image trombino n'est pas validée",$contenu,TROMBINOMEN_ID);
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?php			
		}
	
	}
}


//===============================
	$rep = BASE_DATA."trombino/";
	$dir = opendir($rep); 
	
	echo $message ;
	
	while ($namefile = readdir($dir)) {
		$namefile = explode("_",$namefile) ;
		if ((count($namefile)>=2)&&($namefile[1]=="valider")) {
			$id = $namefile[2] ;
			$DB_trombino->query("SELECT prenom,nom,promo,login FROM eleves WHERE eleve_id=$id") ;
			list($prenom,$nom,$promo,$login) = $DB_trombino->next_row() ;

?>
			<formulaire id="trombi_<?php echo $id ?>" titre="<?php echo "$prenom $nom (X$promo)"?>" action="admin/valid_trombi.php">
				<image source="trombino.php?image=true&amp;login=<?php echo $login; ?>&amp;promo=<?php echo $promo; ?>&amp;original=1" texte="photo originale" legende="photo originale" />
				<image source="trombino.php?image=true&amp;login=<?php echo $login; ?>&amp;promo=<?php echo $promo; ?>" texte="photo actuelle" legende="photo actuelle"/>
				<image source="profil/profil.php?image=true&amp;id=<?php echo $id; ?>" texte="photo à valider" legende="photo à valider"/>
				<zonetext id="refus" titre="La raison du refus si refus"></zonetext>
				<bouton id='valid_<?php echo $id ?>' titre='Valider' onClick="return window.confirm('Valider cette photo ?')"/>
				<bouton id='suppr_<?php echo $id ?>' titre='Supprimer' onClick="return window.confirm('Supprimer cette photo ?!)"/>
			</formulaire>
<?php
		}
	}
	closedir($dir);
?>
</page>

<?php
require_once BASE_FRANKIZ."htdocs/include/page_footer.inc.php";
?>
