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
	Revision 1.22  2004/12/15 01:36:52  kikx
	Specification de la taille des images sous le WIKI

	Revision 1.21  2004/12/14 22:16:06  schmurtz
	Correction de bug du moteur wiki.
	Simplication du code.
	
	Revision 1.20  2004/12/14 14:18:12  schmurtz
	Suppression de la page de doc wiki : doc directement dans les pages concernees.
	
	Revision 1.19  2004/12/14 12:47:49  pico
	Le wiki devrait marcher beaucoup mieux ainsi....
	
	Revision 1.18  2004/12/13 23:45:49  kikx
	Attention ... je sais pas trop ce que je fais mais qd je le fait ca corrige les bugs de alban
	
	Si pico pouvait m'expliquer le code ca serait cool
	
	Revision 1.17  2004/12/13 20:42:36  pico
	Passage du wiki en <br/> (bcp plus simple)
	
	Revision 1.16  2004/12/01 19:28:47  pico
	Format du wiki: - pour les listes
	
	Revision 1.15  2004/12/01 18:56:37  pico
	Nettoyage
	
	Revision 1.14  2004/12/01 17:38:08  pico
	corrections
	
	Revision 1.13  2004/12/01 17:20:28  pico
	Un oubli, excusez moi
	
	Revision 1.12  2004/12/01 17:06:55  pico
	correction listes html
	
	Revision 1.11  2004/12/01 12:27:45  pico
	Ajout du 2 eme niveau de listes
	
	Revision 1.10  2004/12/01 12:17:53  pico
	Début de mise en forme html des listes (pas de 2eme niveau)
	
	Revision 1.9  2004/12/01 12:06:14  pico
	Gestion des listes à 2 niveaux en wiki
	
	Revision 1.8  2004/11/29 16:51:26  schmurtz
	Correction d'un bug de traduction wiki => xml avec un texte du genre "=== blah ="
	
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
	$regexURL = "((http:\/\/|https:\/\/|ftp:\/\/|mailto:)[\w\.\:\@\?\&\~\%\=\+\-\/\_\;]+)";
	$regexURLText = "([\w\.\:\'\@\?\&\~\%\=\+\-\/\_\ \;\,\$éèàù]+)";
	
	// php-specific
	$filtered = "\n".str_replace("\r\n","\n",$filtered)."\n\n";
	
	// pictures [ url ]
	$filtered = preg_replace("/\[($regexURL\.(png|gif|jpg))\]/i",$enhtml?"<img src=\"\\1\"/>":"<image source=\"\\1\"/>",$filtered);
	
	// pictures [ url | alt ]
	$filtered = preg_replace("/\[($regexURL\.(png|gif|jpg))\|$regexURLText\]/i",$enhtml?"<img src=\"\\1\"alt=\"\\5\"/>":"<image source=\"\\1\" texte=\"\\5\"/>",$filtered);
	
	// pictures [ url | alt | largeur]
	$filtered = preg_replace("/\[($regexURL\.(png|gif|jpg))\|$regexURLText\|([0-9]+)\]/i",$enhtml?"<img src=\"\\1\"alt=\"\\5\" width=\"\\6\"/>":"<image source=\"\\1\" texte=\"\\5\" width=\"\\6\"/>",$filtered);
	
	// pictures [ url | alt | haut | largeur]
	$filtered = preg_replace("/\[($regexURL\.(png|gif|jpg))\|$regexURLText\|([0-9]+)\|([0-9]+)\]/i",$enhtml?"<img src=\"\\1\"alt=\"\\5\" height=\"\\6\" width=\"\\7\"/>":"<image source=\"\\1\" texte=\"\\5\" height=\"\\6\" width=\"\\7\"/>",$filtered);
	
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
		"Titres : ===titre1=== ==titre2== =titre3=&lt;br/&gt;".
		"&lt;/small&gt;</html>";
}

?>