<?php
/*
	$Id$
	
	Inclu les modules nécessaires.
	
	Il est possible d'en modifier le comportement à l'aide de variables GET :
	- 'modules[blahblah]' :  'on' pour forcé l'affichage du module 'blahblah',
							'off' pour forcer le non affichage
	- 'modules[tous]' :		valeur par défaut pour l'affichage des modules
*/

$modules = array('css','liens_navigation','liens_contacts','liens_ecole','qdj','qdj_hier',
				 'activites','tour_kawa','anniversaires','stats');

if(!isset($_GET['modules']))
	$_GET['modules'] = array();

foreach($modules as $module)
	if(		(!array_key_exists('tous',$_GET['modules']) || $_GET['modules']['tous']!='off') && ($module == 'css' || $module == 'liens_navigation' || skin_visible($module)=='true')
		 || array_key_exists('tous',$_GET['modules']) && $_GET['modules']['tous']=='on' && $_GET['modules'][$module]!='off')
		require BASE_LOCAL."/modules/$module.php";
?>