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
	
	$Log$
	Revision 1.7  2004/11/28 01:33:32  pico
	Gestion des listes sur le wiki (arbre + feuille)

	Revision 1.6  2004/11/28 00:06:32  pico
	Ajout des images avec légende (et donc "alt") dans le wiki
	
	Revision 1.5  2004/11/27 23:30:34  pico
	Passage des xshare et faq en wiki
	Ajout des images dans l'aide du wiki
	
	Revision 1.4  2004/11/27 18:46:50  pico
	Correction wiki: gestion des liens (génère du xml et plus des balises <a>)
	Correction des skins pour validité xhtml
	
	Revision 1.3  2004/11/25 00:26:55  schmurtz
	Permet de convertir le wiki en html veritable.
	
	Revision 1.2  2004/11/24 12:51:02  kikx
	Pour commencer la compatibilité wiki
	
	Revision 1.1  2004/11/24 00:26:09  schmurtz
	Debut de gestion de wiki
	
*/

function wikiVersXML($filtered,$enhtml=false) {
	// from Simon Schoar <simon@schoar.de> :
	$regexURL = "((http|https|ftp|mailto):\/\/[\w\.\:\@\?\&\~\%\=\+\-\/\_\;]+)";
	$regexURLText = "([\w\.\:\'\@\?\&\~\%\=\+\-\/\_\ \;\,\$éèàù]+)";
	
	// php-specific
	$filtered = "\n".str_replace("\r\n","\n",$filtered)."\n";
	
	// pictures [ url ]
	$filtered = preg_replace("/\[($regexURL\.(png|gif|jpg))\]/i",$enhtml?"<img src=\"\\1\"/>":"<image source=\"\\1\"/>",$filtered);
	
	// pictures [ url | alt ]
	$filtered = preg_replace("/\[($regexURL\.(png|gif|jpg))\|$regexURLText\]/i",$enhtml?"<img src=\"\\1\"alt=\"\\5\"/>":"<image source=\"\\1\" legende=\"\\5\"/>",$filtered);
	
	// [ url | link ] external links
	$filtered = preg_replace("/\[$regexURL\|$regexURLText\]/i",$enhtml?"<a href=\"\\1\">\\3</a>":"<lien url=\"\\1\">\\3</lien>", $filtered);
	
	// plain urls in the text
	$filtered = preg_replace("/(?<![\"\[])$regexURL(?!\")/",$enhtml?"<a href=\"\\0\">\\0</a>":"<lien url=\"\\0\">\\0</lien>",$filtered);
	
	// Headlines <h1><h2><h3>
	$filtered = preg_replace("/\n===(.+)===[\t  ]*\n/","</p>\n<h2>\\1</h2>\n<p>",$filtered);
	$filtered = preg_replace("/\n==(.+)==[\t  ]*\n/","</p>\n<h3>\\1</h3>\n<p>",$filtered);
	$filtered = preg_replace("/\n=(.+)=[\t  ]*\n/","</p>\n<h4>\\1</h4>\n<p>",$filtered);

	// text decorations (bold,italic,underline,boxed)
	$filtered = preg_replace("/\*\*(.+)\*\*/U","<strong>\\1</strong>", $filtered);
	$filtered = preg_replace("/&apos;&apos;(.+)&apos;&apos;/U","<em>\\1</em>", $filtered);
	$filtered = preg_replace("/\|(.+)\|/U","<code>\\1</code>", $filtered);

	// lists <ul>
	$filtered = preg_replace("/(?<=[\n>])\* (.+)\n/","<feuille>\\1</feuille>",$filtered);
	$filtered = preg_replace("/<feuille>(.+)\<\/feuille>/","</p><arbre><noeud>\\0</noeud></arbre><p>",$filtered);
	
	// strip leading and ending line breaks
	$filtered = preg_replace("/^(\n+)/","",$filtered); 
	$filtered = preg_replace("/\n{3,}/","<p> </p>",$filtered); 
	
	
	// <pre> blocks
	//$filtered = preg_replace("/(?<=\n) (.*)(\n)/","<pre>\\1</pre>", $filtered);
	
	// ad html line breaks
	$filtered = str_replace("\n","</p><p>\n",$filtered);
	
	// html beauty
	$filtered = "<p>".$filtered."</p>\n";
	//$filtered = str_replace("</li>","</li>\n",$filtered);
	//$filtered = str_replace("ul>","ul>\n",$filtered);
	$filtered = str_replace("</p><p>\n<h","\n<h", $filtered);
	$filtered = preg_replace("/(<\/h[1-6]>)<\/p><p>\n/","\\1\n", $filtered);
	$filtered = preg_replace("/<p>\n*<\/p>/","",$filtered);
	
	return $filtered;
}


?>