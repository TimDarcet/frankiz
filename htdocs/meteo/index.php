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
			
	$Log$
	Revision 1.6  2004/11/04 22:07:19  schmurtz
	Suppression du parser xml de la meteo : utilisation d'une conversion xsl a
	la place

	Revision 1.5  2004/11/04 16:36:42  schmurtz
	Modifications cosmetiques
	
	Revision 1.4  2004/11/02 13:04:25  pico
	Correction météo (othograffe + skin pico)
	
	Revision 1.3  2004/10/28 16:08:14  kikx
	Ne fait qu'une page de fonctions pour la météo car sinon ça devient ingérable
	
	Revision 1.2  2004/10/28 11:29:07  kikx
	Mise en place d'un cache pour 30 min pour la météo
	
	Revision 1.1  2004/10/26 17:52:07  kikx
	J'essaie de respecter la charte de weather.com mais c'est chaud car il demande le mettre leur nom en gras ... et je peux pas le faire avec la skin
	
	Revision 1.1  2004/10/26 16:57:44  kikx
	Pour la méteo ... ca envoie du paté !!
	
	
*/

require_once "../include/global.inc.php";
require_once "../include/meteo_func.inc.php";

// génération de la page
require "../include/page_header.inc.php";
?>
<page id='meteo' titre='Frankiz : méteo'>
<h1>La météo du platâl</h1>
<?php
	if(!cache_recuperer('meteo',strtotime(date("Y-m-d H:i:00",time()-60*30)))) { // le cache est valide pendant 30min ...
		weather_xml();
		cache_sauver('meteo');
	}
?>
<lien url="http://www.weather.com/?prod=xoap&amp;par=1006415841"><image source="meteo/Weather.com.png"/></lien>
<lien url="http://www.weather.com/?prod=xoap&amp;par=1006415841">Météo fournie grâce à weather.com&#174;</lien>
</page>
<?php require_once "../include/page_footer.inc.php"; ?>