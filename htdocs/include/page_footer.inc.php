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

$minimodules = FrankizMiniModule::load_modules('activites',
					       'anniversaires',
					       'fetes', 
					       'meteo',
					       'lien_ik', 
					       'lien_tol', 
					       'lien_wikix', 
					       'liens_navigation', 
					       'liens_perso', 
					       'liens_profil', 
					       'liens_utiles',
					       'sondages',
					       'qdj',
					       'qdj_hier',
					       'virus');


// Feuille de style
$dom_xsl = new DOMDocument ();
$dom_xsl->load("skins/xhtml/xsl/skin.xsl");

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
$resultat = $xslt->transformToXML($dom_xml);

if ($resultat === false)
{
	echo "Erreur lors de la transformation XSLT";
}

$page->compile_check = AFFICHER_LES_ERREURS;
$page->template_dir  = BASE_TEMPLATES;
$page->compile_dir   = BASE_CACHE . 'templates_c/';

$page->assign('xml', $resultat);
if (isset($_SESSION['skin']))
	$page->assign('css', $_SESSION['skin']['skin_css_url']);
else
	$page->assign('css', "xhtml/cocoa");
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
