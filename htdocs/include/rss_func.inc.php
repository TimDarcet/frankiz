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

	$Id$

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
