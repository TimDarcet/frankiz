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
	
	$Id$
	
*/


require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin'))
	acces_interdit();


// Génération de la page
//===============
require_once BASE_FRANKIZ."include/page_header.inc.php";

?>
<page id="admin_droits" titre="Frankiz : Gestion des DNS">
	<h1>Informations sur la base</h1>
	<liste id='info_1' titre='Alias DNS' selectionnable='non'>
	<entete id='alias' titre='Alias'/>
	<entete id='cible' titre='Cible'/>
<?php
	$DB_xnet->query("SELECT name, rdata from DNS where rdtype = 'Cname' order by rdata");
	while(list($alias,$cible) = $DB_xnet->next_row()) {
		echo "<element id='alias'>
			<colonne id='alias'>$alias</colonne>
			<colonne id='cible'>$cible</colonne>
		      </element>";
	}
?>
	</liste>

	<h1>Surveillance de la base</h1>
	<liste id='liste_1' titre='Un pseudo avec 2 IP dans la base client' selectionnable='non'>
	<entete id='user' titre='Nom'/>
	<entete id='ip1' titre='1ere IP'/>
	<entete id='ip2' titre='2eme IP'/>
<?php
	$DB_xnet->query("SELECT p1.username,  p1.lastip,  p2.lastip FROM clients AS p1,  clients AS p2 WHERE p1.username = p2.username AND p1.lastip > p2.lastip");
	while(list($user,$ip1,$ip2) = $DB_xnet->next_row()){
		echo "<element id='$user'>
				<colonne id='user'>$user</colonne>
				<colonne id='ip1'>$ip1</colonne>
				<colonne id='ip2'>$ip2</colonne>
			</element>";
	}
?>
	</liste>
	
	<liste id='liste_2' titre='Une IP avec 2 pseudos dans la base client' selectionnable='non'>
	<entete id='user' titre='IP'/>
	<entete id='ip1' titre='Nom Utilisé'/>
	<entete id='ip2' titre='Nom oublié'/>
<?php
	$DB_xnet->query("SELECT p1.lastip, p1.username, p2.username FROM clients as p1 inner join clients as p2 ON p1.lastip=p2.lastip AND p1.timestamp>p2.timestamp, DNS as d1 inner join DNS as d2 on d2.rdata = d1.rdata where d1.name = concat(p1.username, '.eleves.polytechnique.fr') and d1.rdata = p1.lastip and d2.name = concat(p2.username, '.eleves.polytechnique.fr') ORDER BY  p1.lastip");
	while(list($user,$ip1,$ip2) = $DB_xnet->next_row()){
		echo "<element id='$user'>
				<colonne id='user'>$user</colonne>
				<colonne id='ip1'>$ip1</colonne>
				<colonne id='ip2'>$ip2</colonne>
			</element>";
	}
?>
	</liste>
	
	<liste id='liste_3' titre='Noms DNS qui correspondent à 2 IPs' selectionnable='non'>
	<entete id='user' titre='Nom'/>
	<entete id='ip1' titre='Bonne IP'/>
	<entete id='ip2' titre='Mauvaise IP'/>
<?php
	$DB_xnet->query("SELECT p2.username,p2.lastip,p1.rdata FROM DNS AS p1, clients AS p2 inner join DNS as p3 on p3.name = concat(p2.username, '.eleves.polytechnique.fr') WHERE p1.name = p3.name  AND p1.rdata != p2.lastip and p1.rdtype = 'A'");
	while(list($user,$ip1,$ip2) = $DB_xnet->next_row()){
			echo "<element id='$user'>
				<colonne id='user'>$user</colonne>
				<colonne id='ip1'>$ip1</colonne>
				<colonne id='ip2'>$ip2</colonne>
			</element>";
	}
?>
	</liste>
	
	<liste id='liste_4' titre='Plusieurs noms associés à une même IP' selectionnable='non'>
	<entete id='user' titre='IP'/>
	<entete id='ip1' titre='Nom correct'/>
	<entete id='ip2' titre='Entrée erronnée'/>
<?php
	$DB_xnet->query("SELECT p1.rdata,  p2.username,  p1.name FROM DNS AS p1,  clients AS p2 inner join DNS as p3 on p3.name = concat(p2.username, '.eleves.polytechnique.fr') and p3.rdtype = 'A' WHERE  p1.rdata = p2.lastip  AND p1.name != p3.name AND p1.rdtype = 'A' ORDER BY p1.rdata");
	while(list($user,$ip1,$ip2) = $DB_xnet->next_row()){
		echo "<element id='$user'>
				<colonne id='user'>$user</colonne>
				<colonne id='ip1'>$ip1</colonne>
				<colonne id='ip2'>$ip2</colonne>
			</element>";
	}
?>
	</liste>
	
	<liste id='liste_5' titre='Analyse table reverse_DNS' selectionnable='non'>
	<entete id='user' titre='Nom'/>
	<entete id='ip1' titre='Bonne IP'/>
	<entete id='ip2' titre='Mauvaise IP'/>
<?php
	$DB_xnet->query("SELECT p1.name, p1.rdata, p2.rdata FROM reverse_DNS as p1 inner join clients as p3 on p1.rdata = concat(p3.username, '.eleves.polytechnique.fr'),  reverse_DNS as p2 WHERE p1.name = p2.name AND p1.rdata != p2.rdata  ORDER BY p1.rdata");
	while(list($user,$ip1,$ip2) = $DB_xnet->next_row()){
			echo "<element id='$user'>
				<colonne id='user'>$user</colonne>
				<colonne id='ip1'>$ip1</colonne>
				<colonne id='ip2'>$ip2</colonne>
			</element>";
	}
?>
	</liste>

</page>

<?php
require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
