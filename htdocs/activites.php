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
	Page d'activites de frankiz.
	
	$Log$
	Revision 1.1  2005/01/17 21:13:45  pico
	Ajout d'une page pour des activités plus complètes...

	
	
*/

require_once "include/wiki.inc.php";


// génération de la page
require "include/page_header.inc.php";
echo "<page id='activites' titre='Frankiz : activités'>\n";

// Etat du bôb
$DB_web->query("SELECT valeur FROM parametres WHERE nom='bob'");
list($valeurBob) = $DB_web->next_row();
// Etat de la Kes
$DB_web->query("SELECT valeur FROM parametres WHERE nom='kes'");
list($valeurKes) = $DB_web->next_row();


	
if(est_authentifie(AUTH_INTERNE) && $valeurBob == 1) echo "<annonce titre=\"Le BôB est ouvert\"/>";
if(est_authentifie(AUTH_INTERNE) && $valeurKes == 1) echo "<annonce titre=\"La Kes est ouverte\"/>";

$date_legend = array("Aujourd'hui","Demain","Après-demain","Dans 3 jours","Dans 4 jours","Dans 5 jours","Dans une semaine");

for($i= 0; $i<7;$i++){
	$DB_web->query("SELECT affiche_id,titre,url,date,exterieur FROM affiches WHERE TO_DAYS(date)=TO_DAYS(NOW() + INTERVAL $i DAY) ORDER BY date");
	if ($DB_web->num_rows()!=0){
		echo "<h3>{$date_legend[$i]}</h3>";
		while (list($id,$titre,$url,$date,$exterieur)=$DB_web->next_row()) { 
			if(!$exterieur && !est_authentifie(AUTH_INTERNE)) continue;
		?>
			<annonce date="<? echo $date ?>">
			<lien url="<?php echo $url?>"><image source="<?php echo DATA_DIR_URL.'affiches/'.$id?>" texte="Affiche" legende="<?php echo $titre?>"/></lien>
			</annonce>
		<?
		}
	}
}

echo "</page>\n";
require_once "include/page_footer.inc.php";
?>