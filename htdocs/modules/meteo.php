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
	Permet de donner la météo sur Paris (cf. meteo_func.inc.php)
		
	$Id$

*/
require_once BASE_LOCAL."/include/meteo_func.inc.php";

echo "<module id=\"meteo\" titre=\"Météo\">\n";

if(!cache_recuperer('meteo',strtotime(date("Y-m-d H:i:00",time()-60*30)))) { // le cache est valide pendant 30min ...
	weather_xml();
	cache_sauver('meteo');
}

echo "</module>\n";
?>
