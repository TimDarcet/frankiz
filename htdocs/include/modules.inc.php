<?php
/*
	$Id$
	
	Inclu les modules n�cessaires.
	
	Il est possible d'en modifier le comportement � l'aide de variables GET�:
	- 'modules[blahblah]'�:	'on' pour forc� l'affichage du module 'blahblah',
							'off' pour forcer le non affichage
	- 'modules[tous]'�:		valeur par d�faut pour l'affichage des modules

	$Log$
	Revision 1.10  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"

*/

// Cette directive "global" est utile dans le cas o� cette page est inclue depuis
// header.inc.php lui m�me inclu depuis la fonction demande_authentification()
// de login.inc.php. En effet, dans ce cas PHP consid�re que l'on est dans une fonction
// donc emp�che l'acc�s direct aux variables globales.
global $DB_web,$DB_trombino,$DB_admin;

function existant_et_egal_a($variable,$index,$valeur) {
	return isset($variable[$index]) && $variable[$index] == $valeur;
}

if(!isset($_GET['modules']))
	$_GET['modules'] = array();

foreach(liste_modules() as $module => $modifiable)
	if(		!existant_et_egal_a($_GET['modules'],'tous','off')
			&& (!$modifiable || !existant_et_egal_a($_SESSION['skin']['skin_visible'],$module,false))
		||
			existant_et_egal_a($_GET['modules'],'tous','on')
			&& !existant_et_egal_a($_GET['modules'],$module,'off') )
		require BASE_LOCAL."/modules/$module.php";
?>