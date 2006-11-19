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
	Help WIKI
	
	$Id$

*/

require_once "include/wiki.inc.php";

// génération de la page
require "include/page_header.inc.php";
?>
<page id='aidewiki' titre='Frankiz : aide WIKI'>

<h1>Aide de syntaxe pour wiki</h1>
<h2>Qu'est ce que le wiki</h2>

Le nom Wiki provient d'un adjectif hawaiien wikiwiki, qui signifie rapide. C'est une forme à redoublement de l'adjectif wiki. Ward Cunningham, créateur du système Wiki en 1995, a choisi ce terme pour désigner le premier site utilisant ce principe, le WikiWikiWeb.

<note>Si tu veux tester une page wiki sur ce site, va sur <lien url="bacasable.php" titre="le bac à sable"/></note>

<liste modifiable="non" titre="Syntaxe du WIKI sur ce site">
		<entete id="type" titre=""/>
		<entete id="syntaxe" titre="Syntaxe"/>
		<entete id="exemple" titre="Exemple"/>
		
		<element id="">
			<colonne id="type">Gras</colonne>
			<colonne id="syntaxe">**Gras**</colonne>
			<colonne id="exemple"><?=wikiVersXML("**Gras**")?></colonne>
		</element>
		<element id="">
			<colonne id="type">Italique</colonne>
			<colonne id="syntaxe">''Italique''</colonne>
			<colonne id="exemple"><?=wikiVersXML("&apos;&apos;Italique&apos;&apos;")?></colonne>
		</element>
		<element id="">
			<colonne id="type">Font-Fixed</colonne>
			<colonne id="syntaxe">|Font|</colonne>
			<colonne id="exemple"><?=wikiVersXML("|Font|")?></colonne>
		</element>
		<element id="">
			<colonne id="type">Titre</colonne>
			<colonne id="syntaxe"><p>===titre1===</p><p>==titre2==</p><p>=titre3=</p></colonne>
			<colonne id="exemple"><?=wikiVersXML("===titre1===").wikiVersXML("==titre2==").wikiVersXML("=titre3=")?></colonne>
		</element>
		<element id="">
			<colonne id="type">Liste</colonne>
			<colonne id="syntaxe"><p>- 1 niveau</p><p>- titre niveau 2</p><p>-- 2e niveau</p></colonne>
			<colonne id="exemple"><?=wikiVersXML("- 1 niveau").wikiVersXML("- titre niveau 2\n-- 2e niveau")?></colonne>
		</element>
		<element id="">
			<colonne id="type">Lien</colonne>
			<colonne id="syntaxe"><p>http://frankiz/</p><p>[http://frankiz|Ici c'est le site élève]</p><p>[mailto:moi@moi|Mon email]</p></colonne>
			<colonne id="exemple"><?=wikiVersXML("http://frankiz/").wikiVersXML("[http://frankiz/|Ici c'est le site élève]").wikiVersXML("[mailto:moi@moi|Mon email]")?></colonne>
		</element>
		<element id="">
			<colonne id="type">Image</colonne>
			<colonne id="syntaxe"><p>[http://binets/icones/reseau.png]</p><p>[http://binets/icones/reseau.png|Logo binet réseau]</p></colonne>
			<colonne id="exemple"><?=wikiVersXML("[http://binets/icones/reseau.png]").wikiVersXML("[http://binets/icones/reseau.png|Logo binet réseau]")?></colonne>
		</element>
</liste>

</page>
<?
require_once "include/page_footer.inc.php";
?>
