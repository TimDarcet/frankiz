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

require_once "include/global.inc.php";
require_once BASE_FRANKIZ."include/meteo_func.inc.php";

// génération de la page
require BASE_FRANKIZ."include/page_header.inc.php";
?>
<page id='meteo' titre='Frankiz : méteo'>
<h1>La météo du platâl</h1>
<?php
	if(!cache_recuperer('meteo',strtotime(date("Y-m-d H:i:00",time()-60*30)))) { // le cache est valide pendant 30min ...
		weather_xml();
		cache_sauver('meteo');
	}
?>
<lien url="http://www.weather.com/?prod=xoap&amp;par=1006415841"><image source="images/Weather.com.png" texte="Logo Weather.com"/></lien><br/>
<lien url="http://www.weather.com/?prod=xoap&amp;par=1006415841">Météo fournie grâce à weather.com&#174;</lien>
</page>
<?php require_once BASE_FRANKIZ."include/page_footer.inc.php"; ?>
