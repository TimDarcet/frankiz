<?php
// +----------------------------------------------------------------------
// | PHP Source                                                           
// +----------------------------------------------------------------------
// | Copyright (C) 2004 by Eric Gruson <eric.gruson@polytechnique.fr>
// +----------------------------------------------------------------------
// |
// | Copyright: See COPYING file that comes with this distribution
// +----------------------------------------------------------------------
//
// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')) {
	header("Location: ".BASE_URL."/admin/");
	exit;
}

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
		$DB_web->query("DELETE FROM ip_ajout WHERE eleve_id=$temp[1] AND valider=0");
		
		$contenu = "Bonjour, \n\n".
					"Nous sommes désolé mais nous ne pouvons pas d'ouvrir une autre ip supplémentaire car nous ne pensons pas que tu en ai absolument besoin...\n\n".
					"Il y a certainement une autre façon de faire qui te permettra de faire ce que tu as envie de faire \n".
					"\n" .
					"Très Cordialement\n" .
					"Le BR\n"  ;
		
		$DB_web->query("SELECT  login,nom,prenom,mail FROM eleves WHERE eleve_id=$temp[1]");
		list($login,$nom,$prenom,$mail) = $DB_web->next_row() ;
		if (($mail=="")||($mail=="NULL")) $mail = $login."@poly.polytechnique.fr" ;
	
		mail("$prenom $nom<$mail>","[Frankiz] Ta demande a été refusée ",$contenu);

	}
	// On accepte la demande d'ip supplémentaire
	//===========================
	if ($temp[0] == "ok") {
		$temp2 = "ajout_ip_".$temp[1] ;
		$temp3 = "raison_".$temp[1] ;
		$DB_web->query("UPDATE ip_ajout SET valider=1,ip_enplus='".$_POST[$temp2]."', raison='".$_POST[$temp3]."' WHERE eleve_id=$temp[1] AND valider=0");
		
		$contenu = "Bonjour, \n\n".
					"Nous t'avons ouvert l'ip suivante :\n".
					$_POST[$temp2]."\n".
					"\n" .
					"Très Cordialement\n" .
					"Le BR\n"  ;
		
		$DB_web->query("SELECT  login,nom,prenom,mail FROM eleves WHERE eleve_id=$temp[1]");
		list($login,$nom,$prenom,$mail) = $DB_web->next_row() ;
		if (($mail=="")||($mail=="NULL")) $mail = $login."@poly.polytechnique.fr" ;
	
		mail("$prenom $nom<$mail>","[Frankiz] Ta demande a été acceptée",$contenu);

	}
	
	// On vire une ip qu'on avait validé
	//===========================
	if ($temp[0] == "suppr") {
		$temp2 = str_replace("xxx",".",$temp[2]) ; // euh c'est pas bo je suis d'accord mais bon c'est pour que ca marche sans trop de trick
		$DB_web->query("DELETE FROM ip_ajout WHERE eleve_id=$temp[1] AND valider=1 AND ip_enplus='$temp2'");
		
		$contenu = "Bonjour, \n\n".
					"Nous t'avons supprimé l'ip suivante :\n".
					$temp2."\n".
					"\n" .
					"Très Cordialement\n" .
					"Le BR\n"  ;
		
		$DB_web->query("SELECT  login,nom,prenom,mail FROM eleves WHERE eleve_id=$temp[1]");
		list($login,$nom,$prenom,$mail) = $DB_web->next_row() ;
		if (($mail=="")||($mail=="NULL")) $mail = $login."@poly.polytechnique.fr" ;
	
		mail("$prenom $nom<$mail>","[Frankiz] Suppression d'une ip",$contenu);

	}
}
?>

<commentaire>
Vous allez valider un ajout d'une ip : Pour le mement le système n'est pas fiable car on ne sais pas si l'ip qu'on lui attribut est libre... Donc faites super attention car après c'est la merde !
</commentaire>
<h2>Liste des personnes demandant</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_ip.php">
		<entete id="login" titre="Login"/>
		<entete id="raison" titre="Raison"/>
		<entete id="ip" titre="Ip"/>
<?
		$DB_web->query("SELECT  eleves.login,ip_ajout.raison,eleves.eleve_id FROM ip_ajout INNER JOIN eleves USING(eleve_id) WHERE ip_ajout.valider=0");
		while(list($login,$raison,$eleve_id) = $DB_web->next_row()) {
?>
			<element id="<? echo $eleve_id ;?>">
				<colonne id="login"><? echo $login ;?></colonne>
				<colonne id="raison">
					<zonetext titre="" id="raison_<? echo $eleve_id ;?>" valeur="<? echo $raison ;?>"/>
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
		<entete id="login" titre="Login"/>
		<entete id="raison" titre="Raison"/>
		<entete id="ip" titre="Ip"/>
<?
		$DB_web->query("SELECT  eleves.login,ip_ajout.raison,eleves.eleve_id,ip_ajout.ip_enplus FROM ip_ajout INNER JOIN eleves USING(eleve_id) WHERE ip_ajout.valider=1 ORDER BY eleves.login ASC");
		while(list($login,$raison,$eleve_id,$ip) = $DB_web->next_row()) {
?>
			<element id="<? echo $eleve_id ;?>">
				<colonne id="login"><? echo $login ;?></colonne>
				<colonne id="raison"><? echo $raison ;?></colonne>
				<colonne id="ip"><? echo $ip ;?><bouton titre="Dégage!" id="suppr_<? echo $eleve_id ;?>_<? echo str_replace(".","xxx",$ip) ;?>"/></colonne>
			</element>
<?
		}
?>
	</liste>

</page>

<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
