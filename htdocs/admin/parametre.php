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
	Cette page permet de modifier les param�tres globaux mysql du site
	genre : 
	# la derni�re promo qui est sur le site
	# la derni�re promo qui est dans le trombi (qui normalment devrait �tre mis a jour automatiquement)
	
	$Log$
	Revision 1.4  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site

	Revision 1.3  2004/09/16 15:30:09  schmurtz
	Ajout de la variable cvs "Log", suppression de return inutiles
	
*/

require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

	
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys,2) ;
	// On traite les modifications dites STANDARD
	if ($temp[0]=='modif') {
		$tempo = "id_".$temp[1] ;
		$DB_web->query("UPDATE parametres SET valeur='".$_POST[$tempo]."' WHERE nom='".$temp[1]."'");
	}
	// On taite maintenant les modifications non standard
	if ($keys == "update_lastpromo_ontrombino") {
		$DB_trombino->query("SELECT MAX(promo) FROM eleves");
		list($max_promo) = $DB_trombino->next_row() ;
		$DB_web->query("UPDATE parametres SET valeur='$max_promo' WHERE nom='lastpromo_ontrombino'");
	}
}


// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="admin_parametre" titre="Frankiz : Modifier les param�tres globaux">

<commentaire>
<p>Cette page est une page pour pouvoir modifier directement les param�tres du sites : </p>
<p> Si vous devez cr�er des varibles dans la table essayer qu'elles soient assez explicite :)</p>
</commentaire>
<h2>Param�tres du site</h2>
	<liste id="liste" selectionnable="non" action="admin/parametre.php">
		<entete id="nom_var" titre="Nom de la varible"/>
		<entete id="valeur" titre="Valeur"/>
<?
		$DB_web->query("SELECT nom,valeur FROM parametres ORDER by nom");
		while(list($nom,$valeur) = $DB_web->next_row()) {
?>
			<element id="<? echo $nom ;?>">
				<colonne id="eleve"><? echo "$nom" ?></colonne>
				<colonne id="valeur">
<?
				// Cas Particuliers trait� � la main
				if ($nom=="lastpromo_ontrombino") {
					echo $valeur." &nbsp; " ;
					echo "<bouton titre='Update' id='update_lastpromo_ontrombino'/>" ;
				} else {
				// fin des cas particuliers 
?>
					<champ titre="" id="id_<? echo $nom ;?>" valeur="<? echo $valeur ;?>"/>
					<bouton titre='Ok' id='modif_<? echo $nom ;?>'/>
<?
				}
?>
				</colonne>
			</element>
<?
		}
?>
	</liste>
	
</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
