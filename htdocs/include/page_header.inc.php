<?php
/*
	Page d'entête pour la transformation du XML. Met en place un cache de sortie.

	$Log$
	Revision 1.5  2004/09/16 15:32:56  schmurtz
	Suppression de la fonction afficher_identifiant(), utilisation de <![CDATA[......]]> aÌ€ la place.

	Revision 1.4  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.3  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "global.inc.php";

// mise en place du cache de sortie
ob_start();

// en-tetes XML
echo "<?xml version='1.0' encoding='ISO-8859-1' ?>\n";
echo "<!DOCTYPE frankiz PUBLIC \"-//BR//DTD FRANKIZ 1.0//FR\" \"http://frankiz.polytechnique.fr/frankiz.dtd\">\n";
echo "<frankiz base='".BASE_URL."/' css='".$_SESSION['skin']['skin_css']."'>\n";
require "modules.inc.php";
?>
