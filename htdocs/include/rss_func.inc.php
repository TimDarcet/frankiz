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
	$proxy = "kuzh.polytechnique.fr";
	$port = 8080;

	$fp = fsockopen($proxy, $port);
	fputs($fp, "GET ".$site." HTTP/1.0\r\nHost: $proxy\r\n\r\n");
	$xml = "";
	while(!feof($fp)){
		$xml .= fgets($fp, 4000);
	}
	$xml = strstr($xml,"<?xml");
	$xml = html_entity_decode ($xml,ENT_NOQUOTES);
	$xml =  str_replace(array('&(^#)','&nbsp;'),array('&amp;',' '),$xml);

	// traduction du rss dans notre format
	if(strstr($xml,"<rss")){
		$xh = xslt_create();
		xslt_set_encoding($xh, "ISO-8859-1");
		$params = array('mode'=>$mode);
		echo xslt_process($xh, 'arg:/_xml', BASE_LOCAL.'/include/rss_convert.xsl', NULL, array('/_xml'=>$xml),$params);
		xslt_free($xh);
	}
}
?>
