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
	function qui donne la météo sur Paris

	$Log$
	Revision 1.1  2004/10/28 16:08:14  kikx
	Ne fait qu'une page de fonctions pour la météo car sinon ça devient ingérable


*/


function weather_xml(){
	$proxy = "kuzh.polytechnique.fr";
	$port = 8080;

	/*the url you want to connect to*/
	$url = WEATHER_DOT_COM ;
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
	} else if ($i==1) { 	// C'est pour les previsions de la journée
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
	} else if ($i==1) { 	// C'est pour les previsions de la journée
		$temp = explode('part p="n"',$string) ;
		$temp = parser("icon",$temp[0]) ;
	} else {				// pour les prevision de la nuit sinon
		$temp = explode('part p="n"',$string) ;
		$temp = parser("icon",$temp[1]) ;			
	}
	switch ($temp) {
		case 31: 	return "Ciel découvert" ;
		case 32: 	return "Ciel découvert" ;
		case 33: 	return "Ciel découvert" ;
		case 36: 	return "Ciel découvert" ;
	
		case 0: 	return "Orage avec Pluie" ;
		case 17:	return "Orage avec Pluie" ;
		case 4: 	return "Orage avec Pluie" ;
		case 3: 	return "Orage avec Pluie" ;
		case 35: 	return "Orage avec Pluie" ;
		case 47: 	return "Orage avec Pluie" ;
		
		case 37: 	return "Orage" ;
		case 38: 	return "Orage" ;

		case 29: 	return "Légers nuages" ;
		case 34: 	return "Légers nuages" ;
		
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
		
		case 18: 	return "Grêle" ;
		case 6: 	return "Grêle" ;
		
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