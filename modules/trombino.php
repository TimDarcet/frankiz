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

	$Id: trombino.php 1969 2007-09-29 13:02:41Z elscouta $

*/
require_once BASE_LOCAL."/include/wiki.inc.php";
require_once BASE_LOCAL."/include/session.inc.php";

class TrombinoModule extends PLModule
{
	function handlers()
	{
		return array('tol'	=> $this->make_hook('tol', AUTH_COOKIE), // @@TODO@@ This must be fixed!!!!!!!!!!!!!!
			     'binets'   => $this->make_hook('binets', AUTH_PUBLIC));
	}

	function handler_tol(&$page)
	{
		global $DB_admin, $DB_web, $DB_trombino, $DB_xnet;

		$page->assign('title', "Frankiz : Trombino");

		$tol_admin = false;
		if (verifie_permission('admin') || verifie_permission('windows')
		 || verifie_permission('trombino') || verifie_permission('news')
		 || verifie_permission('support'))
			$tol_admin = true;

		if (isset($_REQUEST['toladmin'])) {
			demande_authentification(AUTH_MDP);
		}

?><page id='trombino' titre='Frankiz : Trombino'>
<?php

// Recuperation d'une image dans une page
if (!empty($_GET['image']) && ($_GET['image'] === 'show')){
	if (!isset($_GET['original'])) {
		echo "<image source=\"trombino.php?image=true&amp;login={$_GET['login']}&amp;promo={$_GET['promo']}\" texte=\"photo\"  legende=\"{$_GET['login']} ({$_GET['promo']})\"/>";
		echo "<lien url=\"trombino.php?original&amp;image=show&amp;login={$_GET['login']}&amp;promo={$_GET['promo']}\" titre=\"Voir l'image originale\"/><br/>\n" ;
	} else {
		echo "<image source=\"trombino.php?original&amp;image=true&amp;login={$_GET['login']}&amp;promo={$_GET['promo']}\"  texte=\"photo originale\" legende=\"{$_GET['login']} ({$_GET['promo']}) - originale\"/>";
		echo "<lien url=\"trombino.php?image=show&amp;login={$_GET['login']}&amp;promo={$_GET['promo']}\" titre=\"Voir l'image actuelle\"/><br/>\n" ;
	}
} elseif (isset($_REQUEST['chercher']) || isset($_REQUEST['sections']) || isset($_REQUEST['binets'])
	|| (isset($_REQUEST['anniversaire']) && isset($_REQUEST['promo'])) || isset($_REQUEST['anniversaire_week'])
	|| (isset($_REQUEST['cherchertol']) && (!(empty($_REQUEST['q_search']))))) {

	// Affichage des reponses dans le cas d'une recherche
	
	// Recuperation de la derniere promotion arrivee sur le campus
	$DB_web->query('
		SELECT
			valeur
		FROM
			parametres
		WHERE
			nom = "lastpromo_oncampus"');
	list($promoTOS) = $DB_web->next_row() ;

	define('RECHERCHE_HABITE_SUR_LE_PLATAL', 0);
	define('RECHERCHE_UNE_SEULE_PROMO', 1);
	define('RECHERCHE_TOUTES_PROMOS', 2);
	define('RECHERCHE_PROMOS_ACTUELLES', 3);

	$typeRecherchePromo = RECHERCHE_HABITE_SUR_LE_PLATAL;

	$champs = '
		eleves.eleve_id, eleves.nom, prenom, surnom, login, mail,
		DATE_FORMAT(date_nais, "%d/%m/%Y"),
		eleves.piece_id, pieces.tel,
		eleves.commentaire, promo, cie, eleves.section_id, sections.nom';
	$join = '
		LEFT JOIN sections ON
			eleves.section_id = sections.section_id
		LEFT JOIN pieces ON
			eleves.piece_id = pieces.piece_id ';
	$where = '';

	if (isset($_REQUEST['anniversaire'])) {
		// Création de la requête si anniversaire appelle
		$where .= '
			MONTH(date_nais) = MONTH(NOW())
			AND DAYOFMONTH(date_nais) = DAYOFMONTH(NOW())';
		$typeRecherchePromo = RECHERCHE_UNE_SEULE_PROMO;
	} elseif (isset($_REQUEST['anniversaire_week'])) {
		// Création de la requête si anniversaire appelle
		if (isset($_REQUEST['depart'])) {
			$date1 = $_REQUEST['depart'];
		} else {
			$date1 = date('Y-m-d');
		}
		
		$date2 = date('Y-m-d', strtotime($date1) + 7 * 24 * 3600);
		echo "<commentaire>Liste des personnes fêtant leur anniversaire entre le ".date("d/m",strtotime($date1))." et le ".date("d/m",strtotime($date2))."</commentaire>";
		$where .= " DAYOFYEAR(date_nais + INTERVAL (YEAR(NOW()) - YEAR(date_nais)) YEAR) >= DAYOFYEAR('$date1')
			AND DAYOFYEAR(date_nais + INTERVAL (YEAR(NOW()) - YEAR(date_nais)) YEAR) <= DAYOFYEAR('$date1'+ INTERVAL 7 DAY)";
		$typeRecherchePromo = RECHERCHE_PROMOS_ACTUELLES;
	} elseif(isset($_REQUEST['sections'])) {
		// Création de la requête si sections appelle
		$where .= " sections.nom = '{$_REQUEST['sections']}'  AND (promo = $promoTOS OR promo=".($promoTOS -1).")";
		$typeRecherchePromo = RECHERCHE_PROMOS_ACTUELLES;
	} elseif (isset($_REQUEST['binets'])) {
		// Création de la requête si binet appelle
		$join = "LEFT JOIN membres USING(eleve_id) LEFT JOIN binets ON membres.binet_id = binets.binet_id ".$join;
		$where .= (empty($where) ? "" : " AND")." binets.nom = '".$_REQUEST['binets']."'";
		$typeRecherchePromo = RECHERCHE_PROMOS_ACTUELLES;
	} elseif (isset($_REQUEST['cherchertol'])) {
		// Création de la requête si lien_tol appelle
		$where_like = 'CAST(CONCAT_WS(\' \', eleves.nom, prenom, surnom, login, promo, eleves.piece_id, pieces.tel) AS CHAR)';
		$typeRecherchePromo = RECHERCHE_HABITE_SUR_LE_PLATAL;
		$quick = explode(' ', $_REQUEST['q_search']);
		if (count($quick) == 0) {
			$where = ' 0';
		} else {
			foreach ($quick as $word) {
				$where .= (empty($where) ? '(promo != "0000" AND ' : ' AND '). $where_like . ' LIKE \'%' . $word . '%\'';
				if (is_numeric($word) and (($word>=0 and $word<=($promoTOS%100)) or $word==98 or $word==99 or ($word>=1998 and $word<=$promoTOS))) {
					$typeRecherchePromo = RECHERCHE_TOUTES_PROMOS;
				}
			}
			$where .= ')';
		}
	} elseif (isset($_REQUEST['chercher'])) {
		// l'ordre ci-dessous permet de filtrer correctement la promo, la forme
		// la plus restrictive l'emportant
		$where_like = array(
			'casert'	=>	'eleves.piece_id',
			'phone'		=>	'pieces.tel',
			'prise'		=>	'p.prise_id',
			'ip'		=>	'p.ip',
			'mac'		=>	'a.mac',
			'nom'		=>	'eleves.nom',
			'prenom'	=>	'prenom',
			'surnom'	=>	'surnom',
			'mail'		=>	'mail');

		$correspondanceWhereTypeRecherchePromo = array(
			'casert'	=>	RECHERCHE_TOUTES_PROMOS,
			'phone'		=>	RECHERCHE_TOUTES_PROMOS,
			'prise'		=>  RECHERCHE_TOUTES_PROMOS,
			'ip'		=>  RECHERCHE_TOUTES_PROMOS,
			'mac'		=>  RECHERCHE_TOUTES_PROMOS,
			'nom'		=>	RECHERCHE_HABITE_SUR_LE_PLATAL,
			'prenom'	=>	RECHERCHE_HABITE_SUR_LE_PLATAL,
			'surnom'	=>	RECHERCHE_HABITE_SUR_LE_PLATAL,
			'mail'		=>	RECHERCHE_HABITE_SUR_LE_PLATAL);

		foreach ($where_like as $post_arg => $db_field) {
			if (!empty($_REQUEST[$post_arg])) {
				$where .= (empty($where) ? '' : ' AND')." $db_field LIKE '%".$_REQUEST[$post_arg]."%'";
				$typeRecherchePromo = $correspondanceWhereTypeRecherchePromo[$post_arg];
			}
		}

		if (!empty($_REQUEST['section'])) {
			$where .= (empty($where) ? '' : ' AND').' eleves.section_id = '.$_REQUEST['section'];
			$typeRecherchePromo = RECHERCHE_PROMOS_ACTUELLES;

		}

		if (!empty($_REQUEST['cie'])) {
			$where .= (empty($where) ? '' : ' AND').' cie = '.$_REQUEST['cie'];
			$typeRecherchePromo = RECHERCHE_PROMOS_ACTUELLES;
		}

		if(!empty($_REQUEST['binet'])) {
			$join = "LEFT JOIN membres USING(eleve_id) ".$join;
			$where .= (empty($where) ? "" : " AND")." binet_id='".$_REQUEST['binet']."'";
			$typeRecherchePromo = RECHERCHE_PROMOS_ACTUELLES;
		}

		if(!empty($_REQUEST['loginpoly'])) {
			$where .= (empty($where) ? '' : ' AND ')." login ='".$_REQUEST['loginpoly']."'";
			$typeRecherchePromo = RECHERCHE_HABITE_SUR_LE_PLATAL;
		}

		if (!empty($_REQUEST['promo'])) {
			if ($_REQUEST['promo'] == 'toutes') {
				$typeRecherchePromo = RECHERCHE_TOUTES_PROMOS;
			} else {
				$typeRecherchePromo = RECHERCHE_UNE_SEULE_PROMO;
			}
		}

		if ($tol_admin) {
			if (isset($_REQUEST['mac']) || isset($_REQUEST['prise']) || isset($_REQUEST['mac']) || isset($_REQUEST['ip'])) {
				$join .= '
					LEFT JOIN admin.prises AS p ON
						p.piece_id = pieces.piece_id';
				if (isset($_REQUEST['mac'])) {
					$join .= '
						LEFT JOIN admin.arpwatch_log AS a ON
							a.ip = p.ip';
				}
			}

			if (!empty($_REQUEST['dns'])) {
				$DB_xnet->query("
					SELECT
						lastip
					FROM
						clients
					WHERE
						username LIKE '%{$_REQUEST['dns']}%'");
				if ($DB_xnet->num_rows() > 0) {
					list($ip) = $DB_xnet->next_row();
					$where .= (empty($where) ? '' : ' AND')." (p.ip LIKE '$ip'";
					while (list($ip) = $DB_xnet->next_row()) {
						$where .= " OR p.ip LIKE '$ip'";
					}
					$where .= ')';
				} else {
					$where .= (empty($where) ? '' : ' AND').' 0';
				}
				$typeRecherchePromo = RECHERCHE_TOUTES_PROMOS;
			}
		}

		if (isset($_GET['jeveuxvoirlesfillesdelecole'])){
			$where .= (empty($where) ? '' : ' AND')." sexe = '1'";
		}
	}

	switch ($typeRecherchePromo) {
		case RECHERCHE_HABITE_SUR_LE_PLATAL:
			$where .= (empty($where) ? '' : ' AND').' eleves.piece_id IS NOT NULL';
			break;
		case RECHERCHE_UNE_SEULE_PROMO:
			$where .= (empty($where) ? '' : ' AND')." promo = '{$_REQUEST['promo']}'";
			break;
		case RECHERCHE_TOUTES_PROMOS:
			break;
		case RECHERCHE_PROMOS_ACTUELLES:
			$where .= (empty($where) ? '' : ' AND')." ((promo = $promoTOS) OR (promo = $promoTOS - 1))";
			break;
		default:
			break;
	}

	// Génération de la page si il y a au moins un critère.
	if (!empty($where)) {
		$DB_trombino->query("
			SELECT
				$champs
			FROM
				eleves
				$join
			WHERE
				$where
			GROUP BY
				eleves.eleve_id
			ORDER BY
				promo,
				eleves.nom,
				prenom ASC
			LIMIT 100");

		$nombreDeLignes = $DB_trombino->num_rows();
		// Génération d'un message d'erreur si aucun élève ne correspond
		if ($nombreDeLignes == 0) {
			?><warning> Désolé, aucun élève ne correspond à ta recherche </warning><?php
		} elseif ($nombreDeLignes == 100) {
			?><warning>Trop de résultats: seulement les 100 premiers sont affichés</warning><?php
		} else {
			echo "<commentaire> $nombreDeLignes résultat".($nombreDeLignes == 1 ? '' : 's').' trouvé'.($nombreDeLignes == 1 ? '' : 's').'</commentaire>';
		}
		
		// Génération des fiches des élèves
		while (list(
			$eleve_id, $nom, $prenom, $surnom, $login, $mail,
			$date_nais,
			$piece_id, $tel,
			$commentaire, $promo, $cie, $section_id, $section) = $DB_trombino->next_row()) {

			echo "<eleve nom='$nom' prenom='$prenom' promo='$promo' login='$login' surnom='$surnom' date_nais='$date_nais' "
				."tel='$tel' mail='".(empty($mail)?"$login@poly.polytechnique.fr":$mail)."' casert='$piece_id' "
				."section='$section' cie='$cie'>\n";

			if ($tol_admin && isset($_REQUEST['toladmin'])) {
				// Génération de la liste des ips
				$DB_admin->query("SELECT prise_id, p.ip FROM prises as p WHERE piece_id = '$piece_id'");
				$old_prise = '';
				while (list($prise,$ip) = $DB_admin->next_row()) {
					if ($prise != $old_prise) {
						if ($old_prise != '') {
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
					$DB_admin->query("SELECT mac,ts,vendor FROM arpwatch_log LEFT JOIN arpwatch_vendors ON mac like CONCAT(debut_mac,':%') WHERE ip = '$ip' and ts > '".($promo+1)."-04-15' GROUP BY mac, ts ORDER BY ts");
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
				if($old_prise != '') {
					echo "</prise>";
				}
			}

			// Génération de la liste des binets
			$DB_trombino->push_result();
			$DB_trombino->query("
				SELECT
					remarque, nom, membres.binet_id
				FROM
					membres
				LEFT JOIN binets
					USING(binet_id)
				WHERE
					eleve_id = '$eleve_id'
				ORDER BY
					nom ASC");
			while (list($remarque,$binet_nom,$binet_id) = $DB_trombino->next_row())
				// Mais que c'est moche!. TODO: Remplacer par escape-uri (XSLT 2.0) 
				echo "<binet nom='$binet_nom' nom_encode='".rawurlencode(str_replace("&apos;", "'", html_entity_decode($binet_nom)))."' id='$binet_id'>$remarque</binet>\n";
			$DB_trombino->pop_result();

			echo "<cadre>".wikiVersXML($commentaire)."</cadre>";

			// Supprime les accents
			$nompolyorg = str_replace("&apos;", "", $nom);
			$nompolyorg = htmlentities(strtolower(utf8_decode($nompolyorg)));
			$nompolyorg = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $nompolyorg);
			$nompolyorg = str_replace(" ", "-", $nompolyorg);

			$prenompolyorg = str_replace( "&apos;" , "" , $prenom );
			$prenompolyorg = htmlentities(strtolower(utf8_decode($prenompolyorg)));
			$prenompolyorg = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $prenompolyorg);
			$prenompolyorg = str_replace( " " , "-" , $prenompolyorg );

			echo "<lien url='https://www.polytechnique.org/profile/$prenompolyorg.$nompolyorg.$promo' titre='Fiche sur polytechnique.org'/><br/>\n";

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
	<formulaire id="trombino" action="trombino.php">
		<champ titre="Nom" id="nom" valeur="<?php echo empty($_REQUEST['nom']) ? '' : $_REQUEST['nom']; ?>" />
		<champ titre="Prénom" id="prenom" valeur="<?php echo empty($_REQUEST['prenom']) ? '' : $_REQUEST['prenom']; ?>" />
		<champ titre="Surnom" id="surnom" valeur="<?php echo empty($_REQUEST['surnom']) ? '' : $_REQUEST['surnom']; ?>" />

		<choix titre="Promo" id="promo" type="combo" valeur="<?php echo empty($_REQUEST['promo']) ? '' : $_REQUEST['promo']; ?>">
			<option titre="Sur le campus" id=""/>
			<option titre="Toutes" id="toutes" />

<?php
			$DB_trombino->query("SELECT DISTINCT promo FROM eleves WHERE promo != '0000' ORDER BY promo DESC");
			while (list($promo) = $DB_trombino->next_row()) {
				echo "\t\t\t<option titre=\"$promo\" id=\"$promo\"/>\n";
			}
?>

		</choix>

		<choix titre="Section" id="section" type="combo" valeur="<?php echo empty($_REQUEST['section']) ? '' : $_REQUEST['section']; ?>">
			<option titre="Toutes" id=""/>
<?php
			$DB_trombino->query("SELECT section_id,nom FROM sections ORDER BY nom ASC");
			while( list($section_id,$section_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$section_nom\" id=\"$section_id\"/>\n";
?>
		</choix>

		<choix titre="Binet" id="binet" type="combo" valeur="<?php echo empty($_REQUEST['binet']) ? '' : $_REQUEST['binet']; ?>">
			<option titre="Tous" id=""/>
<?php
			$DB_trombino->query("SELECT binet_id,nom FROM binets ORDER BY nom ASC");
			while( list($binet_id,$binet_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$binet_nom\" id=\"$binet_id\"/>\n";
?>
		</choix>

		<champ titre="Login poly" id="loginpoly" valeur="<?php echo empty($_REQUEST['loginpoly']) ? '' : $_REQUEST['loginpoly']; ?>" />
		<champ titre="Téléphone" id="phone" valeur="<?php echo empty($_REQUEST['phone']) ? '' : $_REQUEST['phone']; ?>" />
		<champ titre="Casert" id="casert" valeur="<?php echo empty($_REQUEST['casert']) ? '' : $_REQUEST['casert']; ?>" />

<?php if($tol_admin){ ?>
		<champ titre="Prise" id="prise" valeur="<?php echo empty($_REQUEST['prise']) ? '' : $_REQUEST['prise']; ?>" />
		<champ titre="IP" id="ip" valeur="<?php echo empty($_REQUEST['ip']) ? '' : $_REQUEST['ip']; ?>" />
		<champ titre="Nom Rezix" id="dns" valeur="<?php echo empty($_REQUEST['dns']) ? '' : $_REQUEST['dns']; ?>" />
		<champ titre="Mac" id="mac" valeur="<?php echo empty($_REQUEST['mac']) ? '' : $_REQUEST['mac']; ?>" />
		<choix titre="Tol Admin" id="admin" type="checkbox" valeur="<?php if(isset($_REQUEST['toladmin'])) echo 'toladmin' ;?>">
				<option id="toladmin" titre=""/>
		</choix>
<?php } ?>

		<bouton titre="Effacer" id="reset" />
		<bouton titre="Chercher" id="chercher" />
	</formulaire>
	<lien url="trombino.php?anniversaire_week&amp;depart=<?php echo date("Y-m-d"); ?>" titre="Anniversaires à souhaiter dans la semaine"/><br/>
	<lien url="wikix/Num%C3%A9ros_utiles" titre="Numéros Utiles"/>
</page>
<?
	}

	function handler_binets(&$page)
	{
		global $DB_trombino;

		$page->assign('title', "Binets");

	echo "<page id='binets'>";
	
	$auth = "" ;
	if(!FrankizSession::verifie_permission('interne')) $auth = " exterieur=1 AND " ;

	$categorie_precedente = -1;
	$DB_trombino->query("SELECT binet_id,nom,description,http,folder,b.catego_id,categorie ".
 						"FROM binets as b LEFT JOIN binets_categorie as c USING(catego_id) ".
						"WHERE $auth NOT(http IS NULL AND folder='')".
						"ORDER BY b.catego_id ASC, b.nom ASC");
	while(list($id,$nom,$description,$http,$folder,$cat_id,$categorie) = $DB_trombino->next_row()) {
			if($folder!=""){ 
				if(FrankizSession::est_interne()) $http=URL_BINETS.$folder."/";
				else $http="binets/$folder/";
			}
?>
		<binet id="<?php echo $id; ?>" categorie="<?php echo $categorie; ?>" nom="<?php echo $nom; ?>">
			<image source="binets.php?image=1&amp;id=<?php echo $id; ?>"  texte="<?php echo $nom; ?>"/>
			<description><?php echo stripslashes($description); ?></description>
			<?php if($http!="") echo "<url>$http</url>"; ?>
		</binet>
<?php
	}
?>
</page>
<?php 
	}
}
?>
