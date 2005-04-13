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
	
	$Log$
	Revision 1.3  2005/04/13 17:09:58  pico
	Passage de tous les fichiers en utf8.

	Revision 1.2  2005/01/24 09:13:05  pico
	Stats xnet
	
	Revision 1.1  2005/01/23 16:30:10  pico
	Ajout d'une page pour surveiller les entrées dns
	
	
*/
/*
<Fruneau> 1 - pour la suppression faut faire un match qui correspond à la première et la dernière colonne
<Fruneau> 2 - faut aussi incrémenter le sérial (dans la table sérial)
<Fruneau> le 1 pour être sur de bien supprimer la bonne entrée
<Fruneau> le 2 pour la réplication de la base
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	acces_interdit();


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_droits" titre="Frankiz : Gestion des DNS">
<?
	echo "<liste id='liste_1' titre='Un pseudo avec 2 IP dans la base client' selectionnable='non'>";
	echo "<entete id='user' titre='Nom'/>";
	echo "<entete id='ip1' titre='1ere IP'/>";
	echo "<entete id='ip2' titre='2eme IP'/>";
	$DB_xnet->query("SELECT p1.username,  p1.lastip,  p2.lastip FROM clients AS p1,  clients AS p2 WHERE p1.username = p2.username AND p1.lastip > p2.lastip");
	while(list($user,$ip1,$ip2) = $DB_xnet->next_row()){
		echo "<element id='$user'>
				<colonne id='user'>$user</colonne>
				<colonne id='ip1'>$ip1</colonne>
				<colonne id='ip2'>$ip2</colonne>
			</element>";
	}
	echo "</liste>";
	
	echo "<liste id='liste_2' titre='Une IP avec 2 pseudos dans la base client' selectionnable='non'>";
	echo "<entete id='user' titre='IP'/>";
	echo "<entete id='ip1' titre='Nom Utilisé'/>";
	echo "<entete id='ip2' titre='Nom oublié'/>";
	$DB_xnet->query("SELECT p1.lastip, p1.username, p2.username FROM clients as p1, clients as p2 WHERE p1.lastip=p2.lastip  AND p1.timestamp>p2.timestamp ORDER BY  p1.lastip");
	while(list($user,$ip1,$ip2) = $DB_xnet->next_row()){
		echo "<element id='$user'>
				<colonne id='user'>$user</colonne>
				<colonne id='ip1'>$ip1</colonne>
				<colonne id='ip2'>$ip2</colonne>
			</element>";
	}
	echo "</liste>";
	
	echo "<liste id='liste_3' titre='Noms DNS qui correspondent à 2 IPs' selectionnable='non'>";
	echo "<entete id='user' titre='Nom'/>";
	echo "<entete id='ip1' titre='Bonne IP'/>";
	echo "<entete id='ip2' titre='Mauvaise IP'/>";
	$DB_xnet->query("SELECT p2.username,p2.lastip,p1.rdata FROM DNS AS p1, clients AS p2 WHERE p1.name = concat(p2.username,'.eleves.polytechnique.fr')  AND p1.rdata !=p2.lastip");
	while(list($user,$ip1,$ip2) = $DB_xnet->next_row()){
			echo "<element id='$user'>
				<colonne id='user'>$user</colonne>
				<colonne id='ip1'>$ip1</colonne>
				<colonne id='ip2'>$ip2</colonne>
			</element>";
	}
	echo "</liste>";
	
	echo "<liste id='liste_4' titre='Plusieurs noms associés à une même IP' selectionnable='non'>";
	echo "<entete id='user' titre='IP'/>";
	echo "<entete id='ip1' titre='Nom correct'/>";
	echo "<entete id='ip2' titre='Entrée erronnée'/>";
	$DB_xnet->query("SELECT p1.rdata,  p2.username,  p1.name FROM DNS AS p1,  clients AS p2 WHERE p1.rdata = p2.lastip  AND p1.name !=CONCAT(p2.username, '.eleves.polytechnique.fr')  AND p1.rdtype = 'A' ORDER BY p1.rdata");
	while(list($user,$ip1,$ip2) = $DB_xnet->next_row()){
		echo "<element id='$user'>
				<colonne id='user'>$user</colonne>
				<colonne id='ip1'>$ip1</colonne>
				<colonne id='ip2'>$ip2</colonne>
			</element>";
	}
	echo "</liste>";
	
	echo "<liste id='liste_5' titre='Analyse table reverse_DNS' selectionnable='non'>";
	echo "<entete id='user' titre='Nom'/>";
	echo "<entete id='ip1' titre='Bonne IP'/>";
	echo "<entete id='ip2' titre='Mauvaise IP'/>";
	$DB_xnet->query("SELECT p1.name, p1.rdata, p2.rdata FROM reverse_DNS as p1,  reverse_DNS as p2,  clients as p3 WHERE p1.name = p2.name AND p1.rdata != p2.rdata AND p1.rdata =CONCAT(p3.username, '.eleves.polytechnique.fr') ORDER BY p1.rdata");
	while(list($user,$ip1,$ip2) = $DB_xnet->next_row()){
			echo "<element id='$user'>
				<colonne id='user'>$user</colonne>
				<colonne id='ip1'>$ip1</colonne>
				<colonne id='ip2'>$ip2</colonne>
			</element>";
	}
	echo "</liste>";
?>

</page>

<?
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
