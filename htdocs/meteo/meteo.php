<?php
/*
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
	Permet de donner la m�t�o sur Paris
	
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
	Revision 1.1  2004/10/26 16:57:44  kikx
	Pour la m�teo ... ca envoie du pat� !!

	
*/

require_once "../include/global.inc.php";

// g�n�ration de la page
require "../include/page_header.inc.php";

function weather_xml(){

	
	$proxy = "kuzh.polytechnique.fr";
	$port = 8080;

	/*the url you want to connect to*/
	$url = "http://xoap.weather.com/weather/local/FRXX0076?prod=xoap&par=1006415841&key=5064537abefac140&unit=m&cc=*&dayf=8";
	$fp = fsockopen($proxy, $port);
	fputs($fp, "GET $url HTTP/1.0\r\nHost: $proxy\r\n\r\n");
	$line = "" ;
	while(!feof($fp)){
  		$line .= fgets($fp, 4000);
	}
	return $line; 
}

//Parser de merde qui permet a faible cout de prendre ce qui se situe entre les balise xml
// Il suffit de lui donner en arguments $separateur = separateur1&&separateur2&&....

function parser($separateur,$string){
	$sep = explode("&&",$separateur) ;
	$temp = $string ;
	foreach($sep AS $key => $valeur) {
		$tempo = explode("<".$valeur,$temp) ;
		$i=1 ;
		// On boucle car on peut avoir <balise> ou <balise [option]> et comme on match sur
		// "<balise" il se peut tres bien que l'on tombe sur "<balisepluslongue" qu'il ne faut pas matcher
		while ((substr($tempo[$i],0,1)!=" ")&&(substr($tempo[$i],0,1)!=">")) {
			$i++ ;
		}
		$tempo = explode(">",$tempo[$i],2) ;

		$tempo = explode("</".$valeur.">",$tempo[1]) ;
		$temp = $tempo[0] ;
	}
	return $temp ;
}

function leve_soleil($string) {
	return parser("loc&&sunr",$string) ;
}
function couche_soleil($string) {
	return parser("loc&&suns",$string) ;
}
function temperature($string) {
	return parser("cc&&tmp",$string) ;
}
function temperature_hi($string) {
	return  parser("hi",$string) ;
}
function temperature_low($string) {
	return  parser("low",$string) ;
}
function bar($string) {
	return parser("cc&&bar&&r",$string) ;
}
function temps_image($string,$i=0) {
	if ($i==0){
		return parser("cc&&icon",$string) ;
	} else if ($i==1) { 	// C'est pour les previsions de la journ�e
		$temp = explode('part p="n"',$string) ;
		return parser("icon",$temp[0]) ;
	} else {				// pour les prevision de la nuit sinon
		$temp = explode('part p="n"',$string) ;
		return parser("icon",$temp[1]) ;			
	}
}
function temps($string,$i=0) {
	if ($i==0){
		$temp = parser("cc&&icon",$string) ;
	} else if ($i==1) { 	// C'est pour les previsions de la journ�e
		$temp = explode('part p="n"',$string) ;
		$temp = parser("icon",$temp[0]) ;
	} else {				// pour les prevision de la nuit sinon
		$temp = explode('part p="n"',$string) ;
		$temp = parser("icon",$temp[1]) ;			
	}
	switch ($temp) {
		case 31: 	return "Ciel d�couvert" ;
		case 32: 	return "Ciel d�couvert" ;
		case 33: 	return "Ciel d�couvert" ;
		case 36: 	return "Ciel d�couvert" ;
	
		case 0: 	return "Orage avec Pluie" ;
		case 17:	return "Orage avec Pluie" ;
		case 4: 	return "Orage avec Pluie" ;
		case 3: 	return "Orage avec Pluie" ;
		case 35: 	return "Orage avec Pluie" ;
		case 47: 	return "Orage avec Pluie" ;
		
		case 37: 	return "Orage" ;
		case 38: 	return "Orage" ;

		case 29: 	return "L�gers nuages" ;
		case 34: 	return "L�gers nuages" ;
		
		case 44: 	return "Quelques nuages" ;
		case 30: 	return "Quelques nuages" ;
		
		case 26: 	return "Nuageux" ;
		case 27: 	return "Nuageux" ;
		
		case 28: 	return "Nombreux nuages" ;
		
		case 12: 	return "Faible pluie" ;
		
		case 12: 	return "Pluie" ;
		case 39: 	return "Pluie" ;
		case 40: 	return "Pluie" ;
		case 45: 	return "Pluie" ;
		
		case 45: 	return "Pluie ou neige" ;

		case 1: 	return "Pluie avec vent" ;
		case 2: 	return "Pluie avec vent" ;
		
		case 18: 	return "Gr�le" ;
		case 6: 	return "Gr�le" ;
		
		case 10: 	return "Pluie givrante" ;
		
		case 11: 	return "Faible pluie" ;

		case 7: 	return "Pluie ou neige givrante" ;

		case 13: 	return "Neige faible" ;
		
		case 14: 	return "Chute de neige" ;
		case 41: 	return "Chute de neige" ;
		case 42: 	return "Chute de neige" ;
		case 46: 	return "Chute de neige" ;
		
		case 43: 	return "Chute de neige et vent" ;

		case 15: 	return "Forte chute de neige" ;
		case 16: 	return "Forte chute de neige" ;
			
		case 19: 	return "Brume" ;
		case 20: 	return "Brume" ;
		case 21: 	return "Brume" ;
		case 22: 	return "Brume" ;

		case 9: 	return "Brume / Pluie" ;

		case 8: 	return "Brume givrante" ;
			
		case 23: 	return "Venteux" ;
		case 24: 	return "Venteux" ;
			
		case 25: 	return "Givre probable" ;
			
	}
	return "?" ;
}
function vent($string) {
	$temp =  parser("cc&&wind&&s",$string) ;
	$temp2 = parser("cc&&wind&&t",$string) ;
	return $temp." km/h ".$temp2 ;
}
function humidite($string) {
	return parser("cc&&hmid",$string) ;
}

?>
<page id='meteo' titre='Frankiz : m�teo'>
<h1>La m�t�o du plat�l</h1>
<meteo>
	<now>
<?
$xml = weather_xml() ;

	echo "<sunrise>".leve_soleil($xml)."</sunrise>\n" ;
	echo "<sunset>".couche_soleil($xml)."</sunset>\n" ;
	echo "<temperature>".temperature($xml)."</temperature>\n" ;
	echo "<ciel>".temps($xml)."</ciel>\n" ;
	echo "<image>".temps_image($xml)."</image>\n" ;
	echo "<pression>".bar($xml)."</pression>\n" ;
	echo "<vent>".vent($xml)."</vent>\n" ;
	echo "<humidite>".humidite($xml)."</humidite>\n" ;
?>
	</now>
<?

$jour = explode("day d",$xml) ;

for ($i=1; $i<=8 ; $i++){

	echo "<jour date=\"".($i-1)."\">" ;
			echo "<temperature_hi>".temperature_hi($jour[$i])."</temperature_hi>\n" ;
			echo "<temperature_low>".temperature_low($jour[$i])."</temperature_low>\n" ;
			echo "<cieljour>".temps($jour[$i],1)."</cieljour>\n" ;
			echo "<cielnuit>".temps($jour[$i],2)."</cielnuit>\n" ;
			echo "<imagejour>".temps_image($jour[$i],1)."</imagejour>\n" ;
			echo "<imagenuit>".temps_image($jour[$i],2)."</imagenuit>\n" ;
	echo "</jour>" ;
}
?>
</meteo>
</page>
<?
require_once "../include/page_footer.inc.php";
?>