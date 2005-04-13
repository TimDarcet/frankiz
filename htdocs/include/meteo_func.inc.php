<?/*
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
	Fonction qui donne la météo sur Paris.

	Fonctionnement : Pour obtenir un compte GRATUIT sur www.weather.com aller sur http://www.weather.com/services/oapintellicast.html et remplir le formulaire
		A partir de ce moment vous aurez un partner_id et une clé ! (Elle est privée donc n'utiliser pas celle qui est afficher dans la page merci)
		taper http://xoap.weather.com/search/search?location=[yourlocation] et la page vous retourne du xml avec les id des différents lieu dans le monde qui
		comporte ce nom (choisissez le bon !)
		le reste est bien expliqué
		Sinon pour ce qui concerne la légalité, je crois qu'il faut juste faire apparaitre leur logo en bas ...
		option supplementaire de l'url 
			c=* pour avoir la temperature , l'humidite , etc ...
			unit=m pour avoir les unité en métrique
			dayf=8 pour avoir 7 jour de previsions

	$Log$
	Revision 1.5  2005/04/13 17:10:00  pico
	Passage de tous les fichiers en utf8.

	Revision 1.4  2005/03/23 21:12:12  pico
	Normalement tout ce qui faut pour passer en UTF8
	
	Revision 1.3  2004/11/17 22:27:24  pico
	Corrections et bugfix divers
	
	Revision 1.2  2004/11/04 22:07:19  schmurtz
	Suppression du parser xml de la meteo : utilisation d'une conversion xsl a
	la place
	
	Revision 1.1  2004/10/28 16:08:14  kikx
	Ne fait qu'une page de fonctions pour la météo car sinon ça devient ingérable
	

*/

function weather_xml() {
	// Récupération de la météo
	$proxy = "kuzh.polytechnique.fr";
	$port = 8080;

	$fp = fsockopen($proxy, $port);
	fputs($fp, "GET ".WEATHER_DOT_COM." HTTP/1.0\r\nHost: $proxy\r\n\r\n");
	$xml = "";
	while(!feof($fp)){
		$xml .= fgets($fp, 4000);
	}
	$xml = strstr($xml,"<?xml");	// TODO corriger ce gros hack, vérifier aussi que la requète
									// http à réussie
	
	// traduction de la météo dans notre format
	if(strstr($xml,"<weather")){
		$xh = xslt_create();
		xslt_set_encoding($xh, "utf8");
		echo xslt_process($xh, 'arg:/_xml', BASE_LOCAL.'/include/meteo_convert.xsl', NULL, array('/_xml'=>$xml));
		xslt_free($xh);
	}
}
?>