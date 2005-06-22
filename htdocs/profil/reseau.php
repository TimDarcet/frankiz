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
	Page permettant de modifier ses informations relatives au réseau interne de l'x : le nom de
	ses machines, son compte xnet.
	
	$Id$

*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_FORT);

$DB_admin->query("SELECT ip.piece_id,ip.prise_id,ip.ip,ip.type FROM prises as ip "
				."LEFT JOIN trombino.eleves as e USING(piece_id) WHERE e.eleve_id='{$_SESSION['user']->uid}' "
				."ORDER BY ip.type ASC");
$id_ip = 0;
list($kzert,$prise,$ip{$id_ip},$type) = $DB_admin->next_row();

// Génération du la page XML
require "../include/page_header.inc.php";

?>
<page id="profil_reseau" titre="Frankiz : modification du profil réseau">

<?php
$DB_trombino->query("SELECT e.piece_id FROM eleves as e WHERE e.eleve_id='{$_SESSION['user']->uid}' AND NOT (e.piece_id IS NULL)");
if($DB_trombino->num_rows()!=0){

if(isset($_POST['changer_xnet'])) {
	// Modification du mot de passe
	if($_POST['passwd']=='12345678' && $_POST['passwd2']=='87654321' || empty($_POST['passwd']) && empty($_POST['passwd2'])) {
	// ne rien faire, on garde l'ancien mot de passe
	}
	if($_POST['passwd'] != $_POST['passwd2']) {
		ajoute_erreur(ERR_MDP_DIFFERENTS);
	}
	if(strlen($_POST['passwd']) < 6) {
		ajoute_erreur(ERR_MDP_TROP_PETIT);
	}
	if(aucune_erreur()) {
		$pass = md5($_POST['passwd']."Vive le BR");
		$DB_xnet->query("UPDATE clients SET password='$pass' WHERE lastip='{$_POST['ip_xnet']}'");
		
		$message="<commentaire>Le mot de passe xnet pour l'adresse {$_POST['ip_xnet']} vient d'être changé.</commentaire>";
	}
}

		if(!empty($message))
			echo "$message\n";
		if(a_erreur(ERR_MDP_DIFFERENTS))
			echo "<warning>Les valeurs des deux champs de mot de passe n'étaient pas identiques.</warning>\n";
		if(a_erreur(ERR_MDP_TROP_PETIT))
			echo "<warning>Il faut mettre un mot de passe plus long (au moins 6 caractères).</warning>\n";
?>
	<h2 id="reseau_ip">Infos divers</h2>
	<p>Normalement tu as l'ip <?=$ip{$id_ip}?> (car ta prise est la <?=$prise?>)</p>
	<p>
	<note>Si tu souhaite une nouvelle ip clique <lien titre='ici' url='profil/demande_ip.php'/>
<?
		$bool_ip = $ip{$id_ip}!=$_SERVER['REMOTE_ADDR'];
		$id_ip++;
		if($DB_admin->num_rows()>1) {
			echo "<br/>Tu as en plus fait rajouter ces ips à tes ip autorisées :<br/>" ;
			while(list($kzert,$prise,$ip{$id_ip},$type) = $DB_admin->next_row()) { 
				echo $ip{$id_ip}."<br/>" ;
				$bool_ip = $bool_ip&&($ip{$id_ip}!=$_SERVER['REMOTE_ADDR']) ;
				$id_ip++;
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
	<h2 id="nom_machine">Nom de tes machines</h2>
	<note>Ceci est la liste de tes ordinateurs</note>
<div>
<? 
for ($i = 0; $i < $id_ip; $i++) {
	echo "<p>".gethostbyaddr($ip{$i})." (".$ip{$i}.")</p>" ;
} 
?>
</div>
<br />
<? for ($i = 0; $i < $id_ip; $i++) {
?>
	<formulaire id="mod_xnet_<? echo $i ?>" titre="Modification du mot de passe Xnet (<? echo $ip{$i} ?>)" action="profil/reseau.php">
		<hidden id="ip_xnet" valeur="<? echo $ip{$i} ?>"/>
		<note>Il faut que ton mot de passe fasse au moins 6 caractères</note>
		<champ id="passwd" titre="Mot de passe" valeur="12345678"/>
		<champ id="passwd2" titre="Retaper le" valeur="87654321"/>
		<bouton id="changer_xnet" titre="Changer"/>
	</formulaire>
<? } 
}else{ 
	echo "<warning>Cette page n'est pas utile pour une personne qui ne loge pas dans l'école</warning>";
}
?>
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>
