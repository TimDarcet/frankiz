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
	Revision 1.16  2005/01/20 20:09:03  pico
	Changement de "Très BRment, l'automate"

	Revision 1.15  2004/12/16 17:26:10  schmurtz
	Ajout d'un exemple pour login.promo, ca evitera les dupond.x2002.
	
	Revision 1.14  2004/12/14 00:52:02  kikx
	Envoie les demandes de changement au nom du mec qui demande ... pour faire plaisir au gens ...
	
	Revision 1.13  2004/11/29 17:27:32  schmurtz
	Modifications esthetiques.
	Nettoyage de vielles balises qui trainaient.
	
	Revision 1.12  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.11  2004/11/16 18:32:34  schmurtz
	Petits problemes d'interpretation de <note> et <commentaire>
	
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
demande_authentification(AUTH_FORT);

// Génération du la page XML
require "../include/page_header.inc.php";
?>
<page id="profil_demandeip" titre="Frankiz : Demande d'une nouvelle adresse IP">

<?php
$DB_valid->query("SELECT 0 FROM valid_ip WHERE eleve_id='{$_SESSION['user']->uid}'");
if ($DB_valid->num_rows()>0) { ?>
	<warning>Tu as déjà fait une demande d'attribution d'une nouvelle adresse IP. Attends que le
		BR te valide la première pour en faire une seconde si cela est justifié.</warning>
		
<?php } else if(!isset($_POST['demander'])) { ?>
	<formulaire id="demandeip" titre="Demande d'une nouvelle adresse IP" action="profil/demande_ip.php">
		<note>Tu vas demander une nouvelle adresse IP. Explique nous pourquoi tu as besoin de cette
			adresse IP supplémentaire (par exemple : deux ordinateurs, tu vis en couple&#133;)</note>
		<zonetext titre="Raison" id="raison"></zonetext>
		<bouton titre="Demander" id="demander"/>
	</formulaire>
	
<?php } else {
	$DB_valid->query("INSERT valid_ip SET raison='{$_POST['raison']}', eleve_id='{$_SESSION['user']->uid}'");
	
	// Envoie du mail au webmestre pour le prévenir d'une demande d'ip
	$DB_trombino->query("SELECT nom,prenom FROM eleves WHERE eleve_id='{$_SESSION['user']->uid}'");
	list($nom,$prenom)=$DB_trombino->next_row();
	
	$tempo = explode("profil",$_SERVER['REQUEST_URI']) ;
	
	$contenu = "$prenom $nom a demandé une nouvelle adresse IP pour la raison suivante : <br>".
				$_POST['raison']."<br><br>".
				"Pour valider ou non cette demande va sur la page : <br><br>".
				"<div align='center'><a href='".BASE_URL."/admin/valid_ip.php'>".
				BASE_URL."/admin/valid_ip.php</a></div><br><br>" .
				"Cordialement,<br>" .
				"Le BR<br>";
				
	couriel(ROOT_ID,"[Frankiz] Demande d'une nouvelle adresse IP",$contenu,$_SESSION['user']->uid);
	
	// Affichage d'un message d'information
?>
	<p>Nous avons bien pris en compte ta demande d'attribution d'une nouvelle adresse IP pour la raison
		indiquée ci-dessous. Nous allons la traiter dans les plus brefs délais.</p>
	<p>Raison de la demande :</p> 
	<commentaire>
		<?= $_POST['raison'] ?>
	</commentaire>
	
<?php } ?>

</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
