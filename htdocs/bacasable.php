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
	$Log$
	Revision 1.1  2004/11/24 00:26:09  schmurtz
	Debut de gestion de wiki

*/

require "include/wiki.inc.php";

$_POST = $_REQUEST;

if(!isset($_POST['contenu']))
	$_POST['contenu'] = "";

require "include/page_header.inc.php";
?>
<page id="bacasable" titre="Frankiz : bac � sable">
	<cadre id="bacasable" titre="Contenu">
		<?php echo wikiVersXML($_POST['contenu']) ?>
	</cadre>
	<formulaire id="bacasable" titre="Bac � sable" action="bacasable.php">
		<zonetext id="contenu" titre="contenu"><?=$_POST['contenu']?></zonetext>
		<bouton id="tester" titre="Tester"/>
	</formulaire>
</page>
<?php require "include/page_footer.inc.php" ?>
