<?php
/*
	Pied de page pour la transformation du XML. récupère le cache de sortie et applique une
	transformation XSLT.
	C'est d'ici qu'est appelé la fonction qui affiche les erreurs en haut de la page.
	
	$Log$
	Revision 1.5  2004/09/16 15:32:56  schmurtz
	Suppression de la fonction afficher_identifiant(), utilisation de <![CDATA[......]]> aÌ€ la place.

	Revision 1.4  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.3  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

echo "</frankiz>\n";

// Récupération du cache de sortie
$xml = ob_get_contents();
ob_end_clean();
ob_end_flush();

if(isset($_GET['xml'])) {
	echo $xml;
	exit;
}

// Application des feuilles de styles XSL
$xh = xslt_create();
xslt_set_encoding($xh, "ISO-8859-1");

$resultat = xslt_process($xh, 'arg:/_xml', BASE_LOCAL.'/skins/'.$_SESSION['skin']['skin_nom'].'/skin.xsl', NULL, array('/_xml'=>$xml),$_SESSION['skin']['skin_parametres']);
echo xslt_error($xh);
xslt_free($xh);

// Envoi la page vers le navigateur
affiche_erreurs_php();
echo $resultat;
?>
