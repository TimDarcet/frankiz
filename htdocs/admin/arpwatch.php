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
	Gestion arpwatch	
	
	$Log$
	Revision 1.10  2005/03/16 17:13:40  pico
	Les admin@windows ont accès à la liste des ips et à l'arpwatch

	Revision 1.9  2005/02/13 11:37:21  kikx
	Permet d'avoir les details de l'arpwatch de facon plus lisible pour une personne ! (pour NC)
	
	Revision 1.8  2005/02/09 20:24:55  kikx
	Allegement de la page de l'arpwatch ... 1 seul requete de zamer qui fait tout d'y coup
	
	Revision 1.7  2005/01/31 00:34:38  kikx
	Avancement de l'arpwatch ... les roots vous pouvez me dire ce que vous voulez sur cette page ?
	
	Revision 1.6  2005/01/23 18:30:48  kikx
	Debut d'une page d'arp watch pour frankiz
	

	
*/

// En-tetes
set_time_limit(0) ;

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('windows'))
	acces_interdit();
	
$message = "" ;
$blabla = "" ;

// Gestion des détails d'une personne
$montrer_detail=0 ;

 foreach ($_REQUEST AS $keys => $val){
        //echo "<p>$keys # $val</p>";
	$temp = explode("_",$keys) ;
	if ($temp[0] == "detail")
		rediriger_vers("/trombino.php?chercher&loginpoly=$temp[1]&promo=$temp[2]");
	if ($temp[0] == "plus") {
		$montrer_detail=1 ;
		$login_detail = $temp[1] ;
		$promo_detail = $temp[2] ;
	}
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

function maj_mac_dans_bdd($fich) {
	global $DB_admin ;
	// On efface toute les entrées avant de recommencer
	$DB_admin->query("DELETE FROM prise_mac") ;
	
	// On re-remplit la base !
	$fich = spliti("<tr>",$fich) ;
	for($i=2 ; $i<count($fich) ; $i++) {
		$temp = spliti("<td>",$fich[$i]) ; 
		
		$mac = str_replace("-",":",strip_tags($temp[2])) ;
		$mac = str_replace("00","0",$mac) ;
		$mac = str_replace("01","1",$mac) ;
		$mac = str_replace("02","2",$mac) ;
		$mac = str_replace("03","3",$mac) ;
		$mac = str_replace("04","4",$mac) ;
		$mac = str_replace("05","5",$mac) ;
		$mac = str_replace("06","6",$mac) ;
		$mac = str_replace("07","7",$mac) ;
		$mac = str_replace("08","8",$mac) ;
		$mac = str_replace("09","9",$mac) ;
		$mac = str_replace("A","a",$mac) ;
		$mac = str_replace("B","b",$mac) ;
		$mac = str_replace("C","c",$mac) ;
		$mac = str_replace("D","d",$mac) ;
		$mac = str_replace("E","e",$mac) ;
		$mac = str_replace("F","f",$mac) ;
		$mac = str_replace("0a","0",$mac) ;
		$mac = str_replace("0b","0",$mac) ;
		$mac = str_replace("0c","0",$mac) ;
		$mac = str_replace("0d","0",$mac) ;
		$mac = str_replace("0e","0",$mac) ;
		$mac = str_replace("0f","0",$mac) ;
		$mac = str_replace("\n","",$mac) ;
		$mac = str_replace("\r","",$mac) ;
		$mac = str_replace("\t","",$mac) ;
		$mac = str_replace(" ","",$mac) ;
		
		$prise = str_replace("\n","",strip_tags($temp[1])) ;
		$prise = str_replace("\r","",$prise) ;
		$prise = str_replace("\t","",$prise) ;
		$prise = str_replace(" ","",$prise) ;
		
		echo $mac ;
		
		$DB_admin->query("INSERT INTO prise_mac SET prise='$prise', mac='$mac' ") ;
	}
}


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_arp" titre="Frankiz : gestion de l'arpwatch">
<?
if ($montrer_detail==1) {
	$DB_admin->query("SELECT e.nom,e.prenom,p.prise_id, p.piece_id FROM prises as p LEFT JOIN trombino.eleves as e ON e.piece_id=p.piece_id WHERE e.promo='$promo_detail' AND e.login='$login_detail' LIMIT 1");
	
	list($nom,$prenom,$id_prise,$id_piece) = $DB_admin->next_row() ;
	echo "<h2>Infos Standards</h2>" ;
	echo "$prenom $nom (X$promo_detail) : $login_detail<br/>" ;
	echo "<br/>Prise : $id_prise<br/>" ;
	echo "Chambre : $id_piece<br/>" ;
	
	echo "<h2>Mac autorisées</h2>" ;	
	$DB_admin->query("SELECT DISTINCT pm.mac FROM prises as p LEFT JOIN trombino.eleves as e ON e.piece_id=p.piece_id LEFT JOIN prise_mac as pm ON pm.prise=p.prise_id WHERE e.promo='$promo_detail' AND e.login='$login_detail'");
	
	while(list($mac) = $DB_admin->next_row()) {
		echo "$mac<br/>" ;
	}
	
	echo "<h2>IP autorisées</h2>" ;	
	$DB_admin->query("SELECT DISTINCT p.ip,p.type FROM prises as p LEFT JOIN trombino.eleves as e ON e.piece_id=p.piece_id LEFT JOIN prise_mac as pm ON pm.prise=p.prise_id WHERE e.promo='$promo_detail' AND e.login='$login_detail'");
	$count =0;
	while(list($ip) = $DB_admin->next_row()) {
		echo "$ip<br/>" ;
		$ip_array[$count] = $ip ;
		$count ++ ;
	}
	
	echo "<h2>IP non autorisées et prises</h2>" ;	
	$DB_admin->query("SELECT al.ts,al.ip FROM prises as p LEFT JOIN trombino.eleves as e ON e.piece_id=p.piece_id LEFT JOIN prise_mac as pm ON pm.prise=p.prise_id LEFT JOIN arpwatch_log as al ON al.mac=pm.mac WHERE e.promo='$promo_detail' AND e.login='$login_detail' ORDER BY al.ts DESC");
	$ip_array= "#".implode($ip_array,",") ;
	while(list($ts,$ip) = $DB_admin->next_row()) {
		if (($ip!="")&&(!ereg("$ip","$ip_array"))) {
			echo "$ip ($ts)<br/>" ;
		}
	}
} else {
	?>
	<h2>Log de la semaine</h2>
	
	<?
		if(!empty($message))
			echo "<commentaire>$message</commentaire>\n";
			
		$where = " WHERE 1 " ;
		If (isset($_REQUEST['rech_kzert'])) $where .= "AND p.piece_id LIKE '%".$_REQUEST['rech_kzert']."%' " ;
		If (isset($_REQUEST['rech_login'])) $where .= "AND e.login LIKE '%".$_REQUEST['rech_login']."%' " ;
		if (isset($_REQUEST['rech_prise'])) $where .= "AND p.prise_id  LIKE '%".$_REQUEST['rech_prise']."%' " ;
		if (isset($_REQUEST['rech_ip'])) $where .= "AND p.ip LIKE'%".$_REQUEST['rech_ip']."%' " ;
		
		
	
	
	?>
		<formulaire id="update_prise" titre="Mise a jour" action="admin/arpwatch.php">
			<commentaire>Mettez à jour grâce à ce bouton la correspondance des prises et des macs</commentaire>
			<bouton titre="Update" id="update"/>
		</formulaire>
		
		<formulaire id="recherche" titre="Recherche" action="admin/arpwatch.php">
			<commentaire>Le nombre de résultat sera limité à 100 </commentaire>
			<champ titre="Login" id="rech_login" valeur="<? if (isset($_REQUEST['rech_login'])) echo $_REQUEST['rech_login']?>" />
			<champ titre="Pièce" id="rech_kzert" valeur="<? if (isset($_REQUEST['rech_kzert'])) echo $_REQUEST['rech_kzert']?>" />
			<champ titre="Prise" id="rech_prise" valeur="<? if (isset($_REQUEST['rech_prise'])) echo $_REQUEST['rech_prise']?>" />
			<champ titre="Ip" id="rech_ip" valeur="<? if (isset($_REQUEST['rech_ip'])) echo $_REQUEST['rech_ip']?>" />
			<bouton titre="Recherche" id="recherche"/>
		</formulaire>
	<?
	echo $message ;
	if (isset($_REQUEST['update']) ) {
		$toutes_macs = toutes_macs() ;
		maj_mac_dans_bdd($toutes_macs) ;
		echo "<commentaire>La base vient d'être correctement mise à jour</commentaire>" ;
	}
	
	if (isset($_REQUEST['recherche']) ) {
		// on trouve la promo !
		
		$DB_web->query("SELECT  valeur FROM parametres WHERE nom='lastpromo_oncampus'");
		list($lastpromo) = $DB_web->next_row() ;
		$on = " ON ((e.promo='".($lastpromo)."' OR e.promo='".($lastpromo-1)."' OR e.promo IS NULL) AND e.piece_id=p.piece_id) " ;
	
		$DB_admin->query("SELECT al.ts,al.ip,pm.mac,e.login, e.promo, p.prise_id, p.piece_id, p.ip,p.type FROM prises as p LEFT JOIN trombino.eleves as e $on LEFT JOIN prise_mac as pm ON pm.prise=p.prise_id LEFT JOIN arpwatch_log as al ON al.mac=pm.mac $where ORDER BY p.piece_id ASC");
	?>
	
		<liste id="liste_ip" selectionnable="non" action="admin/arpwatch.php">
			<entete id="login" titre="Login"/>
			<entete id="ip" titre="IP auth."/>
			<entete id="ip_fo" titre="IP prise"/>
	<?php
			
			$t_mac="";
			$t_login="";
			$t_promo="" ;
			$t_id_prise="";
			$t_id_piece="";
			$t_ip_theorique="" ;
			$t_type=""; 
			$t_ip_prise="" ;
			
			$ip_prise = "" ;
			
			while(list($date,$ip_prise,$mac,$login,$promo, $id_prise,$id_piece,$ip_theorique,$type) = $DB_admin->next_row()) {
				
			
				if ($login==$t_login) {
				// On copie les ips ...
					$t_ip_theorique = str_replace("$ip_theorique","",$t_ip_theorique) ;
					if ($t_ip_theorique!="")
						$t_ip_theorique .= "<br/>".$ip_theorique ;
					else 
						$t_ip_theorique .= $ip_theorique ;
					$t_ip_theorique = str_replace("<br/><br/>","<br/>",$t_ip_theorique) ;
	
				//et on gere les ips qui ne sont prise de facon non autorisé
					if ($ip_prise!="") {
						$t_ip_prise .= "<br/>".$ip_prise." (par $mac le $date)" ;
					}
					
				} else {
					if ($t_id_piece!="") {	
						echo "\t\t<element id=\"$id_prise\">\n";			
						if (strlen($t_login)>0 ) {
							$t_login2 = "<bouton titre='Détails' id='detail_{$t_login}_{$t_promo}' type='detail'/>" ;
							$t_login2 = $t_login2.$t_login ;
						}
						if (strlen($t_ip_theorique)>0 ) {
							$t_ip_theorique2 = "<bouton titre='Détails' id='plus_{$t_login}_{$t_promo}' type='detail'/>" ;
							$t_ip_theorique2 = $t_ip_theorique2.$t_ip_theorique ;
						}
						echo "\t\t\t<colonne id=\"login\">$t_login2<br/><br/><br/></colonne>\n";
						echo "\t\t\t<colonne id=\"ip\">$t_ip_theorique2</colonne>\n";
						echo "\t\t\t<colonne id=\"ip_fo\">".$t_ip_prise." </colonne>\n";
						
						echo "\t\t</element>\n";
					}
					
					$t_login=$login ;
					$t_promo=$promo ;
					$t_id_prise=$id_prise ;
					$t_id_piece=$id_piece ;
					$t_ip_theorique=$ip_theorique ;
					$t_type=$type ;
					$t_ip_prise = $ip_prise." (par $mac le $date)" ;
				}
			}
			
			if ($t_id_piece!="") {	
				echo "\t\t<element id=\"$id_prise\">\n";			
				if (strlen($t_login)>0 ) {
					$t_login2 = "<bouton titre='Détails' id='detail_{$t_login}_{$t_promo}' type='detail'/>" ;
					$t_login2 = $t_login2.$t_login ;
				}
				if (strlen($t_ip_theorique)>0 ) {
					$t_ip_theorique2 = "<bouton titre='Détails' id='plus_{$t_login}_{$t_promo}' type='detail'/>" ;
					$t_ip_theorique2 = $t_ip_theorique2.$t_ip_theorique ;
				}
				echo "\t\t\t<colonne id=\"login\">$t_login2</colonne>\n";
				echo "\t\t\t<colonne id=\"ip\">$t_ip_theorique2</colonne>\n";
				echo "\t\t\t<colonne id=\"ip_fo\">".$t_ip_prise." </colonne>\n";
				
				echo "\t\t</element>\n";
			}
			
			
	?>
		</liste>
	<?
	
	}
}
?>

</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
