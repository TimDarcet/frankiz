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
	Affiche la liste des IPs attribuer aux élèves.
	Permet aussi de supprimer des IPs.
	
	$Log$
	Revision 1.21  2004/10/25 19:17:12  kikx
	Juste un petit warning

	Revision 1.20  2004/10/25 17:19:24  kikx
	Parsage de la page de la DSI pour trouver les mac associer aux prises
	
	Revision 1.19  2004/10/25 15:36:47  kikx
	Recherhce par login
	
	Revision 1.18  2004/10/25 14:05:09  kikx
	Correction d'un bug sur la page
	
	Revision 1.17  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.16  2004/10/20 18:47:07  kikx
	Pour rajouter des lignes non selectionnables dans une liste
	
	Revision 1.15  2004/09/15 23:20:18  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.14  2004/09/15 21:42:27  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	rediriger_vers("/admin/");
	
$message = "" ;
$blabla = "" ;

// Gestion des détails d'une personne
 foreach ($_POST AS $keys => $val){
        //echo "<p>$keys # $val</p>";
	$temp = explode("_",$keys) ;
	if ($temp[0] == "detail")
		rediriger_vers("/trombino/?chercher=1&loginpoly=$temp[1]");
}

function mac($id_prise){

	/*your proxy server address*/
	$proxy = "kuzh.polytechnique.fr";
	/*your proxy server port*/
	$port = 8080;
	/*the url you want to connect to*/
	$url = "http://intranet.polytechnique.fr/SYSRES/SMAC/search.php?id_prise=$id_prise";
	$fp = fsockopen($proxy, $port);
	fputs($fp, "GET $url HTTP/1.0\r\nHost: $proxy\r\n\r\n");
	$line = "" ;
	while(!feof($fp)){
  		$line = fgets($fp, 4000);
		$line = explode("-",$line) ;
		if (count($line)==6) {
			fclose($fp);
			return ($line[0].":".$line[1].":".$line[2].":".$line[3].":".$line[4].":".$line[5]) ;
		}
	}
}



// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_arp" titre="Frankiz : gestion des logs ip">
<?

	if(!empty($message))
		echo "<commentaire>$message</commentaire>\n";
		
	$where = " WHERE 1 " ;
	If (isset($_POST['rech_kzert'])) $where .= "AND p.piece_id LIKE '%".$_POST['rech_kzert']."%' " ;
	If (isset($_POST['rech_login'])) $where .= "AND e.login LIKE '%".$_POST['rech_login']."%' " ;
	if (isset($_POST['rech_prise'])) $where .= "AND p.prise_id  LIKE '%".$_POST['rech_prise']."%' " ;
 	if (isset($_POST['rech_ip'])) $where .= "AND p.ip LIKE'%".$_POST['rech_ip']."%' " ;
	
	$DB_web->query("SELECT  valeur FROM parametres WHERE nom='lastpromo_oncampus'");
	list($lastpromo) = $DB_web->next_row() ;
	$on = " ON ((e.promo='".($lastpromo)."' OR e.promo='".($lastpromo-1)."' OR e.promo IS NULL) AND e.piece_id=p.piece_id) " ;


?>
<commentaire>Evitez de faire des recherches globales sur tout le monde car ça fait 1000 requêtes sur le serveur de la DSI :)</commentaire>
	<formulaire id="recherche" titre="Recherche" action="admin/ip.php">
		<champ titre="Login" id="rech_login" valeur="<? if (isset($_POST['rech_login'])) echo $_POST['rech_login']?>" />
		<champ titre="Pièce" id="rech_kzert" valeur="<? if (isset($_POST['rech_kzert'])) echo $_POST['rech_kzert']?>" />
		<champ titre="Prise" id="rech_prise" valeur="<? if (isset($_POST['rech_prise'])) echo $_POST['rech_prise']?>" />
		<champ titre="Ip" id="rech_ip" valeur="<? if (isset($_POST['rech_ip'])) echo $_POST['rech_ip']?>" />
		<bouton titre="Recherche" id="recherche"/>
	</formulaire>
<?
echo $message ;

if (isset($_POST['recherche']) ) {

	$DB_admin->query("SELECT e.login, e.promo, p.prise_id, p.piece_id, p.ip,p.type FROM prises as p "
					."LEFT JOIN trombino.eleves as e $on  $where ORDER BY p.piece_id ASC");
?>

	<liste id="liste_ip" selectionnable="non" action="admin/ip.php">
		<entete id="login" titre="Login"/>
		<entete id="promo" titre="Promo"/>
		<entete id="piece" titre="Piece"/>
		<entete id="prise" titre="Prise"/>
		<entete id="ip" titre="IP"/>
		<entete id="mac" titre="Mac"/>
<?php
		
		$temp_piece = "" ;
		while(list($login,$promo, $id_prise,$id_piece,$ip_theorique,$type) = $DB_admin->next_row()) {
			echo "\t\t<element id=\"$id_prise\">\n";

			$login2 ="" ;
			$promo2 ="" ;
			
			// Si l'ip est une ip rajouté
			//=====================
			if ($type=="secondaire") {
				echo "\t\t\t<colonne id=\"login\">-</colonne>\n";
				echo "\t\t\t<colonne id=\"promo\">-</colonne>\n";
				echo "\t\t\t<colonne id=\"piece\">-</colonne>\n";
				echo "\t\t\t<colonne id=\"prise\">-</colonne>\n";
				echo "\t\t\t<colonne id=\"ip\"><p>$ip_theorique </p>(secondaire)</colonne>\n";
				echo "\t\t\t<colonne id=\"mac\">-</colonne>\n";
				
//				$texte_brut=file_get_contents("http://intranet.polytechnique.fr/SYSRES/SMAC/search.php?id_prise=$id_prise");
			} else {
			
			// Si l'ip est l'ip par défaut sur la prise
			//=====================
			
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
				echo "\t\t\t<colonne id=\"ip\">$ip_theorique</colonne>\n";
				echo "\t\t\t<colonne id=\"mac\">".mac($id_prise)."</colonne>\n";
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
