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
	
	$Log$
	Revision 1.11  2005/01/18 19:30:34  pico
	Place la boite du sudo dans la boite avec les infos de connection.
	Pbs d'encodage des variables passées à sablotron réglés
	Pb du su quand on est pas loggué par mot de passe réglé

	Revision 1.10  2005/01/14 10:07:46  pico
	Passage d'infos à la skin
	
	Revision 1.9  2004/12/16 12:52:57  pico
	Passage des paramètres lors d'un login
	
	Revision 1.8  2004/11/24 20:26:38  schmurtz
	Reorganisation des skins (affichage melange skin/css + depacement des css)
	
	Revision 1.7  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.6  2004/09/20 20:33:47  schmurtz
	Mise en place d'un systeme de cache propre
	
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

if(isset($_REQUEST['xml'])) {
	echo $xml;
	exit;
}

// Application des feuilles de styles XSL
$xh = xslt_create();
xslt_set_encoding($xh, "ISO-8859-1");

// Les paramètres à passer à sablotron sont en UTF8
$parameters = array (
  'user_nom' => utf8_encode($_SESSION['user']->nom),
  'user_prenom' => utf8_encode($_SESSION['user']->prenom),
  'date' => utf8_encode(date("d/m/Y")),
  'heure' => utf8_encode(date("H:i"))
);

$resultat = xslt_process($xh, 'arg:/_xml', $_SESSION['skin']['skin_xsl_chemin'], NULL, array('/_xml'=>$xml),array_merge($_SESSION['skin']['skin_parametres'],$parameters));
echo xslt_error($xh);
xslt_free($xh);

// Envoi la page vers le navigateur
affiche_erreurs_php();
echo $resultat;
?>
