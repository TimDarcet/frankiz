<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Gestion arpwatch	
	
	$Log$
	Revision 1.6  2005/01/23 18:30:48  kikx
	Debut d'une page d'arp watch pour frankiz


	
*/

// En-tetes
set_time_limit(0) ;

require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	acces_interdit();
	
$message = "" ;
$blabla = "" ;

// Gestion des d�tails d'une personne
 foreach ($_REQUEST AS $keys => $val){
        //echo "<p>$keys # $val</p>";
	$temp = explode("_",$keys) ;
	if ($temp[0] == "detail")
		rediriger_vers("/trombino.php?chercher&loginpoly=$temp[1]&promo=$temp[2]");
}
function toutes_macs(){
	$port = 80 ;
	$url = "http://".DSI_URL."/SMAC/search.php" ;
	$fp = fsockopen(DSI_URL, $port);
	fputs($fp, "GET $url HTTP/1.0\r\nHost: ".DSI_URL."\r\n\r\n");
	$line = "" ;
	while(!feof($fp)){
		$line .= fgets($fp,4000);
	}
	return $line ;
}

function mac($id_prise,$fich){
	$resultat = "" ;
	$fich = spliti("<tr>",$fich) ;
	for($i=1 ; $i<count($fich) ; $i++) {
		if (eregi("$id_prise",$fich[$i])) {
			$fich[$i] = spliti("<td>",$fich[$i]) ; 
			$resultat .= "<p>".strip_tags($fich[$i][2])."</p>" ;
		} 
	}
	return $resultat ;
}

function ip_non_auth($ipnormal,$mac) {
	global $DB_admin ;
	
	$macformat = str_replace("-",":",$mac) ;
	$macformat = str_replace(":0",":",$macformat) ;
	$macformat = str_replace("00","0",$macformat) ;
	$macformat = str_replace("<p>","",$macformat) ;
	$macformat = str_replace("</p>","",$macformat) ;
	$macformat = str_replace("\n","",$macformat) ;
	$macformat = str_replace("\r","",$macformat) ;
	$macformat = str_replace(" ","",$macformat) ;
	
	$result = "" ;

	$DB_admin->push_result() ;
	$DB_admin->query("SELECT ip,ts FROM arpwatch_log WHERE mac='$macformat' ORDER BY ts DESC") ;
	while(list($ip,$timestamp) = $DB_admin->next_row()) {
		$year = substr($timestamp, 2, 2);
		$month = substr($timestamp, 5, 2);
		$day = substr($timestamp, 8, 2);
		$heure = substr($timestamp, 11,8) ;
		$date = "$day/$month/$year � $heure" ;
		
		$test = explode(".",$ip) ;
		if (($test[0]!="129")||($test[1]!="104")){
			//$result .= "pas bon = $test[0] / $test[1] " ; 
			break ;
		}
		if ($ip == $ipnormal) 
			$result .= "<p>$ip ($date)</p>" ;
		else 
			$result .= "<p><old_string>$ip</old_string> ($date)</p>" ;
	}
	$DB_admin->pop_result() ;
	return $result ;
}



// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_arp" titre="Frankiz : gestion des logs ip">
<?
	if(!empty($message))
		echo "<commentaire>$message</commentaire>\n";
		
	$where = " WHERE 1 " ;
	If (isset($_REQUEST['rech_kzert'])) $where .= "AND p.piece_id LIKE '%".$_REQUEST['rech_kzert']."%' " ;
	If (isset($_REQUEST['rech_login'])) $where .= "AND e.login LIKE '%".$_REQUEST['rech_login']."%' " ;
	if (isset($_REQUEST['rech_prise'])) $where .= "AND p.prise_id  LIKE '%".$_REQUEST['rech_prise']."%' " ;
 	if (isset($_REQUEST['rech_ip'])) $where .= "AND p.ip LIKE'%".$_REQUEST['rech_ip']."%' " ;
	
	$DB_web->query("SELECT  valeur FROM parametres WHERE nom='lastpromo_oncampus'");
	list($lastpromo) = $DB_web->next_row() ;
	$on = " ON ((e.promo='".($lastpromo)."' OR e.promo='".($lastpromo-1)."' OR e.promo IS NULL) AND e.piece_id=p.piece_id) " ;


?>
<note>Si la page ne marche pas trop, allez sur la page de la <lien titre="DSI" url="http://<?=DSI_URL?>/SMAC/main.php"/></note>
	<formulaire id="recherche" titre="Recherche" action="admin/arpwatch.php">
		<champ titre="Login" id="rech_login" valeur="<? if (isset($_REQUEST['rech_login'])) echo $_REQUEST['rech_login']?>" />
		<champ titre="Pi�ce" id="rech_kzert" valeur="<? if (isset($_REQUEST['rech_kzert'])) echo $_REQUEST['rech_kzert']?>" />
		<champ titre="Prise" id="rech_prise" valeur="<? if (isset($_REQUEST['rech_prise'])) echo $_REQUEST['rech_prise']?>" />
		<champ titre="Ip" id="rech_ip" valeur="<? if (isset($_REQUEST['rech_ip'])) echo $_REQUEST['rech_ip']?>" />
		<bouton titre="Recherche" id="recherche"/>
	</formulaire>
<?
echo $message ;

if (isset($_REQUEST['recherche']) ) {
	$toutes_macs = toutes_macs() ;

	$DB_admin->query("SELECT e.login, e.promo, p.prise_id, p.piece_id, p.ip,p.type FROM prises as p "
					."LEFT JOIN trombino.eleves as e $on $where ORDER BY p.piece_id ASC");
?>

	<liste id="liste_ip" selectionnable="non" action="admin/arpwatch.php">
		<entete id="login" titre="Login"/>
		<entete id="promo" titre="Promo"/>
		<entete id="piece" titre="Piece"/>
		<entete id="prise" titre="Prise"/>
		<entete id="ip" titre="IP auth."/>
		<entete id="ip_fo" titre="IP non auth"/>
		<entete id="mac" titre="Mac"/>
<?php
		
		$temp_piece = "" ;
		while(list($login,$promo, $id_prise,$id_piece,$ip_theorique,$type) = $DB_admin->next_row()) {
			echo "\t\t<element id=\"$id_prise\">\n";

			$login2 ="" ;
			$promo2 ="" ;
			
			// Si l'ip est une ip rajout�
			//=====================
			if ($type=="secondaire") {
				echo "\t\t\t<colonne id=\"login\">-</colonne>\n";
				echo "\t\t\t<colonne id=\"promo\">-</colonne>\n";
				echo "\t\t\t<colonne id=\"piece\">-</colonne>\n";
				echo "\t\t\t<colonne id=\"prise\">-</colonne>\n";
				echo "\t\t\t<colonne id=\"ip\"><p>$ip_theorique </p>(secondaire)</colonne>\n";
				echo "\t\t\t<colonne id=\"ip_fo\">?</colonne>\n";
				echo "\t\t\t<colonne id=\"mac\">-</colonne>\n";
				
//				$texte_brut=file_get_contents("http://intranet.polytechnique.fr/SYSRES/SMAC/search.php?id_prise=$id_prise");
			} else {
			
			// Si l'ip est l'ip par d�faut sur la prise
			//=====================
			
				if (($temp_piece==$id_piece)&&($temp_prise==$id_prise)){
					$id_piece = "###" ;
					$id_prise = "###" ;
				}
				
				if (strlen($login)>0 ) 
					$login = "<bouton titre='D�tails' id='detail_{$login}_{$promo}' type='detail'/>".$login ;
				$macmac= mac($id_prise,$toutes_macs) ;
				echo "\t\t\t<colonne id=\"login\">$login</colonne>\n";
				echo "\t\t\t<colonne id=\"promo\">$promo</colonne>\n";
				echo "\t\t\t<colonne id=\"piece\">$id_piece</colonne>\n";
				echo "\t\t\t<colonne id=\"prise\">$id_prise</colonne>\n";
				echo "\t\t\t<colonne id=\"ip\">$ip_theorique</colonne>\n";
				echo "\t\t\t<colonne id=\"ip_fo\">".ip_non_auth($ip_theorique,$macmac)." </colonne>\n";
				echo "\t\t\t<colonne id=\"mac\">".$macmac."</colonne>\n";
			}
			

			// On sauve temporaitement la piece
			$temp_piece = $id_piece ;
			$temp_prise = $id_prise ;
			
			echo "\t\t</element>\n";
		}
?>
	</liste>
<?

}
?>

</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
