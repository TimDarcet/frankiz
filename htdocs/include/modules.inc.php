<?php
/*
	$Id$
	
	Inclu les modules nécessaires.
	
	Il est possible d'en modifier le comportement à l'aide de variables GET :
	- 'modules[blahblah]' :	'on' pour forcé l'affichage du module 'blahblah',
							'off' pour forcer le non affichage
	- 'modules[tous]' :		valeur par défaut pour l'affichage des modules
*/


function existant_et_egal_a($variable,$index,$valeur) {
	return isset($variable[$index]) && $variable[$index] == $valeur;
}

if(!isset($_GET['modules']))
	$_GET['modules'] = array();

foreach(liste_modules() as $module => $modifiable)
	if(		/*!existant_et_egal_a($_GET['modules'],'tous','off')
			&&*/ (!$modifiable || !existant_et_egal_a($_SESSION['skin']['skin_visible'],$module,false))
		/*||
			existant_et_egal_a($_GET['modules'],'tous','on')
			&& !existant_et_egal_a($_GET['modules'],$module,'off')*/ )
		require BASE_LOCAL."/modules/$module.php";
?>