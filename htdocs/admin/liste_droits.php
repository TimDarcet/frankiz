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
	Revision 1.3  2005/01/03 20:05:07  pico
	Corrections diverses

	Revision 1.2  2005/01/03 19:55:22  pico
	Modif pour pouvoir mieux administrer
	
	Revision 1.1  2005/01/03 19:52:47  pico
	Ajout page pour voir la liste des droits sur le site
	

	
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
<page id="admin_droits" titre="Frankiz : Liste des droits">
<?
foreach(liste_droits() as $droit => $description){
	echo "<h3>$droit: $description</h3>";
	$DB_trombino->query("SELECT eleves.eleve_id,eleves.nom,prenom,surnom FROM eleves LEFT JOIN frankiz2.compte_frankiz AS cpt ON eleves.eleve_id=cpt.eleve_id WHERE  cpt.perms LIKE '%{$droit},%' ORDER BY eleves.nom,prenom ASC");
	while(list($eleve_id,$nom,$prenom,$surnom) = $DB_trombino->next_row()) {
		echo "$eleve_id: $nom $prenom ($surnom)<br/>\n";
		echo "<lien url='".BASE_URL."/admin/user.php?id=$eleve_id' titre='Administrer $prenom $nom'/><br/>\n" ;
	}
}

?>

</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
