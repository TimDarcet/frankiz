<?php
/*
	Cette page gère l'attribution d'adresses IP supplémentaires aux élèves.
	L'élève fait une demande grâce à la page profil/demande_ip.php, on valide
	ou refuse la demande ici.
	
	$Log$
	Revision 1.13  2004/09/17 12:45:22  kikx
	Permet de voi quel sont les ips que la personne a déjà avant de valider ... en particulier ca permet de pas se planter de sous réseau !!!!!!!!!!!!!

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
		$DB_valid->query("DELETE FROM valid_ip WHERE eleve_id='{$temp[1]}'");
		
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
		echo "<warning><p>Envoie d'un mail à $mail</p><p>Le prévient que sa demande n'est pas acceptée</p></warning>" ;
	}
	// On accepte la demande d'ip supplémentaire
	//===========================
	if ($temp[0] == "ok") {
		$temp2 = "ajout_ip_".$temp[1] ;
		$temp3 = "raison_".$temp[1] ;
		$DB_trombino->query("SELECT piece_id FROM eleves WHERE eleve_id='{$temp[1]}'") ;
		list($kzert) = $DB_trombino->next_row();
		
		$DB_admin->query("SELECT 0 FROM prises WHERE ip='{$_POST[$temp2]}'");
		
		// S'il n'y a aucune entrée avec cette ip dans la base
		if ($DB_admin->num_rows()==0){
			$DB_valid->query("DELETE FROM valid_ip WHERE eleve_id='{$temp[1]}'");
			$DB_admin->query("INSERT prises SET prise_id='',piece_id='$kzert',ip='{$_POST[$temp2]}',type='secondaire'");
			
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
			echo "<warning><p>Envoie d'un mail à $mail</p><p>Le prévient que sa demande à été accepté (Nlle ip =".$_POST[$temp2].") </p></warning>" ;
		// S'il y  a deja une entrée comme celle demandé dans la base !
		} else {
			echo "<warning><p>IMPOSSIBLE DE METTRE CETTE IP</p><p>Il y a déjà une autre personne la possédant</p></warning>" ;		
		}

	}
	
	// On vire une ip qu'on avait validé
	//===========================
	if ($temp[0] == "suppr") {
		$temp2 = str_replace("x",".",$temp[1]) ; // euh c'est pas bo je suis d'accord mais bon c'est pour que ca marche sans trop de trick
		$DB_admin->query("DELETE FROM prises WHERE type='secondaire' AND ip='$temp2' AND prise_id=''");
		
		$contenu = "Bonjour, \n\n".
					"Nous t'avons supprimé l'ip suivante :\n".
					$temp2."\n".
					"\n" .
					"Très Cordialement\n" .
					"Le BR\n"  ;
		
		$DB_trombino->query("SELECT login,nom,prenom,mail FROM eleves WHERE eleve_id='{$temp[2]}'");
		list($login,$nom,$prenom,$mail) = $DB_trombino->next_row() ;
		if (($mail=="")||($mail=="NULL")) $mail = $login."@poly.polytechnique.fr" ;
	
		mail("\"$prenom $nom\" <$mail>","[Frankiz] Suppression d'une ip",$contenu);
		echo "<warning><p>Envoie d'un mail à $mail</p><p>Le previent que son ip $temp2 vient d'être supprimé</p></warning>" ;			

	}
}
?>

<commentaire>
Vous allez valider un ajout d'une ip : Pour le moment le système n'est pas fiable car on ne sais pas si l'ip qu'on lui attribut est libre... Donc faites super attention car après c'est la merde !
</commentaire>
<h2>Liste des personnes demandant</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_ip.php">
		<entete id="eleve" titre="Élève"/>
		<entete id="raison" titre="Raison"/>
		<entete id="ip" titre="IP"/>
<?
		$DB_valid->query("SELECT v.raison,e.nom,e.prenom,e.piece_id,e.eleve_id FROM valid_ip as v INNER JOIN trombino.eleves as e USING(eleve_id)");
		while(list($raison,$nom,$prenom,$piece,$eleve_id) = $DB_valid->next_row()) {
?>
			<element id="<? echo $eleve_id ;?>">
				<colonne id="eleve"><? echo "$nom $prenom" ?></colonne>
				<colonne id="raison">
					<zonetext titre="" id="raison_<? echo $eleve_id ;?>" valeur="<? echo $raison ;?>"/>
				</colonne>
				<colonne id="ip">
<?
					$DB_admin->query("SELECT ip FROM prises WHERE piece_id='$piece'") ;
					while(list($ip)=$DB_admin->next_row()) {
						echo "<p>" ;
							echo $ip ;
						echo "</p>" ;
					}
?>					
					<p>
						<champ titre="" id="ajout_ip_<? echo $eleve_id ;?>" valeur="129.104." /> 
						<bouton titre="Ok" id="ok_<? echo $eleve_id ;?>" />
						<bouton titre="Vtff" id="vtff_<? echo $eleve_id ;?>" onClick="return window.confirm('Voulez vous vraiment ne pas valider cette ip ?')"/>
					</p>
				</colonne>
			</element>
<?
		}
?>
	</liste>
	
	
	
	<h2>Liste des personnes ayant eu leurs ips supplémentaires</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_ip.php">
		<entete id="eleve" titre="Élève"/>
		<entete id="ip" titre="IP"/>
<?
		$DB_admin->query("SELECT e.eleve_id,e.nom,e.prenom,prises.ip FROM prises INNER JOIN trombino.eleves as e USING(piece_id) WHERE type='secondaire' ORDER BY e.nom ASC, e.prenom ASC");
		while(list($id,$nom,$prenom,$ip) = $DB_admin->next_row()) {
?>
			<element id="<? echo str_replace(".","x",$ip) ;?>">
				<colonne id="eleve"><? echo "$nom $prenom" ?></colonne>
				<colonne id="ip"><? echo $ip ;?><bouton titre="Dégage!" id="suppr_<? echo str_replace(".","x",$ip) ;?>_<? echo $id?>" onClick="return window.confirm('Voulez vous vraiment supprimez cette ip ?')"/></colonne>
			</element>
<?
		}
?>
	</liste>

</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
