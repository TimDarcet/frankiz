<?php
/*
	Page d'entête pour la transformation du XML. Met en place un cache de sortie.
	
	$Id$
*/

require_once "global.inc.php";

// pour utilisation avec afficher_identifiant()
$donnees = array();
ob_start();

// en-tetes XML
echo "<?xml version='1.0' encoding='ISO-8859-1' ?>\n";
echo "<!DOCTYPE frankiz PUBLIC \"-//Binet Reseau//DTD PLIST 1.0//FR\" \"http://frankiz.polytechnique.fr/frankiz.dtd\">\n";
echo "<frankiz base='".BASE_URL."/' css='".$_SESSION['skin']['skin_css']."'>\n";
require "modules.inc.php";
?>
