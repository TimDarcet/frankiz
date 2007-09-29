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
	$Id$

*/

require "include/wiki.inc.php";

$_POST = $_REQUEST;

if(!isset($_POST['contenu']))
	$_POST['contenu'] = "";

require "include/page_header.inc.php";

class BacASableModule extends PLModule
{
	function run()
	{
		$this->assign('title', "Frankiz : bac à sable");

?>
<page id="bacasable">
	<cadre id="resultat" titre="Contenu">
		<?php echo wikiVersXML($_POST['contenu']) ?>
	</cadre>
	<formulaire id="form_bacasable" titre="Bac à sable" action="bacasable.php">
		<note>
			Le texte utilise le format wiki rappelé en bas de la page et décrit dans l'<lien url="helpwiki.php" titre="aide wiki"/>
		</note>
		<zonetext id="contenu" titre="contenu" type="grand"><?php echo $_POST['contenu']; ?></zonetext>
		<bouton id="tester" titre="Tester"/>
	</formulaire>
	<?php affiche_syntaxe_wiki() ?>
</page>

<?php 
	}
}
$smarty = new BacASableModule;
	
require "include/page_footer.inc.php" ?>
