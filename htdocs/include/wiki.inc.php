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
	Moteur Wiki (TipiWiki)
	
	$Id$

*/

function wikiVersXML($filtered,$enhtml=false) {
	// from Simon Schoar <simon@schoar.de> :
	$regexURL = "((http:\/\/|https:\/\/|ftp:\/\/|mailto:)[\w\.\:\@\?\&\~\%\=\+\-\/\_\;#]+)";
	$regexURLImage = "((http:\/\/|https:\/\/|ftp:\/\/)[\w\.\~\%\+\-\/\_]+)";
	$regexURLText = "([^\[\]\|]+)";
	
	// php-specific
	$filtered = "\n".str_replace("\r\n","\n",$filtered)."\n\n";
	
	// pictures [ url ]
	$filtered = preg_replace("/\[($regexURLImage\.(png|gif|jpg))\]/i",$enhtml?"<img src=\"\\1\"/>":"<image source=\"\\1\"/>",$filtered);
	
	// pictures [ url | alt ]
	$filtered = preg_replace("/\[($regexURLImage\.(png|gif|jpg))\|$regexURLText\]/i",$enhtml?"<img src=\"\\1\"alt=\"\\5\"/>":"<image source=\"\\1\" texte=\"\\5\"/>",$filtered);
	
	// pictures [ url | alt | largeur]
	$filtered = preg_replace("/\[($regexURLImage\.(png|gif|jpg))\|$regexURLText\|([0-9]+)\]/i",$enhtml?"<img src=\"\\1\"alt=\"\\5\" width=\"\\6\"/>":"<image source=\"\\1\" texte=\"\\5\" width=\"\\6\"/>",$filtered);
	
	// pictures [ url | alt | haut | largeur]
	$filtered = preg_replace("/\[($regexURLImage\.(png|gif|jpg))\|$regexURLText\|([0-9]+)\|([0-9]+)\]/i",$enhtml?"<img src=\"\\1\"alt=\"\\5\" height=\"\\6\" width=\"\\7\"/>":"<image source=\"\\1\" texte=\"\\5\" height=\"\\6\" width=\"\\7\"/>",$filtered);
	
	// [ url | link ] external links
	$filtered = preg_replace("/\[$regexURL\|$regexURLText\]/i","<a href=\"\\1\">\\3</a>", $filtered);
	
	// plain urls in the text
	$filtered = preg_replace("/(?<![\"\[])$regexURL(?!\")/","<a href=\"\\0\">\\0</a>",$filtered);
	
	// strip leading and ending line breaks and convert line breaks
	$filtered = preg_replace("/^(\n+)/","",$filtered); 
	$filtered = preg_replace("/(\n+)$/","",$filtered); 
	$filtered = "<p>".preg_replace("/\n+/","</p>\n<p>",$filtered)."</p>";

	// Headlines <h1><h2><h3>
	$filtered = preg_replace("/<p>===([^=].*[^=])===[\t  ]*<\/p>/","<h2>\\1</h2>",$filtered);
	$filtered = preg_replace("/<p>==([^=].*[^=])==[\t  ]*<\/p>/","<h3>\\1</h3>",$filtered);
	$filtered = preg_replace("/<p>=([^=].*[^=])=[\t  ]*<\/p>/","<h4>\\1</h4>",$filtered);

	// lists <ul>
	$filtered = preg_replace("/<p>--(.+)<\/p>/","<ul><li><ul><li>\\1</li></ul></li></ul>",$filtered); // Liste 2 eme niveau
	$filtered = preg_replace("/<p>-(.+)<\/p>/","<ul><li>\\1</li></ul>",$filtered); // Liste 1er niveau
	$filtered = str_replace ("</li></ul></li></ul>\n<ul><li><ul><li>","</li>\n<li>",$filtered);
	$filtered = str_replace ("</li></ul>\n<ul><li>","</li>\n<li>",$filtered);
	$filtered = str_replace ("</li>\n<li><ul>","<ul>",$filtered);
	
	// text decorations (bold,italic,underline,boxed)
	$filtered = preg_replace("/\*\*(.+)\*\*/U","<strong>\\1</strong>", $filtered);
	$filtered = preg_replace("/&apos;&apos;(.+)&apos;&apos;/U","<em>\\1</em>", $filtered);
	$filtered = preg_replace("/\|(.+)\|/U","<code>\\1</code>", $filtered);
	
	// <pre> blocks
	//$filtered = preg_replace("/(?<=\n) (.*)(\n)/","<pre>\\1</pre>", $filtered);
		
	return $filtered;
}

function affiche_syntaxe_wiki() {
	echo "<html>&lt;small&gt;".
		"Formatage : **gras** ' 'italic' ' |code|    Listes : -élément --sousélément &lt;br/&gt;".
		"Liens/image : [http://exemple.fr/|Titre] [http://exemple.fr/image.png]&lt;br/&gt;".
		"Lien pour envoyer un courriel : [mailto:exemple@poly|Mailez-moi] &lt;br/&gt;".
		"Titres : ===titre1=== ==titre2== =titre3=&lt;br/&gt;".
		"&lt;/small&gt;</html>";
}

?>
