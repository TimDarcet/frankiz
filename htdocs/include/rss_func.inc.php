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
	$proxy = "kuzh.polytechnique.fr";
	$port = 8080;

	$fp = fsockopen($proxy, $port);
	fputs($fp, "GET ".$site." HTTP/1.0\r\nHost: $proxy\r\n\r\n");
	$xml = "";
	while(!feof($fp)){
		$xml .= fgets($fp, 4000);
	}
	$xml = strstr($xml,"<?xml");	// TODO corriger ce gros hack, vérifier aussi que la requète
									// http à réussie
	// traduction du rss dans notre format
	if(strstr($xml,"<rss")){
		$xh = xslt_create();
		xslt_set_encoding($xh, "ISO-8859-1");
		echo xslt_process($xh, 'arg:/_xml', BASE_LOCAL.'/include/rss_convert_'.$mode.'.xsl', NULL, array('/_xml'=>$xml));
		xslt_free($xh);
	}
}
?>
