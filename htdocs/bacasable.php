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
	Revision 1.5  2005/01/26 13:58:20  pico
	Correction de pbs sur le wiki et les pages qui y font référence
	(genre le <p><h1>blah</h1></p> ...)

	Revision 1.4  2005/01/04 21:44:40  pico
	Remise en place du lien vers l'helpwiki parce que le résumé en bas de page est incomprehensible
	
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
	<cadre id="resultat" titre="Contenu">
		<?php echo wikiVersXML($_POST['contenu']) ?>
	</cadre>
	<formulaire id="form_bacasable" titre="Bac à sable" action="bacasable.php">
		<note>
			Le texte utilise le format wiki rappelé en bas de la page et décrit dans l'<lien url="helpwiki.php" titre="aide wiki"/>
		</note>
		<zonetext id="contenu" titre="contenu" type="grand"><?=$_POST['contenu']?></zonetext>
		<bouton id="tester" titre="Tester"/>
	</formulaire>
	<?php affiche_syntaxe_wiki() ?>
</page>
<?php require "include/page_footer.inc.php" ?>
