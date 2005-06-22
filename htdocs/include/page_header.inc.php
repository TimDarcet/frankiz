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

	$Id$

*/
require_once "global.inc.php";

// mise en place du cache de sortie
ob_start();

if (isset($_GET['forceskin']) && is_dir(BASE_LOCAL."/skins/{$_GET['forceskin']}")) {
	$_SESSION['skin']['skin_css_url'] = BASE_URL."/skins/{$_SESSION['skin']['skin_nom']}/default/style.css";
	$_SESSION['skin']['skin_xsl_chemin'] = BASE_LOCAL."/skins/{$_GET['forceskin']}/xsl/skin.xsl";
}

// en-tetes XML
echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
echo "<!DOCTYPE frankiz PUBLIC \"-//BR//DTD FRANKIZ 1.0//FR\" \"http://frankiz.polytechnique.fr/frankiz.dtd\">\n";
echo "<frankiz base='".BASE_URL."/' css='{$_SESSION['skin']['skin_css_url']}'>\n";

require "modules.inc.php";
?>
