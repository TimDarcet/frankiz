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
	Revision 1.5  2004/11/04 16:36:42  schmurtz
	Modifications cosmetiques

	Revision 1.4  2004/11/02 13:04:25  pico
	Correction m�t�o (othograffe + skin pico)
	
	Revision 1.3  2004/10/28 16:08:14  kikx
	Ne fait qu'une page de fonctions pour la m�t�o car sinon �a devient ing�rable
	
	Revision 1.2  2004/10/28 11:29:07  kikx
	Mise en place d'un cache pour 30 min pour la m�t�o
	
	Revision 1.1  2004/10/26 17:52:07  kikx
	J'essaie de respecter la charte de weather.com mais c'est chaud car il demande le mettre leur nom en gras ... et je peux pas le faire avec la skin
	
	Revision 1.1  2004/10/26 16:57:44  kikx
	Pour la m�teo ... ca envoie du pat� !!
	
	
*/

require_once "../include/global.inc.php";
require_once "../include/meteo_func.inc.php";

// g�n�ration de la page
require "../include/page_header.inc.php";


?>
<page id='meteo' titre='Frankiz : m�teo'>
<h1>La m�t�o du plat�l</h1>


<?
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

?>

<lien url="http://www.weather.com/?prod=xoap&amp;par=1006415841"><image source="meteo/Weather.com.png"/></lien>
<lien url="http://www.weather.com/?prod=xoap&amp;par=1006415841">M�t�o fournie gr�ce � weather.com&#174;</lien>

</page>
<?
require_once "../include/page_footer.inc.php";
?>