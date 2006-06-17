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

if(isset($_REQUEST['xml'])) {
	echo $xml;
	exit;
}

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
	
// Envoi la page vers le navigateur
affiche_erreurs_php();
echo $resultat;
affiche_debug_php();
?>
