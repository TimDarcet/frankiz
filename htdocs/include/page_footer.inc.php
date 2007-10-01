<?php
/*
	Copyright (C) 2004 Binet Réseau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
/*
	Pied de page pour la transformation du XML. récupère le cache de sortie et applique une
	transformation XSLT.
	C'est d'ici qu'est appelé la fonction qui affiche les erreurs en haut de la page.
	
	$Id$

*/
echo "</frankiz>\n";
// Récupération du cache de sortie
$xml = ob_get_contents();
ob_end_clean();
header('Content-Type: text/html');

require_once BASE_LOCAL."/include/minimodules.inc.php";

require_once BASE_LOCAL."/modules/liens_perso.php";
require_once BASE_LOCAL."/modules/liens_navigation.php";
require_once BASE_LOCAL."/modules/liens_profil.php";
require_once BASE_LOCAL."/modules/liens_utiles.php";
require_once BASE_LOCAL."/modules/activites.php";
require_once BASE_LOCAL."/modules/sondages.php";
require_once BASE_LOCAL."/modules/fetes.php";
require_once BASE_LOCAL."/modules/lien_tol.php";
require_once BASE_LOCAL."/modules/lien_wikix.php";
require_once BASE_LOCAL."/modules/lienik.php";
require_once BASE_LOCAL."/modules/meteo.php";
require_once BASE_LOCAL."/modules/annonce_virus.php";
require_once BASE_LOCAL."/modules/anniversaires.php";

$minimodules = FrankizMiniModule::load_modules('Activites',
					       'Anniversaires',
					       'Fetes', 
					       'Meteo',
					       'LienIK', 
					       'LienTol', 
					       'LienWikix', 
					       'LiensNavigation', 
					       'LiensPerso', 
					       'LiensProfil', 
					       'LiensUtiles',
					       'Sondages',
					       'Virus');


// Feuille de style
$dom_xsl = new DOMDocument ();
$dom_xsl->load($_SESSION['skin']['skin_xsl_chemin']);

// XML
$dom_xml = new DOMDocument ();
$dom_xml->loadXML($xml);

// Transformer XSLT
$xslt = new XSLTProcessor();
$xslt->importStyleSheet($dom_xsl);

// Les paramètres à passer à sablotron sont en UTF8
$parameters = array (
  'user_nom' => str_replace("&apos;","'",$_SESSION['user']->nom),
  'user_prenom' => str_replace("&apos;","'",$_SESSION['user']->prenom),
  'date' => date("d/m/Y"),
  'heure' => date("H:i")
);

// Transformation
$xslt->setParameter('', array_merge($_SESSION['skin']['skin_parametres'],$parameters));
$resultat = $xslt->transformToXML($dom_xml);

if ($resultat === false)
{
	echo "Erreur lors de la transformation XSLT";
}

$page->compile_check = AFFICHER_LES_ERREURS;
$page->template_dir  = BASE_TEMPLATES;
$page->compile_dir   = BASE_CACHE . 'templates_c/';

$page->assign('xml', $resultat);
$page->assign('css', $_SESSION['skin']['skin_css_url']);
$page->assign('css_list', Skin::get_list());
$page->assign('base', BASE_URL);
$page->assign('session', new Session);
$page->assign('minimodules', $minimodules);
$page->assign('template_name', $page->tpl_name);
if (isset($_SESSION['sueur']))
	$smarty->assign('sueur', $_SESSION['sueur']);

affiche_erreurs_php();
$page->display("main.tpl");
affiche_debug_php();
?>
