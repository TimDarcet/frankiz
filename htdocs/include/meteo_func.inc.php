<?/*
	Copyright (C) 2004 Binet R�seau
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
	Fonction qui donne la m�t�o sur Paris.

	Fonctionnement : Pour obtenir un compte GRATUIT sur www.weather.com aller sur http://www.weather.com/services/oapintellicast.html et remplir le formulaire
		A partir de ce moment vous aurez un partner_id et une cl� ! (Elle est priv�e donc n'utiliser pas celle qui est afficher dans la page merci)
		taper http://xoap.weather.com/search/search?location=[yourlocation] et la page vous retourne du xml avec les id des diff�rents lieu dans le monde qui
		comporte ce nom (choisissez le bon !)
		le reste est bien expliqu�
		Sinon pour ce qui concerne la l�galit�, je crois qu'il faut juste faire apparaitre leur logo en bas ...
		option supplementaire de l'url 
			c=* pour avoir la temperature , l'humidite , etc ...
			unit=m pour avoir les unit� en m�trique
			dayf=8 pour avoir 7 jour de previsions

	$Log$
	Revision 1.2  2004/11/04 22:07:19  schmurtz
	Suppression du parser xml de la meteo : utilisation d'une conversion xsl a
	la place

	Revision 1.1  2004/10/28 16:08:14  kikx
	Ne fait qu'une page de fonctions pour la m�t�o car sinon �a devient ing�rable
	

*/

function weather_xml() {
	// R�cup�ration de la m�t�o
	$proxy = "kuzh.polytechnique.fr";
	$port = 8080;

	$fp = fsockopen($proxy, $port);
	fputs($fp, "GET ".WEATHER_DOT_COM." HTTP/1.0\r\nHost: $proxy\r\n\r\n");
	$xml = "";
	while(!feof($fp)){
		$xml .= fgets($fp, 4000);
	}
	$xml = strstr($xml,"<?xml");	// TODO corriger ce gros hack, v�rifier aussi que la requ�te
									// http � r�ussie
	
	// traduction de la m�t�o dans notre format
	$xh = xslt_create();
	xslt_set_encoding($xh, "ISO-8859-1");
	echo xslt_process($xh, 'arg:/_xml', BASE_LOCAL.'/include/meteo_convert.xsl', NULL, array('/_xml'=>$xml));
	xslt_free($xh);
}
?>