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
		Fonction pour parser des rss
		
		$Log$
		Revision 1.15  2005/03/23 21:12:12  pico
		Normalement tout ce qui faut pour passer en UTF8

		Revision 1.14  2005/01/19 18:29:15  pico
		Parce que wget c'est quand même mieux que lynx
		
		Revision 1.13  2005/01/19 17:47:46  pico
		Pour que les rss marchent même quand l'entrée est gzippée
		
		Revision 1.12  2005/01/11 13:13:36  pico
		Histoire d'avoir un cache des flux rss
		
		Revision 1.11  2004/11/25 00:16:02  pico
		Ne traite plus le flux rss si celui ci n'est pas du xml valide
		
		Revision 1.10  2004/11/24 23:51:40  pico
		Oubli
		
		Revision 1.9  2004/11/24 23:50:42  pico
		Tri dans le flux css
		
		Revision 1.8  2004/11/24 23:31:11  pico
		Affichage plus correct des rss (les &#232; sont maintenant afichés par le navigateur)
		
		Revision 1.7  2004/11/24 21:51:16  pico
		Passage du mode d'affichage en paramètre dans la xsl
		
		Revision 1.6  2004/11/24 21:09:04  pico
		Sauvegarde avant mise à jour skins
		
		Revision 1.5  2004/11/24 17:15:54  pico
		Marche mieux comme ça, sinon le premier parsage xsl fait de la merde avec les accents
		
		Revision 1.4  2004/11/23 17:36:02  pico
		Rajout de balises link pour les navigateurs texte et pour indiquer la précense du feed rss
		
		Revision 1.3  2004/11/22 21:59:28  pico
		2 modes d'affichage des rss: complet ou liste de liens
		
		Revision 1.2  2004/11/18 12:11:58  pico
		Premier jet de page pour afficher des news externes
		
		Revision 1.1  2004/11/17 22:39:45  pico
		Fonction pour parser des rss extérieures
		
*/

/* 
	mode = '' : 			affichage complet
	mode = 'sommaire' :	seulement les titres et lien vers l'article
*/
function rss_xml($site,$mode = 'complet') {
	// Récupération de la météo
	$proxy = "http://kuzh.polytechnique.fr";
	$port = 8080;
	$date_valide = time()-600; //cache 10min
	if(!(file_exists(BASE_CACHE."rss/".base64_encode($site)) && filemtime(BASE_CACHE."rss/".base64_encode($site)) > $date_valide)) {
		exec("http_proxy=\"$proxy:$port\" wget -O ".BASE_CACHE."rss/".base64_encode($site)." $site");
	}
	
	$fp=fopen(BASE_CACHE."rss/".base64_encode($site),'r');
	$xml = "";
	while(!feof($fp)){
		$xml .= fgets($fp, 4000);
	}
	fclose($fp);
	if(!strstr($xml,"<?xml")){
		copy(BASE_CACHE."rss/".base64_encode($site),BASE_CACHE."rss/".base64_encode($site).".gz");
		exec("cd ".BASE_CACHE."rss/ && gunzip -df ".BASE_CACHE."rss/".base64_encode($site).".gz");
		$fp=fopen(BASE_CACHE."rss/".base64_encode($site),'r');
		$xml = "";
		while(!feof($fp)){
			$xml .= fgets($fp, 4000);
		}
		fclose($fp);
	}
	$xml = strstr($xml,"<?xml");

	// traduction du rss dans notre format
	$p = xml_parser_create();
	xml_parser_set_option($p, XML_OPTION_CASE_FOLDING,0);
	if(xml_parse($p, $xml, true) && strstr($xml,"<rss")){
		$xh = xslt_create();
		xslt_set_encoding($xh, "utf8");
		$params = array('mode'=>$mode);
		echo xslt_process($xh, 'arg:/_xml', BASE_LOCAL.'/include/rss_convert.xsl', NULL, array('/_xml'=>$xml),$params);
		xslt_free($xh);
	}
}
?>
