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

// Application des feuilles de styles XSL
$xh = xslt_create();
xslt_set_encoding($xh, "utf8");

// Les paramètres à passer à sablotron sont en UTF8
$parameters = array (
  'user_nom' => $_SESSION['user']->nom,
  'user_prenom' => $_SESSION['user']->prenom,
  'date' => date("d/m/Y"),
  'heure' => date("H:i")
);

$resultat = xslt_process($xh, 'arg:/_xml', $_SESSION['skin']['skin_xsl_chemin'], NULL, array('/_xml'=>$xml),array_merge($_SESSION['skin']['skin_parametres'],$parameters));
echo xslt_error($xh);
xslt_free($xh);

// Envoi la page vers le navigateur
affiche_erreurs_php();
echo $resultat;
?>
