<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Permet de donner la m�t�o sur Paris (cf. meteo_func.inc.php)
		
	$Log$
	Revision 1.3  2004/11/04 22:07:19  schmurtz
	Suppression du parser xml de la meteo : utilisation d'une conversion xsl a
	la place

	Revision 1.2  2004/10/28 16:08:14  kikx
	Ne fait qu'une page de fonctions pour la m�t�o car sinon �a devient ing�rable
	
	Revision 1.1  2004/10/28 14:49:47  kikx
	Mise en place de la m�t�o en module : TODO eviter de repliquer 2 fois le code de la m�t�o
	

*/
require_once BASE_LOCAL."/include/meteo_func.inc.php";

echo "<module id=\"meteo\" titre=\"M�t�o\">\n";

if(!cache_recuperer('meteo',strtotime(date("Y-m-d H:i:00",time()-60*30)))) { // le cache est valide pendant 30min ...
	weather_xml();
	cache_sauver('meteo');
}

echo "</module>\n";
?>
