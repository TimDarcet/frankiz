<?php
/*
	Script de création de la partie activités contenant des images type "affiche".
	
	$Id$
*/

if(est_authentifie(AUTH_MINIMUM)) {
	echo "<module id=\"activites\" titre=\"Activités\">\n";

	$DB_web->query("SELECT affiche_id,titre,url,DATE_FORMAT(date,'%H:%i')"
					   ."FROM affiches WHERE valide=1 AND TO_DAYS(date)=TO_DAYS(NOW())");

	while (list($id,$titre,$url,$heure)=$DB_web->next_row()) { ?>
		<annonce date="<?php echo $heure?>">
		<lien url="<?php echo $url?>"><image source="<?php echo BASE_URL.'/data/affiches/'.$id?>.gif" texte="Affiche" legende="<?php echo $titre?>"/></lien>
		</annonce>
<?php }

	echo "</module>\n";
}
