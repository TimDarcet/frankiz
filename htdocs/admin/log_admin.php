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
	Page principale d'administration : affiche la liste des pages d'administration auxquelles
	l'utilisateur courant à accès.

	$Id$
	
*/
	
// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	acces_interdit();

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin" titre="Frankiz : administration">
<h2>Log de la partie d'administration</h2>
<commentaire>Voici les 100 dernières actions des administrateurs</commentaire>
<?
	$DB_admin->query("SELECT l.date,l.log,e.nom, e.prenom, e.promo FROM log_admin as l LEFT JOIN trombino.eleves as e ON e.eleve_id=l.id_admin ORDER BY date DESC LIMIT 100") ;
	while (list($date,$log,$nom,$prenom,$promo) = $DB_admin->next_row()) {
		$y = substr($date,0,4) ;
		$m = substr($date,4,2) ;
		$j = substr($date,6,2) ;
		$h = substr($date,8,2) ;
		$min = substr($date,10,2) ;
		$s = substr($date,12,2) ;
		echo "<p>$j/$m/$y $h:$min:$s : $prenom $nom ($promo) a $log</p>" ;
	}
?>

</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
