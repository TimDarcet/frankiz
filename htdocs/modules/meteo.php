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
	Permet de donner la météo sur Paris
	
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
	Revision 1.2  2004/10/28 16:08:14  kikx
	Ne fait qu'une page de fonctions pour la météo car sinon ça devient ingérable

	Revision 1.1  2004/10/28 14:49:47  kikx
	Mise en place de la météo en module : TODO eviter de repliquer 2 fois le code de la météo
	

*/
require_once BASE_LOCAL."/include/meteo_func.inc.php";

echo "<module id=\"meteo\" titre=\"Météo\">\n";

if(!cache_recuperer('meteo',strtotime(date("Y-m-d H:i:00",time()-60*30)))) { // le cache est valide pendant 30min ...
?>
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
	
		echo "<jour date=\"".($i-1)."\">\n" ;
				echo "\t<temperature_hi>".temperature_hi($jour[$i])."</temperature_hi>\n" ;
				echo "\t<temperature_low>".temperature_low($jour[$i])."</temperature_low>\n" ;
				echo "\t<cieljour>".temps($jour[$i],1)."</cieljour>\n" ;
				echo "\t<cielnuit>".temps($jour[$i],2)."</cielnuit>\n" ;
				echo "\t<imagejour>".temps_image($jour[$i],1)."</imagejour>\n" ;
				echo "\t<imagenuit>".temps_image($jour[$i],2)."</imagenuit>\n" ;
		echo "</jour>\n" ;
	}
	?>
	</meteo>
	<?
	cache_sauver('meteo');
}
echo "</module>\n";
?>
