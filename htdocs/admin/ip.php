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
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");

// Gestion des détails d'une personne
 foreach ($_POST AS $keys => $val){
        //echo "<p>$keys # $val</p>";
	$temp = explode("_",$keys) ;
	if ($temp[0] == "detail")
		rediriger_vers("/trombino/?chercher=1&loginpoly=$temp[1]");
}

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_arp" titre="Frankiz : gestion des logs ip">
<?

// Gestion de la suppression
if(isset($_POST['supprimer'])) {
	if(isset($_POST['elements'])) {
	
		if (suppression()) {
			$ids = "";
			foreach($_POST['elements'] as $id => $on)
				if($on='on') $ids .= (empty($ids) ? "" : ",") . "'$id'";
				
			
			//mysql_query("DELETE FROM ip_chambre_theory WHERE prise_id IN ($ids)");
			
			$message = "<p>".count($_POST['elements'])." ip viennent d'être supprimées avec succès.</p>\n";
		}
	}
}


	if(!empty($message))
		echo "<commentaire>$message</commentaire>\n";
		
	$where = " WHERE 1 " ;
	If (isset($_POST['rech_kzert'])) $where .= "AND ip_chambre_theory.piece_id LIKE '%".$_POST['rech_kzert']."%' " ;
	if (isset($_POST['rech_prise'])) $where .= "AND ip_chambre_theory.prise_id  LIKE '%".$_POST['rech_prise']."%' " ;
 	if (isset($_POST['rech_ip'])) $where .= "AND ip_chambre_theory.ip_theorique LIKE'%".$_POST['rech_ip']."%' " ;
	
//	$result = mysql_query("SELECT  valeur FROM parametres WHERE nom='lastpromo_oncampus'");
//	list($lastpromo) = mysql_fetch_row($result) ;
//	$on = " ON (eleves.promo='".($lastpromo)."' AND eleves.promo='".($lastpromo-1)."' AND eleves.promo IS NULL AND eleves.piece_id=ip_chambre_theory.piece_id) " ;


?>
	<formulaire id="recherche" titre="Recherche" action="admin/ip.php">
		<champ titre="Pièce" id="rech_kzert" valeur="<? if (isset($_POST['rech_kzert'])) echo $_POST['rech_kzert']?>" />
		<champ titre="Prise" id="rech_prise" valeur="<? if (isset($_POST['rech_prise'])) echo $_POST['rech_prise']?>" />
		<champ titre="Ip" id="rech_ip" valeur="<? if (isset($_POST['rech_ip'])) echo $_POST['rech_ip']?>" />
		<bouton titre="Recherche" id="recherche"/>
	</formulaire>
<?
if (isset($_POST['recherche']) ) {

	$query2 = "SELECT eleves.login, eleves.promo, ip_chambre_theory.prise_id, ip_chambre_theory.piece_id, ip_chambre_theory.ip_theorique FROM eleves RIGHT JOIN ip_chambre_theory USING(piece_id) $where ORDER BY ip_theorique ASC" ;

	$DB_admin->query($query2);

?>

	<liste id="liste_ip" selectionnable="oui" action="admin/ip.php">
		<entete id="login" titre="Login"/>
		<entete id="promo" titre="Promo"/>
		<entete id="piece" titre="Piece"/>
		<entete id="prise" titre="Prise"/>
		<entete id="ip" titre="IP"/>
<?php
		
		$temp_piece = "" ;
		while(list($login,$promo, $id_prise,$id_piece,$ip_theorique) = $DB_admin->next_row()) {
			echo "\t\t<element id=\"$id_prise\">\n";

			$login2 ="" ;
			$promo2 ="" ;
			if (($temp_piece==$id_piece)&&($temp_prise==$id_prise)){
				$id_piece = "###" ;
				$id_prise = "###" ;
			}
			
			if (strlen($login)>0 ) 
				$login = "<bouton titre='Détails' id='detail_$login' type='detail'/>".$login ;
			
			echo "\t\t\t<colonne id=\"login\">$login</colonne>\n";
			echo "\t\t\t<colonne id=\"promo\">$promo</colonne>\n";
			echo "\t\t\t<colonne id=\"piece\">$id_piece</colonne>\n";
			echo "\t\t\t<colonne id=\"prise\">$id_prise</colonne>\n";
			
			// On sauve temporaitement la piece
			$temp_piece = $id_piece ;
			$temp_prise = $id_prise ;
			
			
			
//=======================
//TEMP

/*			$ip = "129.104." ;
			$aaa = "" ;
			$bbb = "" ;
			$temp_aaa = substr($id_prise,0,2) ;
			
			if ($temp_aaa=="A0") $aaa="205" ;
			else if ($temp_aaa=="A1") $aaa="206" ;
			else if ($temp_aaa=="A2") $aaa="207" ;
			else if ($temp_aaa=="A3") $aaa="208" ;
			else if ($temp_aaa=="B0") $aaa="215" ;
			else if ($temp_aaa=="B1") $aaa="216" ;
			else if ($temp_aaa=="B2") $aaa="217" ;
			else if ($temp_aaa=="B3") $aaa="218" ;
			else if ($temp_aaa=="C0") $aaa="209" ;
			else if ($temp_aaa=="C1") $aaa="210" ;
			else if ($temp_aaa=="C2") $aaa="211" ;
			else if ($temp_aaa=="C3") $aaa="212" ;
			else if ($temp_aaa=="D0") $aaa="219" ;
			else if ($temp_aaa=="D1") $aaa="220" ;
			else if ($temp_aaa=="D2") $aaa="221" ;
			else if ($temp_aaa=="D3") $aaa="222" ;
			else if ($temp_aaa=="M0") $aaa="203" ;
			else if ($temp_aaa=="N0") $aaa="204" ;
			else if ($temp_aaa=="17") $aaa="214" ;
			
			
			
			if (($temp_aaa!="M0")&&($temp_aaa!="N0")&&($temp_aaa!="17")) {
				$temp_bbb = substr($id_prise,2,2) ;
				$bbb = 120 + $temp_bbb ;
			} else if (($temp_aaa=="M0")||($temp_aaa=="N0")) {
				$temp_bbb = substr($id_prise,2,2) ;
				$bbb = 50 + $temp_bbb ;
			} else {
				$temp_bbb = substr($id_prise,3,2) ;
				if ($temp_bbb == "10")
					$bbb = 13 + substr($id_prise,6,2) ;
				if ($temp_bbb == "20")
					$bbb = 66 + substr($id_prise,6,2) ;
			}
			mysql_query("UPDATE ip_chambre_theory SET ip_theorique='$ip$aaa.$bbb' WHERE prise_id='$id_prise'");*/
			
			echo "\t\t\t<colonne id=\"ip\">$ip_theorique</colonne>\n";
			echo "\t\t</element>\n";
		
//=======================

		}
?>
		<bouton titre="Supprimer" id="supprimer"/>
	</liste>
<?
}
?>

</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
