<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	
	$Log$
	Revision 1.2  2004/11/26 22:51:21  pico
	Correction du SU dans les pages d'admin
	Les utilisateurs avec le droit 'affiches' peuvent changer les dates des activit�s qu'ils ont post�es, si celles ci ont �t� pr�alablement valid�es par le br

	Revision 1.1  2004/11/26 22:28:58  pico
	Ajout d'une page pour pouvoir modifier la date d'une activit� ou la supprimer
	
	
*/
	
require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if(verifie_permission('admin'))
	$user_id = '%';
else if(verifie_permission('affiches'))
	$user_id = $_SESSION['user']->uid;
else
	rediriger_vers("/admin/");



// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="planif_affiches" titre="Frankiz : Planifie tes activit�s">
<h1>Planification des activit�s</h1>
<?
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	// Fixe une date de parution � la QDJ
	
	if ($temp[0]=='valid') {
		if((strtotime($_REQUEST['date']) <=time())&&($_REQUEST['date']!="0000-00-00 00:00"))
		{ ?>
			<warning>ERREUR: Veuillez choisir une date sup�rieure � aujourd'hui</warning>
		<?
		}
		else
		{
			$DB_web->query("UPDATE affiches SET date='{$_REQUEST['date']}'WHERE affiche_id='{$temp[1]}'");
	
		?>
			<commentaire>Planification effectu�e</commentaire>
		<?
		}	
	}
	
	// D�placer un jour apr�s
	if ($temp[0]=='augdate') {
		$date1 = date('Y-m-d H:i:s',strtotime(base64_decode($temp[2]))+24*3600);
		$DB_web->query("UPDATE affiches SET date='{$date1}' WHERE affiche_id='{$temp[1]}'");
	}
	
	// D�placer un jour avant
	if ($temp[0]=='reddate') {
		$date1 = date('Y-m-d H:i:s',strtotime(base64_decode($temp[2]))-24*3600);
		$DB_web->query("UPDATE affiches SET date='{$date1}' WHERE affiche_id='{$temp[1]}'");
	}
	
	// Formulaire pour modifier la date de parution de la QDJ d�j� planifi�e
	
	if($temp[0]=='modif') {
		$DB_web->query("SELECT affiche_id,titre,url,date FROM affiches WHERE affiche_id='{$temp[1]}' AND eleve_id LIKE '$user_id'");
		list($id,$titre,$url,$date) = $DB_web->next_row(); 
		$id = $temp[1];
?>
		<warning><p>Cette Activit� est d�j� planifi�e pour le <?echo base64_decode($temp[2]) ?></p></warning>
		<annonce date="<? echo $date ?>">
			<lien url="<?php echo $url?>"><image source="<?php echo DATA_DIR_URL.'affiches/'.$id?>" texte="Affiche" legende="<?php echo $titre?>"/></lien>
		</annonce>
		<formulaire id="affiche_<? echo $id ?>" action="admin/planif_affiches.php">
			<champ id="date" titre="date" valeur="<? echo base64_decode($temp[2]) ?>"/>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider la planification de cette activit� ?')"/>
		</formulaire>
<?	
	}
	
	// Supprime la qdj de la liste
	
	if ($temp[0]=='suppr') {
		$DB_web->query("DELETE FROM affiches WHERE affiche_id='{$temp[1]}'") ;
	

	?>
		<warning><p>Suppression d'une activit�</p></warning>
	<?
	}
}

$date = date("Y-m-d", time());
?>
<commentaire>
	<p>Nous sommes le: <? echo $date ?></p>
</commentaire>
	
<?
// Affiche la planification existante
?>
	<h2>Pr�visions</h2>
<?
$DB_web->query("SELECT affiche_id,titre,url,date FROM affiches WHERE TO_DAYS(date)>=TO_DAYS(NOW()) AND eleve_id LIKE '$user_id'");
while(list($id,$titre,$url,$date) = $DB_web->next_row()){

?>
		<annonce date="<? echo $date ?>">
			<lien url="<?php echo $url?>"><image source="<?php echo DATA_DIR_URL.'affiches/'.$id?>" texte="Affiche" legende="<?php echo $titre?>"/></lien>
		</annonce>
	<formulaire id="<? echo $id ?>" action="admin/planif_affiches.php">
		<textsimple valeur="<? echo $date ?>"/>
		<? if(strtotime($date) >time()-3025 + 24*3600){ ?><bouton titre="Un jour plus t�t" id="reddate_<? echo $id ?>_<? echo base64_encode($date) ?>"/><? } ?>
		<bouton titre="Un jour plus tard" id="augdate_<? echo $id ?>_<? echo base64_encode($date) ?>"/>
		<bouton id='modif_<? echo $id ?>_<? echo base64_encode($date) ?>' titre='Modifier la date manuellement'/>
		<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette qdj ?!!!!!')"/>
		<hidden id="show"/>
	
	</formulaire>
<? 
}
?>

 
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
