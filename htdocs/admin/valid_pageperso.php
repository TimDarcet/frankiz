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
	Cette page gère l'attribution d'adresses IP supplémentaires aux élèves.
	L'élève fait une demande grâce à la page profil/demande_ip.php, on valide
	ou refuse la demande ici.
	
	$Log$
	Revision 1.3  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire

	Revision 1.2  2004/11/27 15:02:17  pico
	Droit xshare et faq + redirection vers /gestion et non /admin en cas de pbs de droits
	
	Revision 1.1  2004/11/24 12:51:58  kikx
	Oubli de ma part
	

	
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/gestion/");

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_valid_ip" titre="Frankiz : Ajouter une ip à un utilisateur">

<?
// On regarde quel cas c'est ...
// On envoie chié le mec pour son changement d'ip et on le supprime de la base
// On accepte le changement et on l'inbscrit dans la base

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;
	
	// On refuse la demande d'ip supplémentaire
	//==========================
	if ($temp[0] == "vtff") {
		$DB_valid->query("DELETE FROM valid_pageperso WHERE eleve_id='{$temp[1]}'");
		
		$bla = "refus_".$temp[1] ;
		$contenu = "<b>Bonjour,</b> <br><br>".
			"Nous sommes désolé mais le BR n'a pas approuvé ta demande pour la raison suivante <br>".
			$_POST[$bla]."<br>".
			"<br>" .
			"Très Cordialement<br>" .
			"Le BR<br>"  ;
	
		couriel($temp[1],"[Frankiz] La demande pour ton site a été refusée ",$contenu);
		echo "<warning>Envoie d'un mail <br/>Le prévient que sa demande n'est pas acceptée</warning>" ;
	}
	// On accepte la demande d'ip supplémentaire
	//===========================
	if ($temp[0] == "ok") {
		$DB_valid->query("DELETE FROM valid_pageperso WHERE eleve_id='{$temp[1]}'");
		
		$DB_web->query("INSERT INTO sites_eleves SET eleve_id='{$temp[1]}'");
			
		$contenu = "<b>Bonjour,</b> <br><br>".
				"Ton site perso apparaitra desormais sur le site élève<br>".
				"<br>" .
				"Très Cordialement<br>" .
				"Le BR<br>"  ;
		
		couriel($temp[1],"[Frankiz] La demande pour ton site perso a été acceptée",$contenu);
			echo "<commentaire>Envoie d'un mail<br/>Le prévient que sa demande à été acceptée</commentaire>" ;

	}
}
?>

<h2>Liste des personnes demandant une entrée sur la page des sites élèves</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_pageperso.php">
		<entete id="eleve" titre="Élève"/>
		<entete id="url" titre="Url"/>
<?
		$DB_valid->query("SELECT e.eleve_id,e.nom,e.prenom,e.promo,e.login FROM valid_pageperso as v INNER JOIN trombino.eleves as e USING(eleve_id)");
		while(list($id,$nom,$prenom,$promo,$login) = $DB_valid->next_row()) {
?>
			<element id="<? echo $id ;?>">
				<colonne id="eleve"><? echo "$nom $prenom ($promo)" ?></colonne>
				<colonne id="url">
					<lien id="<?=$id?>" titre="Site" url="<? echo URL_PAGEPERSO."$login-$promo"?>"/>
					<zonetext titre="Raison du Refus si refus" id="refus_<? echo $id ;?>" valeur=""/>
					<bouton titre="Ok" id="ok_<? echo $id ;?>" />
					<bouton titre="Vtff" id="vtff_<? echo $id ;?>" onClick="return window.confirm('Voulez vous vraiment ne pas valider cette page ?')"/>

				</colonne>
			</element>
<?
		}
?>
	</liste>

</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
