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
	
	$Log$
	Revision 1.14  2005/01/22 17:58:38  pico
	Modif des images

	Revision 1.13  2005/01/21 20:38:50  pico
	Légende des images pour qu'on sache qui est quoi
	
	Revision 1.12  2005/01/20 20:09:03  pico
	Changement de "Très BRment, l'automate"
	
	Revision 1.11  2005/01/13 17:10:58  pico
	Mails de validations From le validateur qui va plus ou moins bien
	
	Revision 1.10  2004/12/17 17:25:08  schmurtz
	Ajout d'une belle page d'erreur.
	
	Revision 1.9  2004/12/15 17:23:59  pico
	On affiche la photo a sa vraie taille pour valider un changement trombi
	
	Revision 1.8  2004/12/15 12:33:19  pico
	quand on déplace un fichier, on vérifie que ça fout pas la merde ailleurs :(
	
	Revision 1.7  2004/12/13 16:47:07  kikx
	oups !
	
	Revision 1.6  2004/12/13 16:45:05  kikx
	Protection de la validation des photos trombino
	
	Revision 1.5  2004/11/27 15:39:54  pico
	Ajout des droits trombino
	
	Revision 1.4  2004/11/27 15:02:17  pico
	Droit xshare et faq + redirection vers /gestion et non /admin en cas de pbs de droits
	
	Revision 1.3  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.2  2004/10/29 16:04:34  kikx
	Passage en HTML des mail et raison du refus de modificatoin
	
	Revision 1.1  2004/10/25 10:35:49  kikx
	Page de validation (ou pas) des modif de trombi
	

*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('trombino'))
	acces_interdit();

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";
$message ="" ;
?>
<page id="valid_trombi" titre="Frankiz : Valide une modification d'image trombino">
<h1>Validation des modifications des photos trombi</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation d'affiche :)

// Enregistrer ...

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;
	if ($temp[0]=='valid') {
		if (file_exists(DATA_DIR_LOCAL."trombino/a_valider_{$temp[1]}")) {
			$DB_trombino->query("SELECT prenom,nom,promo,login FROM eleves WHERE eleve_id={$temp[1]}") ;
			list($prenom,$nom,$promo,$login) = $DB_trombino->next_row() ;
			rename(DATA_DIR_LOCAL."trombino/a_valider_{$temp[1]}",BASE_PHOTOS."$promo/$login.jpg") ;
			
			$message .= "<commentaire> Image validée pour $prenom $nom</commentaire>" ;
			
			$contenu = "Ton image trombino est validée <br><br>".
			"Cordialement,<br>" .
			"Le Tolmestre<br>"  ;
			couriel($temp[1],"[Frankiz] Ton image trombino est validée",$contenu,TROMBINOMEN_ID);
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?			
		}

	}
	if ($temp[0]=='suppr') {
		if (file_exists(DATA_DIR_LOCAL."trombino/a_valider_{$temp[1]}")) {

			$DB_trombino->query("SELECT prenom,nom,promo,login FROM eleves WHERE eleve_id={$temp[1]}") ;
			list($prenom,$nom,$promo,$login) = $DB_trombino->next_row() ;
			
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
	<?			
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
			<formulaire id="trombi_<? echo $id ?>" titre="<? echo "$prenom $nom (X$promo)"?>" action="admin/valid_trombi.php">
				<image source="trombino.php?image=true&amp;login=<?=$login?>&amp;promo=<?=$promo?>&amp;original=1" texte="photo originale" legende="photo originale" />
				<image source="trombino.php?image=true&amp;login=<?=$login?>&amp;promo=<?=$promo?>" texte="photo actuelle" legende="photo actuelle"/>
				<image source="profil/profil.php?image=true&amp;id=<?=$id ?>" texte="photo à valider" legende="photo à valider"/>
				<zonetext id="refus" titre="La raison du refus si refus"></zonetext>
				<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider cette photo ?')"/>
				<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('Supprimer cette photo ?!)"/>
			</formulaire>
<?
		}
	}
	closedir($dir);
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
