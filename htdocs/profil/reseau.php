<?php
/*
	$Id$
	
	Page permettant de modifier ses informations relatives au réseau interne de l'x : le nom de
	ses machines, son compte xnet.
	
	TODO faire la page
*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_FORT);

$DB_admin->query("SELECT ip.piece_id,ip.prise_id,ip.ip,ip.type FROM prises as ip "
				."INNER JOIN trombino.eleves as e USING(piece_id) WHERE e.eleve_id='{$_SESSION['user']->uid}' "
				."ORDER BY ip.type ASC");
list($kzert,$prise,$ip,$type) = $DB_admin->next_row();

// Génération du la page XML
require "../include/page_header.inc.php";
?>
<page id="profil_reseau" titre="Frankiz : modification du profil réseau">
	<h2>Infos divers</h2>
	<p>Normalement tu as l'ip <?=$ip?> (car ta prise est la <?=$prise?>)</p>
	<p>
	<note>Si tu souhaite une nouvelle ip clique <lien titre='ici' url='profil/demande_ip.php'/>
<?
		$bool_ip = $ip!=$_SERVER['REMOTE_ADDR'];
		
		if($DB_admin->num_rows()>1) {
			echo "<p>&nbsp;</p><p>Tu as en plus fait rajouter ces ips à tes ip autorisées :</p>" ;
			
			while(list($kzert,$prise,$ip,$type) = $DB_admin->next_row()) { 
				echo "<p>$ip</p>" ;
				$bool_ip = $bool_ip&&($ip!=$_SERVER['REMOTE_ADDR']) ;
			}
		}
?>
	</note>
<? 

	if(substr($_SERVER['REMOTE_ADDR'],0,7)=="129.104" && $bool_ip) {
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
