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
	Script de création de la partie activités contenant des images type "affiche".
	
	$Id$

*/


// Etat du bôb
$valeur = getEtatBob();

$DB_web->query("SELECT affiche_id,titre,url,date,exterieur FROM affiches WHERE TO_DAYS(date)=TO_DAYS(NOW()) ORDER BY date");
	
if ($DB_web->num_rows()!=0 || $valeur){
	echo "<module id=\"activites\" titre=\"Activités\">\n";
	if(est_authentifie(AUTH_INTERNE) && $valeur) echo "<annonce titre=\"Le BôB est ouvert\"/>";
	while (list($id,$titre,$url,$date,$exterieur)=$DB_web->next_row()) { 
		if(!$exterieur && !est_authentifie(AUTH_INTERNE)) continue;
	?>
		<annonce date="<?php echo $date ?>">
		<lien url="<?php echo ($url!="")?$url:"activites.php";?>"><image source="<?php echo DATA_DIR_URL.'affiches/'.$id?>" texte="Affiche" legende="<?php echo $titre?>"/></lien>
		</annonce>
<?php }
	echo "</module>\n";
}
?>
