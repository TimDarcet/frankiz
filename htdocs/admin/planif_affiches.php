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
	Page qui permet aux admins de valider une qdj
	
	$Id$
	
*/
	
require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(verifie_permission('admin')||verifie_permission('web'))
	$user_id = '%';
else if(verifie_permission('affiches'))
	$user_id = $_SESSION['uid'];
else
	acces_interdit();



// Génération de la page
//===============
require_once BASE_FRANKIZ."include/page_header.inc.php";

?>
<page id="planif_affiches" titre="Frankiz : Planifie tes activités">
<h1>Planification des activités</h1>
<?php
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	// Fixe une date de parution à la QDJ
	
	if ($temp[0]=='valid') {
		if((strtotime($_REQUEST['date']) <=time())&&($_REQUEST['date']!="0000-00-00 00:00"))
		{ ?>
			<warning>ERREUR: Veuillez choisir une date supérieure à aujourd'hui</warning>
		<?php
		}
		else
		{
			$DB_web->query("UPDATE affiches SET titre='{$_REQUEST['titre']}', url='{$_REQUEST['url']}', description='{$_REQUEST['text']}', date='{$_REQUEST['date']}' WHERE affiche_id='{$temp[1]}'");
	
		?>
			<commentaire>Modification effectuée</commentaire>
		<?php
		}	
	}
	
	// Déplacer un jour après
	if ($temp[0]=='augdate') {
		$date1 = date('Y-m-d H:i:s',strtotime(base64_decode($temp[2]))+24*3600);
		$DB_web->query("UPDATE affiches SET date='{$date1}' WHERE affiche_id='{$temp[1]}'");
	}
	
	// Déplacer un jour avant
	if ($temp[0]=='reddate') {
		$date1 = date('Y-m-d H:i:s',strtotime(base64_decode($temp[2]))-24*3600);
		$DB_web->query("UPDATE affiches SET date='{$date1}' WHERE affiche_id='{$temp[1]}'");
	}
	
	// Formulaire pour modifier la date de parution de la QDJ déjà planifiée
	
	if($temp[0]=='modif') {
		$DB_web->query("SELECT affiche_id,titre,url,date,description FROM affiches WHERE affiche_id='{$temp[1]}' AND eleve_id LIKE '$user_id'");
		list($id,$titre,$url,$date,$texte) = $DB_web->next_row(); 
		$id = $temp[1];
?>
		<warning>Cette Activité est déjà planifiée pour le <?php echo $date ?></warning>
		<annonce date="<?php echo $date ?>">
			<lien url="<?php echo $url?>"><image source="<?php echo DATA_DIR_URL.'affiches/'.$id?>" texte="Affiche" legende="<?php echo $titre?>"/></lien>
			<?php echo wikiVersXML($texte); ?>
		</annonce>
		<formulaire id="affiche_<?php echo $id ?>" action="admin/planif_affiches.php">
			<champ id="date" titre="date" valeur="<?php echo $date ?>"/>
			<champ id="titre" titre="Le titre" valeur="<?php  echo $titre ;?>"/>
			<champ id="url" titre="URL du lien" valeur="<?php echo $url ;?>"/>
			<zonetext id="text" titre="Description plus détaillée"><?php echo $texte; ?></zonetext>
			<champ id="date" titre="Date d'affichage" valeur="<?php echo $date ;?>"/>
			<bouton id='valid_<?php echo $id ?>' titre='Valider' onClick="return window.confirm('Valider la planification de cette activité ?')"/>
		</formulaire>
<?php	
	}
	
	// Supprime la qdj de la liste
	
	if ($temp[0]=='suppr') {
		$DB_web->query("DELETE FROM affiches WHERE affiche_id='{$temp[1]}'") ;
		if (file_exists(DATA_DIR_LOCAL."affiches/{$temp[1]}")){
				unlink(DATA_DIR_LOCAL."affiches/{$temp[1]}") ;
		}
	?>
		<warning>Suppression d'une activité</warning>
	<?php
	}

}

$date = date("Y-m-d", time());
?>
<commentaire>
	Nous sommes le: <?php echo $date ?>
</commentaire>

<?php


// Affiche la planification existante
?>
	<h2>Prévisions</h2>
<?php
$DB_web->query("SELECT affiche_id,titre,url,date,description FROM affiches WHERE TO_DAYS(date)>=TO_DAYS(NOW()) AND eleve_id LIKE '$user_id'");
while(list($id,$titre,$url,$date,$texte) = $DB_web->next_row()){

?>
		<annonce date="<?php echo $date ?>">
			<lien url="<?php echo $url?>"><image source="<?php echo DATA_DIR_URL.'affiches/'.$id?>" texte="Affiche" legende="<?php echo $titre?>"/></lien>
			<?php echo wikiVersXML($texte); ?>
		</annonce>
	<formulaire id="<?php echo $id ?>" action="admin/planif_affiches.php">
		<note><?php echo $date ?></note>
		<?php if(strtotime($date) >time() + 24*3600){ ?><bouton titre="Un jour plus tôt" id="reddate_<?php echo $id ?>_<?php echo base64_encode($date) ?>"/><?php } ?>
		<bouton titre="Un jour plus tard" id="augdate_<?php echo $id ?>_<?php echo base64_encode($date) ?>"/>
		<bouton id='modif_<?php echo $id ?>_<?php echo base64_encode($date) ?>' titre="Modifier l'activité"/>
		<bouton id='suppr_<?php echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette affiche ?!!!!!')"/>
		<hidden id="show"/>
	
	</formulaire>
<?php 
}
?>

 
</page>

<?php
require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
