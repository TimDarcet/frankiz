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
?>

<page id='statnews' titre='Frankiz : Statistiques des news'>
	<?php if(verifie_permission('admin')||verifie_permission('news')) { ?>
		<h2>Bienvenue à toi ô tres cher Maître</h2>
		Tu peux aussi consulter <a href="stats/news_gros_posteurs.php">la liste des boulets</a> page réservée aux newsmestres.
	<?php } ?>
	
	<h2>Statistiques diverses</h2>
	<h3>Posteurs insomniques</h3>
	<?php include(BASE_CACHE."news_data_premiers_posteurs"); ?><br/>

	<h3>Activité des newsgroups les 4 derniers jours</h3>
	<image source="stats/news_stats.png" texte="Nombre de postes"/>

	<h3>Utilisation des clients news</h3>
	<p>Voici la liste des différents clients utilisés les 10 derniers jours.</p>
	<?php
		$DB_web->query("CREATE TEMPORARY TABLE news_users SELECT client FROM news.news WHERE DATE_SUB(CURDATE(), INTERVAL 10 DAY) <= date AND LENGTH(client) > 0 AND client NOT LIKE 'mail2news' AND forum NOT LIKE 'control.cancel' GROUP BY pseudo_news");
		$DB_web->query("SELECT client, count(*) as c FROM news_users GROUP BY client ORDER BY c DESC");
		$i = 1;
		while(list($client, $count) = $DB_web->next_row())
		{
			if($i == 1) echo "<strong>";
			echo "$i - ".htmlentities(iconv("ISO-8859-1", "UTF-8", $client))." : $count utilisateurs<br/>";
			if($i == 1) echo "</strong>";
			$i++;
		}
		$DB_web->query("DROP TABLE news_users");
	?>
	
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

