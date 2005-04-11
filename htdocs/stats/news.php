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
        Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.*/
/*
	Statistiques des news
*/

	require_once("../include/global.inc.php");
	require "../include/page_header.inc.php";
	include ("newsmestres.inc");
?>

<page id='statnews' titre='Frankiz : Statistiques des news'>
	<?php if ($newsmestres==1) {
		echo "<h2>Bienvenue à toi ô tres cher Maître</h2>";
		echo "Tu peux aussi consulter ".
		"<a href=\"stats/news_gros_posteurs.php\">la liste des boulets</a>,";
		echo " page réservée aux newsmestres (filtrage par IP).";
		echo "<h3>Etat du serveur de news</h3>";
		include ("server_status");
	} ?>
	
	<h2>Statistiques diverses</h2>
	<?php include("news_data_premiers_posteurs"); ?><br/>
	<image source="stats/news_stats.png" texte="Nombre de postes"/>
	
	<h2>Et avec ceci...</h2>
	<h3>Le mot des newsmestres</h3>
	Pour les windowsiens et les makkeux les newsmestres conseillent d'utiliser
	<a href="http://frankiz/xshare.php?affich_elt=MC8zLzU0LzQ3LzE4NA==">Thunderbird</a> comme lecteur news.<br/>
	Les linuxiens peuvent quant à eux choisir entre 
	<a href="http://knode.sourceforge.net">Knode</a> et
	<a href="http://www.mozilla.org/products/thunderbird/">Thunderbird</a>.
	
	<h3>Ecrire aux newsmestres</h3>
	Pour toute remarque ou demande concernant le serveur de news, tu peux 
	<a href="mailto:news@frankiz">écrire</a>
	aux irresponsables de la page news..

</page>

<?
require_once "../include/page_footer.inc.php";
?>

