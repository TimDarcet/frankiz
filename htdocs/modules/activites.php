<?php
/*
	Script de création de la partie activités contenant des images type "affiche".
	
	$Id$
*/
?>

<module id="activites" titre="Activités" visible="<?php echo skin_visible("activites"); ?>">
<?php

// Connexion à la base mysql de frankiz
connecter_mysql_frankiz();

$result=mysql_query("SELECT affiche_id,titre,url,DATE_FORMAT(date,'%H:%i')"
				   ."FROM affiches WHERE valide=1 AND TO_DAYS(date)=TO_DAYS(NOW())");

while (list($id,$titre,$url,$heure)=mysql_fetch_row($result)) { ?>
	<annonce date="<?php echo $heure?>">
	<lien url="<?php echo $url?>"><image source="<?php echo BASE_URL.'/data/affiches/'.$id?>.gif" texte="Affiche" legende="<?php echo $titre?>"/></lien>
	</annonce>
<?php }
mysql_free_result($result);

deconnecter_mysql_frankiz();

?>
</module>
