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
	$Log$
	Revision 1.3  2004/12/14 22:16:06  schmurtz
	Correction de bug du moteur wiki.
	Simplication du code.

	Revision 1.2  2004/12/14 14:18:11  schmurtz
	Suppression de la page de doc wiki : doc directement dans les pages concernees.
	
	Revision 1.1  2004/11/24 00:26:09  schmurtz
	Debut de gestion de wiki
	
*/

require "include/wiki.inc.php";

$_POST = $_REQUEST;

if(!isset($_POST['contenu']))
	$_POST['contenu'] = "";

require "include/page_header.inc.php";
?>
<page id="bacasable" titre="Frankiz : bac à sable">
	<cadre id="bacasable" titre="Contenu">
		<?php echo wikiVersXML($_POST['contenu']) ?>
	</cadre>
	<formulaire id="bacasable" titre="Bac à sable" action="bacasable.php">
		<zonetext id="contenu" titre="contenu" type="grand"><?=$_POST['contenu']?></zonetext>
		<bouton id="tester" titre="Tester"/>
	</formulaire>
	<?php affiche_syntaxe_wiki() ?>
</page>
<?php require "include/page_footer.inc.php" ?>
