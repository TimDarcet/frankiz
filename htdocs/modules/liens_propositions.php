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
	Liens permettants de contacter les webmestres et faire des demandes.
	
	$Id$

*/
if(est_authentifie(AUTH_MINIMUM)) {
?>
<module id="liens_contacts" titre="Contribuer">
		<lien id="propo_annonce" titre="Proposer une annonce" url="proposition/annonce.php" />
		<lien id="propo_activite" titre="Proposer une activité" url="proposition/affiche.php" />
		<lien id="propo_qdj" titre="Proposer une qdj" url="proposition/qdj.php" />
		<lien id="propo_sondage" titre="Proposer un sondage" url="proposition/sondage.php" />
		<lien id="propo_mailpromo" titre="Demander un mail promo" url="proposition/mail_promo.php" />
</module>
<?
}
?>
