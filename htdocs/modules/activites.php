<?php
/*
	Script de création de la partie activités contenant des images type "affiche".
	
	$Log$
	Revision 1.7  2004/09/17 22:49:29  kikx
	Rajout de ce qui faut pour pouvoir faire des telechargeement de fichiers via des formulaires (ie des champs 'file' des champ 'hidden') de plus maintenant le formulaire sont en enctype="multipart/form-data" car sinon il parait que ca marche pas !

	Revision 1.6  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.5  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

if(est_authentifie(AUTH_MINIMUM)) {
	echo "<module id=\"activites\" titre=\"Activités\">\n";

	$DB_web->query("SELECT affiche_id,titre,url,DATE_FORMAT(date,'%H:%i')"
					   ."FROM affiches WHERE TO_DAYS(date)=TO_DAYS(NOW())");

	while (list($id,$titre,$url,$heure)=$DB_web->next_row()) { ?>
		<annonce date="<?php echo $heure?>">
		<lien url="<?php echo $url?>"><image source="<?php echo BASE_URL.'/data/affiches/'.$id?>.gif" texte="Affiche" legende="<?php echo $titre?>"/></lien>
		</annonce>
<?php }

	echo "</module>\n";
}
