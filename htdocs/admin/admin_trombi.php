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
	Recherche dans le trombino.

*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('windows')&&!verifie_permission('trombino'))
        acces_interdit();


// Récupération d'une image
if((isset($_GET['image']))&&($_GET['image'] == "true") && ($_GET['image'] != "")){
	require_once "../include/global.inc.php";
	if (!isset($_GET['original'])&&(file_exists(BASE_PHOTOS.$_GET['promo']."/".$_GET['login'].".jpg"))) {
		$size = getimagesize(BASE_PHOTOS.$_GET['promo']."/".$_GET['login'].".jpg");
		header("Content-type: {$size['mime']}");
		readfile(BASE_PHOTOS.$_GET['promo']."/".$_GET['login'].".jpg");
	} else {
		$size = getimagesize(BASE_PHOTOS.$_GET['promo']."/".$_GET['login']."_original.jpg");
		header("Content-type: {$size['mime']}");
		readfile(BASE_PHOTOS.$_GET['promo']."/".$_GET['login']."_original.jpg");		
	}
	exit;
}

if(isset($_GET['tdb'])&&isset($_GET['promo'])){
	$DB_trombino->query("SELECT login,nom,prenom FROM eleves WHERE promo='{$_GET['promo']}' ORDER BY promo,nom,prenom ASC");
	echo "#\n";
	while(list($login,$nom,$prenom) = $DB_trombino->next_row())
		echo "$login:$nom:$prenom\n";
	echo "#\n";
	exit;
}

require "../include/page_header.inc.php";
echo "<page id='trombino' titre='Frankiz : Trombino'>\n";

// Affichage des réponses
if(isset($_REQUEST['chercher'])||isset($_REQUEST['sections'])||isset($_REQUEST['binets'])||(isset($_REQUEST['anniversaire'])&&isset($_REQUEST['promo']))||isset($_REQUEST['anniversaire_week'])||(isset($_REQUEST['cherchertol'])&&(!(empty($_REQUEST['q_search']))))) {
		
	$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
	list($promo_temp) = $DB_web->next_row() ;

	$group = "";
	$where = "";
		$join = "LEFT JOIN sections ON eleves.section_id=sections.section_id LEFT JOIN pieces ON eleves.piece_id = pieces.piece_id";
		$champs = "eleves.eleve_id,eleves.nom,prenom,surnom,date_nais,eleves.piece_id,sections.nom,eleves.section_id,cie,promo,login,mail,pieces.tel";
	
	// Création de la requête si sections appelle
	if(isset($_REQUEST['sections'])) {
		$where .= " sections.nom='{$_REQUEST['sections']}'  AND (promo=$promo_temp OR promo=".($promo_temp -1).")";
	}

	if(isset($_REQUEST['mac']) || isset($_REQUEST['prise']) || isset($_REQUEST['mac']) || isset($_REQUEST['ip'])) {
		$join .= " LEFT JOIN admin.prises as p ON p.piece_id = pieces.piece_id";
		if(isset($_REQUEST['mac'])) {
			$join .= " LEFT JOIN admin.arpwatch_log as a ON a.ip = p.ip";
		}
	}
	
	if(!empty($_REQUEST['dns'])) {
		$DB_xnet->query("SELECT lastip FROM clients WHERE username LIKE '%{$_REQUEST['dns']}%'");
		if($DB_xnet->num_rows() > 0) {
			list($ip) = $DB_xnet->next_row();
			$where .= (empty($where) ? "" : " AND") . " (p.ip like '$ip'";
			while(list($ip) = $DB_xnet->next_row()) {
				$where .= " OR p.ip LIKE '$ip'";
			}
			$where .= ")";
		} else {
			$where = (empty($where) ? "" : " AND") . " 0";
		}
	}
	
	// Création de la requête si binet appelle
	if(isset($_REQUEST['binets'])) {
			$join = "LEFT JOIN membres USING(eleve_id) LEFT JOIN binets ON membres.binet_id=binets.binet_id " . $join;
			$where .= (empty($where) ? "" : " AND") . " binets.nom='".$_REQUEST['binets']."' AND (promo=$promo_temp OR promo=".($promo_temp -1).")";
	}
	// Création de la requête si lien_tol appelle
	if(isset($_REQUEST['cherchertol'])) {
		$where_like = array(
			'nom' => 'eleves.nom',	'prenom' => 'prenom',	'surnom' => 'surnom');
		foreach($where_like as $post_arg => $db_field)
			$where .= (empty($where) ? "(" : " OR") . " $db_field LIKE '%".$_REQUEST['q_search']."%'";
		$where .= ") AND (promo=$promo_temp OR promo=".($promo_temp -1).")";
	}
	
	// Création de la requète si tol s'appelle
	if(isset($_REQUEST['chercher'])) {
		$where_exact = array(
				'section' => 'eleves.section_id',	'cie' => 'cie');
		foreach($where_exact as $post_arg => $db_field)
			if(!empty($_REQUEST[$post_arg]))
				$where .= (empty($where) ? "" : " AND") . " $db_field='".$_REQUEST[$post_arg]."'";
			if(empty($_REQUEST['promo']))
				$where .=  (empty($where) ? "" : " AND") ." (promo=$promo_temp OR promo=".($promo_temp -1).")";
			else if($_REQUEST['promo'] != "toutes")
				$where .= (empty($where) ? "" : " AND") ." promo='".$_REQUEST['promo']."'";

		$where_like = array(
				'nom' => 'eleves.nom',	'prenom' => 'prenom',   'casert' => 'eleves.piece_id',
				'phone' => 'pieces.tel',		'surnom' => 'surnom',   'mail' => 'mail',
				'loginpoly' => 'login', 'prise' => 'p.prise_id', 'ip' => 'p.ip', 'mac' => 'a.mac');
		foreach($where_like as $post_arg => $db_field)
			if(!empty($_REQUEST[$post_arg]))
				$where .= (empty($where) ? "" : " AND") . " $db_field LIKE '%".$_REQUEST[$post_arg]."%'";
			
		if(!empty($_REQUEST['binet'])) {
			$join = "LEFT JOIN membres USING(eleve_id) " . $join;
			$where .= (empty($where) ? "" : " AND") . " binet_id='".$_REQUEST['binet']."'";
		}
		
		if(isset($_GET['jeveuxvoirlesfillesdelecole'])){
			$where .= (empty($where) ? "" : " AND") . " sexe='1'";
		}
	}
	
	// Génération de la page si il y a au moins un critère.
	if(!empty($where)) {	
		
		$DB_trombino->query("SELECT $champs FROM eleves $join WHERE $where GROUP BY eleves.eleve_id ORDER BY promo,eleves.nom,prenom ASC LIMIT 80");
		
		// Génération d'un message d'erreur si aucun élève ne correspond
		if($DB_trombino->num_rows()==0)
		echo "<warning> Désolé, aucun élève ne correspond à ta recherche </warning>";
		if($DB_trombino->num_rows()==80)
		echo "<warning>Trop de résultats: seulement les 80 premiers sont affichés</warning>";
		// Génération des fiches des élèves
		while(list($eleve_id,$nom,$prenom,$surnom,$date_nais,$piece_id,$section,$section_id,$cie,$promo,$login,$mail,$tel) = $DB_trombino->next_row()) {
			$date_nais = date("d/m/Y",strtotime($date_nais));
			echo "<eleve nom='$nom' prenom='$prenom' promo='$promo' login='$login' surnom='$surnom' date_nais='$date_nais' "
				."tel='$tel' mail='$login@poly.polytechnique.fr' casert='$piece_id' "
				."section='$section' cie='$cie'>\n";

			// Génération de la liste des ips
			$DB_admin->query("SELECT prise_id,p.ip FROM prises as p WHERE piece_id = '$piece_id'");
			$old_prise = "";
			while(list($prise,$ip) = $DB_admin->next_row()) {
				if($prise != $old_prise) {
					if($old_prise != "") {
						echo "</prise>";
					}
					echo "<prise id='$prise'>";
					$old_prise = $prise;
				}

				$DB_xnet->query("SELECT username,if( ((options & 0x1c0) >> 6) = 1, 'Windows 9x', if( ((options & 0x1c0) >> 6)= 2, 'Windows XP', if( ((options & 0x1c0) >> 6) = 3, 'Linux', if( ((options & 0x1c0) >> 6)= 4, 'MacOS', if( ((options & 0x1c0) >> 6), 'MacOS X', 'Inconnu'))))),s.name FROM clients LEFT JOIN software as s ON clients.version = s.version  WHERE lastip like '$ip'");
				list($dns,$os,$client) = $DB_xnet->next_row();
				if(empty($client)) $client = 'Pas de client';
				if(empty($dns)) $dns = "&lt;none>";
				echo "<ip id='$ip' dns='$dns' os='$os' clientxnet='$client'>";

				// Toutes les macs qui ont été associées à cette ip
				// On ne prend en compte que les macs qui correspondent à la période où la promo est sur le campus
				$DB_admin->push_result();
				$DB_admin->query("SELECT mac,ts,vendor FROM arpwatch_log LEFT JOIN arpwatch_vendors ON mac like CONCAT(debut_mac,':%') WHERE ip = '$ip' and ts > '".($promo+1)."-05-01' GROUP BY mac, ts ORDER BY ts");	
				while(list($mac, $ts,$vendor) = $DB_admin->next_row()) {
					if(!empty($mac)) {
						if(empty($vendor)) $vendor = "<inconnu>";
						$vendor = htmlentities($vendor, ENT_QUOTES);
						echo "<mac id='$mac' time='$ts' constructeur='$vendor'/>";
					}
				}
				$DB_admin->pop_result();
				echo "</ip>";
			}
			if($old_prise != "") {
				echo "</prise>";
			}
			
			
			// Génération de la liste des binets
			$DB_trombino->push_result();
			$DB_trombino->query("SELECT remarque,nom,membres.binet_id FROM membres "
							   ."LEFT JOIN binets USING(binet_id) WHERE eleve_id='$eleve_id'");
			while(list($remarque,$binet_nom,$binet_id) = $DB_trombino->next_row())
				echo "<binet nom='$binet_nom' id='$binet_id'>$remarque</binet>\n";
			$DB_trombino->pop_result();
			
			
			// Echappe les '
			$nompolyorg = str_replace( "&apos;" , "" , $nom );
			$prenompolyorg = str_replace( "&apos;" , "" , $prenom );
			
			// Echappe les espaces
			$nompolyorg = str_replace( " " , "-" , $nompolyorg );
			$prenompolyorg = str_replace( " " , "-" , $prenompolyorg );
			
			echo "<lien url='https://www.polytechnique.org/fiche.php?user=$prenompolyorg.$nompolyorg.$promo' titre='Fiche sur polytechnique.org'/><br/>\n";
			
			// Liens d'administration
			if(verifie_permission('admin')||verifie_permission('trombino')) {
				echo "<lien url='".BASE_URL."/admin/user.php?id=$eleve_id' titre='Administrer $prenom $nom'/><br/>\n" ;
			}
			if(verifie_permission('admin')) {
				echo "<lien url='".BASE_URL."/?su=$eleve_id' titre='Prendre l&apos;identité de $prenom $nom'/><br/>\n" ;
			}
			echo "</eleve>\n";
			echo "<br/>";
		}
	}
}

// Affichage du formulaire de recherche
?>
	<formulaire id="trombino" action="admin/admin_trombi.php">
		<champ titre="Nom" id="nom" valeur="" />
		<champ titre="Prénom" id="prenom" valeur="" />
		<champ titre="Surnom" id="surnom" valeur="" />
		
		<choix titre="Promo" id="promo" type="combo" valeur="">
			<option titre="Sur le campus" id=""/>
			<option titre="Toutes" id="toutes" />

<?php
			$DB_trombino->query("SELECT DISTINCT promo FROM eleves WHERE promo != '0000' ORDER BY promo DESC");
			while( list($promo) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$promo\" id=\"$promo\"/>\n";
?>

		</choix>
		
		<choix titre="Section" id="section" type="combo" valeur="">
			<option titre="Toutes" id=""/>
<?php
			$DB_trombino->query("SELECT section_id,nom FROM sections ORDER BY nom ASC");
			while( list($section_id,$section_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$section_nom\" id=\"$section_id\"/>\n";
?>
		</choix>
			
		<choix titre="Binet" id="binet" type="combo" valeur="">
			<option titre="Tous" id=""/>
<?php
			$DB_trombino->query("SELECT binet_id,nom FROM binets ORDER BY nom ASC");
			while( list($binet_id,$binet_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$binet_nom\" id=\"$binet_id\"/>\n";
?>
		</choix>
		
		<champ titre="Login poly" id="loginpoly" valeur="" />
		<champ titre="Téléphone" id="phone" valeur="" />
		<champ titre="Casert" id="casert" valeur="" />
		<champ titre="Prise" id="prise" valeur="" />
		<champ titre="IP" id="ip" valeur="" />
		<champ titre="Nom Rezix" id="dns" valeur="" />
		<champ titre="Mac" id="mac" valeur="" />
		
		<bouton titre="Effacer" id="reset" />
		<bouton titre="Chercher" id="chercher" />
	</formulaire>
</page>
<?php require "../include/page_footer.inc.php" ?>
