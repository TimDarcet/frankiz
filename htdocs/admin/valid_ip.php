<?php
/*
	Cette page gère l'attribution d'adresses IP supplémentaires aux élèves.
	L'élève fait une demande grâce à la page profil/demande_ip.php, on valide
	ou refuse la demande ici.
	
	$Log$
	Revision 1.10  2004/09/15 23:20:18  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

	Revision 1.9  2004/09/15 21:42:27  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

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
		$DB_admin->query("DELETE FROM validations_ip WHERE eleve_id='{$temp[1]}'");
		
		$contenu = "Bonjour, \n\n".
					"Nous sommes désolé mais nous ne pouvons pas d'ouvrir une autre ip supplémentaire car nous ne pensons pas que tu en ai absolument besoin...\n\n".
					"Il y a certainement une autre façon de faire qui te permettra de faire ce que tu as envie de faire \n".
					"\n" .
					"Très Cordialement\n" .
					"Le BR\n"  ;
		
		$DB_trombino->query("SELECT login,nom,prenom,mail FROM eleves WHERE eleve_id='{$temp[1]}'");
		list($login,$nom,$prenom,$mail) = $DB_trombino->next_row() ;
		if (($mail=="")||($mail=="NULL")) $mail = $login."@poly.polytechnique.fr" ;
	
		mail("\"$prenom $nom\" <$mail>","[Frankiz] Ta demande a été refusée ",$contenu);

	}
	// On accepte la demande d'ip supplémentaire
	//===========================
	if ($temp[0] == "ok") {
		$temp2 = "ajout_ip_".$temp[1] ;
		$temp3 = "raison_".$temp[1] ;
		$DB_admin->query("DELETE FROM validations_ip WHERE eleve_id='{$temp[1]}'");
		$DB_admin->query("INSERT prises SET prise_id='',piece_id='',ip='{$_POST[$temp2]}',type='secondaire'");
		
		$contenu = "Bonjour, \n\n".
					"Nous t'avons ouvert l'ip suivante :\n".
					$_POST[$temp2]."\n".
					"\n" .
					"Très Cordialement\n" .
					"Le BR\n"  ;
		
		$DB_trombino->query("SELECT login,nom,prenom,mail FROM eleves WHERE eleve_id='{$temp[1]}'");
		list($login,$nom,$prenom,$mail) = $DB_trombino->next_row();
		if (($mail=="")||($mail=="NULL")) $mail = $login."@poly.polytechnique.fr" ;
	
		mail("\"$prenom $nom\" <$mail>","[Frankiz] Ta demande a été acceptée",$contenu);

	}
	
	// On vire une ip qu'on avait validé
	//===========================
	if ($temp[0] == "suppr") {
		$temp2 = str_replace("x",".",$temp[2]) ; // euh c'est pas bo je suis d'accord mais bon c'est pour que ca marche sans trop de trick
		$DB_admin->query("DELETE FROM prises WHERE type='secondaire' AND ip='$temp2'");
		
		$contenu = "Bonjour, \n\n".
					"Nous t'avons supprimé l'ip suivante :\n".
					$temp2."\n".
					"\n" .
					"Très Cordialement\n" .
					"Le BR\n"  ;
		
		$DB_trombino->query("SELECT login,nom,prenom,mail FROM eleves WHERE eleve_id='{$temp[1]}'");
		list($login,$nom,$prenom,$mail) = $DB_trombino->next_row() ;
		if (($mail=="")||($mail=="NULL")) $mail = $login."@poly.polytechnique.fr" ;
	
		mail("\"$prenom $nom\" <$mail>","[Frankiz] Suppression d'une ip",$contenu);

	}
}
?>

<commentaire>
Vous allez valider un ajout d'une ip : Pour le mement le système n'est pas fiable car on ne sais pas si l'ip qu'on lui attribut est libre... Donc faites super attention car après c'est la merde !
</commentaire>
<h2>Liste des personnes demandant</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_ip.php">
		<entete id="eleve" titre="Élève"/>
		<entete id="raison" titre="Raison"/>
		<entete id="prises" titre="Prises"/>
		<entete id="ip" titre="IP"/>
<?
		$DB_admin->query("SELECT v.raison,e.nom,e.prenom,e.piece_id,e.eleve_id FROM validations_ip as v INNER JOIN trombino.eleves as e USING(eleve_id)");
		while(list($raison,$nom,$prenom,$piece,$eleve_id) = $DB_admin->next_row()) {
?>
			<element id="<? echo $eleve_id ;?>">
				<colonne id="eleve"><? echo "$nom $prenom" ?></colonne>
				<colonne id="raison">
					<zonetext titre="" id="raison_<? echo $eleve_id ;?>" valeur="<? echo $raison ;?>"/>
				</colonne>
				<colonne id="prises">
				</colonne>
				<colonne id="ip">
					<champ titre="" id="ajout_ip_<? echo $eleve_id ;?>" valeur="129.104." /> 
					<bouton titre="Ok" id="ok_<? echo $eleve_id ;?>"/>
					<bouton titre="Vtff" id="vtff_<? echo $eleve_id ;?>"/>
				</colonne>
			</element>
<?
		}
?>
	</liste>
	
	
	
	<h2>Liste des personnes ayant eu leurs ips supplémentaires</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_ip.php">
		<entete id="eleve" titre="Élève"/>
		<entete id="prise" titre="Prise"/>
		<entete id="ip" titre="IP"/>
<?
		$DB_admin->query("SELECT e.nom,e.prenom,prises.prise_id,prises.ip FROM prises INNER JOIN trombino.eleves as e USING(piece_id) WHERE type='secondaire' ORDER BY e.nom ASC, e.prenom ASC");
		while(list($nom,$prenom,$prise,$ip) = $DB_admin->next_row()) {
?>
			<element id="<? echo str_replace(".","x",$ip) ;?>">
				<colonne id="eleve"><? echo "$nom $prenom" ?></colonne>
				<colonne id="prise"><? echo $prise ?></colonne>
				<colonne id="ip"><? echo $ip ;?><bouton titre="Dégage!" id="<? echo str_replace(".","x",$ip) ;?>"/></colonne>
			</element>
<?
		}
?>
	</liste>

</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
