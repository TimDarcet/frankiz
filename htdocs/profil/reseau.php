<?php
/*
	$Id$
	
	Page permettant de modifier ses informations relatives au réseau interne de l'x : le nom de
	ses machines, son compte xnet.
	
	TODO faire la page
*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_FORT);

$DB_web->query("SELECT ip_chambre_theory.piece_id,ip_chambre_theory.prise_id,ip_chambre_theory.ip_theorique FROM ip_chambre_theory INNER JOIN eleves USING(piece_id) WHERE eleve_id='".$_SESSION['user']->uid."'");
list($kzert,$prise,$ip) = $DB_web->next_row();

// Génération du la page XML
require "../include/page_header.inc.php";
?>
<page id="profil_reseau" titre="Frankiz : modification du profil réseau">
	<h2>Infos divers</h2>
	<p>
	Normalement tu as l'ip 
<?
	echo $ip." (car ta prise est la ".$prise.")" ;
?>
	</p>
	<p>

	<note>Si tu souhaite une nouvelle ip clique <lien titre='ici' url='profil/demande_ip.php'/>
<?
		$DB_web->query("SELECT ip_enplus FROM ip_ajout WHERE eleve_id='".$_SESSION['user']->uid."'");
		if ($DB_web->num_rows()>0) {
			echo "<p>&nbsp;</p><p>Tu as en plus fait rajouter ces ips à tes ip autorisées :</p>" ;
			
			$bool_ip = true ;
			while(list($ip_enplus) = $DB_web->next_row()) { 
				echo "<p>$ip_enplus</p>" ;
				$bool_ip = $bool_ip&&($ip_enplus!=$_SERVER['REMOTE_ADDR']) ;
			}
		}
?>
	</note>
<? 

	if (($ip!=$_SERVER['REMOTE_ADDR'])&&(substr($_SERVER['REMOTE_ADDR'],0,7)=="129.104")&&$bool_ip) {
		echo "<warning>ATTENTION : " ;
		echo "Ton ip est actuellement ".$_SERVER['REMOTE_ADDR'] ; 
		echo " et ceci ne devrait pas être le cas si tu te connecte de ton kzert</warning>";
	}
?>
	</p>
	<h2>Nom de tes machines</h2>
	<p>En construction
</p>
	<h2>Compte Xnet (mot de passe)</h2>
	<p>En construction
</p>
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
