<?php
/*
	Script de cr�ation de la partie activit�s contenant des images type "affiche".
	
	$Log$
	Revision 1.8  2004/10/07 22:52:20  kikx
	Correction de la page des activites (modules + proposition + administration)
		rajout de variables globales : DATA_DIR_LOCAL
						DATA_DIR_URL

	Comme ca si ca change, on est safe :)

	Revision 1.7  2004/09/17 22:49:29  kikx
	Rajout de ce qui faut pour pouvoir faire des telechargeement de fichiers via des formulaires (ie des champs 'file' des champ 'hidden') de plus maintenant le formulaire sont en enctype="multipart/form-data" car sinon il parait que ca marche pas !
	
	Revision 1.6  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.5  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

if(est_authentifie(AUTH_MINIMUM)) {
	echo "<module id=\"activites\" titre=\"Activit�s\">\n";

	$DB_web->query("SELECT affiche_id,titre,url "
					   ."FROM affiches WHERE TO_DAYS(date)=TO_DAYS(NOW())");

	while (list($id,$titre,$url)=$DB_web->next_row()) { ?>
		<annonce date="">
		<lien url="<?php echo $url?>"><image source="<?php echo DATA_DIR_URL.'affiches/'.$id?>" texte="Affiche" legende="<?php echo $titre?>"/></lien>
		</annonce>
<?php }

	echo "</module>\n";
}
