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
	Page d'entête pour la transformation du XML. Met en place un cache de sortie.

	$Log$
	Revision 1.12  2005/04/13 17:10:00  pico
	Passage de tous les fichiers en utf8.

	Revision 1.11  2005/04/11 20:29:27  pico
	Passage en utf8 suite Ã  une connerie
	
	Revision 1.10  2005/02/08 21:57:56  pico
	Correction bug #62
	
	Revision 1.9  2005/01/18 19:30:34  pico
	Place la boite du sudo dans la boite avec les infos de connexion.
	Pbs d'encodage des variables passées à sablotron réglés
	Pb du su quand on est pas loggué par mot de passe réglé
	
	Revision 1.8  2004/11/24 20:26:38  schmurtz
	Reorganisation des skins (affichage melange skin/css + depacement des css)
	
	Revision 1.7  2004/11/13 00:12:24  schmurtz
	Ajout du su
	
	Revision 1.6  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.5  2004/09/16 15:32:56  schmurtz
	Suppression de la fonction afficher_identifiant(), utilisation de <![CDATA[......]]> aÌ la place.
	
	Revision 1.4  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.3  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
require_once "global.inc.php";

// mise en place du cache de sortie
ob_start();

// en-tetes XML
echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
echo "<!DOCTYPE frankiz PUBLIC \"-//BR//DTD FRANKIZ 1.0//FR\" \"http://frankiz.polytechnique.fr/frankiz.dtd\">\n";
echo "<frankiz base='".BASE_URL."/' css='".$_SESSION['skin']['skin_css_url']."'>\n";

require "modules.inc.php";
?>
