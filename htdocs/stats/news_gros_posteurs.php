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
        Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.*/
/*
	Statistique sur les plus gros posteurs
*/
	require_once("../include/global.inc.php");
	require "../include/page_header.inc.php";
	include ("newsmestres.inc");
?>
<page id="grosposteur" titre="Frankiz : Boul�tiseurs de news">
	<?	
		if($newsmestres==1) {
			echo "<h2>Bienvenue � toi � tr�s cher Ma�tre</h2>";
			include("news_data");
		} else {
			echo "<h2>H� h�</h2>";
			echo "Tu viens de te faire avoir... cette page est private";
		}
	?>
</page>
<?php
	require_once "../include/page_footer.inc.php";
?>


