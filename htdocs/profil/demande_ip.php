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
	Page permettant de faire une demande d'adresse IP supplémentaire pour mettre
	une seconde machine dans son casert.
	
	$Log$
	Revision 1.10  2004/10/29 15:41:48  kikx
	Passage des mail en HTML pour les ip

	Revision 1.9  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.8  2004/09/20 08:29:24  kikx
	Rajout d'une page pour envoyer des mail d'amour a ses webmestres adorés
	
	Revision 1.7  2004/09/17 11:34:10  kikx
	Bla
	
	Revision 1.6  2004/09/15 23:20:07  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.5  2004/09/15 21:42:21  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_MINIMUM);

$eleve_id=$_SESSION['user']->uid;

// Génération du la page XML
require "../include/page_header.inc.php";
?>
<page id="profil_reseau" titre="Frankiz : Demande d'une nouvelle ip">
<?
if (!isset($_POST['demander'])) {
?>

	<formulaire id="demande" titre="Demande d'une nouvelle IP" action="profil/demande_ip.php">
		<commentaire>Tu vas demander une nouvelle ip : Explique nous pourquoi tu en as besoin de cette ip supplémentaire (par exemple : 2 ordinateurs, tu vis en couple ...)</commentaire>
		<zonetext titre="Raison" id="raison" valeur="" />
		<bouton titre="Demander" id="demander"/>
	</formulaire>
<?
} else {
	$DB_valid->query("SELECT 0 FROM valid_ip WHERE eleve_id='{$_SESSION['user']->uid}'");
	if ($DB_valid->num_rows()>0){
?>

		<warning>Tu as déjà fait une demande</warning>
		<p>Tu ne peux pas faire plusieurs demandes à la fois ...</p>
		<p>Attends que les BRmen te valident la première pour en faire une seconde si cela est justifié</p>
	
<?
	} else {
		$DB_valid->query("INSERT valid_ip SET raison='{$_POST['raison']}', eleve_id='{$_SESSION['user']->uid}'");
		
		// Envoie du mail au webmestre pour le prévenir d'une demande d'ip
		$DB_trombino->query("SELECT nom,prenom FROM eleves WHERE eleve_id='{$_SESSION['user']->uid}'");
		list($nom,$prenom)=$DB_trombino->next_row();
		
		$tempo = explode("profil",$_SERVER['REQUEST_URI']) ;
		
		$contenu = "$prenom $nom a demandé une nouvelle ip pour la raison suivante : <br>".
					stripslashes($_POST['raison'])."<br><br>".
					"Pour valider ou non cette demande va sur la page suivante : <br><br>".
					"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_ip.php'>".
					"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_ip.php</a></div><br><br>" .
					"Très BR-ement<br>" .
					"L'automate :)<br>"  ;
					
		couriel(ROOT_ID,"[Frankiz] Demande d'une nouvelle ip",$contenu);
	
?>

		<p>Nous avons bien pris en compte ta demande pour la raison suivante ci dessous. Nous allons la traiter dans les plus brefs delais :)</p>
		<p>&nbsp;</p>
		<p>Raison de la demande :</p> 
		<commentaire>
			<? echo stripslashes($_POST['raison']) ;?>
		</commentaire>
	
<?
	}
}
?>
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
