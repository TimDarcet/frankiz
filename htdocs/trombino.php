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

	$Log$
	Revision 1.66  2005/06/16 12:51:33  pico
	Merge du trombi et du trombi admin

	Revision 1.65  2005/05/23 15:38:37  pico
	oublié les "L'"
	
	Revision 1.64  2005/05/23 15:26:57  pico
	Bon en fait, je l'avais pas commité parce que ça marchait pas. Maintenant, ça devrait marcher, à tester aussi
	
	Revision 1.63  2005/05/23 14:52:23  pico
	Pour plus avoir les accents dans les noms des fiches poly.org
	(je croyais avoir commité ça depuis longtemps, c'est un oubli)
	
	Revision 1.62  2005/04/13 17:09:58  pico
	Passage de tous les fichiers en utf8.
	
	Revision 1.61  2005/04/07 21:51:46  fruneau
	Juste pour qu'on puisse laisser les fakes dans la base avec une promo 0000
	
	Revision 1.60  2005/03/14 16:34:32  pico
	Le grand retour de la recherche des tossettes (ne pas communiquer l'url)
	
	Revision 1.59  2005/03/10 16:47:17  pico
	Affiche la photo originale si pas de nouvelle photo
	
	Revision 1.58  2005/01/28 21:41:23  pico
	BugFix /me boulet
	
	Revision 1.57  2005/01/28 21:21:53  pico
	Idem, on peut afficher plus d'images
	
	Revision 1.56  2005/01/26 14:39:44  pico
	Résolution du pb du cookie poly.fr (toutes les photos étaient celles du propriétaire du cookie, pas top..)
	
	Revision 1.55  2005/01/22 17:58:38  pico
	Modif des images
	
	Revision 1.54  2005/01/18 23:24:42  pico
	Ajout fonction tdb
	Modif taille images trombi
	
	Revision 1.53  2005/01/12 22:56:31  pico
	classemnt trombi par promo, maxi 80 résultats
	
	Revision 1.52  2005/01/12 21:40:41  pico
	Erreur
	
	Revision 1.51  2005/01/12 21:39:51  pico
	Affichage photo d'origine
	
	Revision 1.50  2005/01/12 21:34:42  pico
	Change l'affichage des photos du trombi, par contre, on ne peut pas ouvrir dans une nouvelle fenetre, car ce n'est pas valide xhtml strict
	
	Revision 1.49  2005/01/05 20:56:35  pico
	Pour un blattage injustifié dans l'IK, la modif est sortie avant la parution officielle :)
	
	Revision 1.48  2005/01/02 10:50:25  pico
	Passage de certaines pages en visibles de l'intérieur (non loggué)
	
	Revision 1.47  2004/12/17 16:29:29  kikx
	Dans le trombino maintenant les promo sont dynamiques
	Je limit aussi le changement des images (selon leur dimension200x200 dans le trombino)
	Dans les annonces maintenant c'est 400x300 mais < ou egal
	
	Revision 1.46  2004/12/17 13:41:07  kikx
	Poura voir que les resultat des promos sur le campus qd on fait une recherche trombino
	
	Revision 1.45  2004/12/17 13:18:47  kikx
	Rajout des numéros utiles car c'est une demande importante
	
	Revision 1.44  2004/12/17 01:09:08  pico
	Ajout de la date de naissance dans le trombi
	
	Revision 1.43  2004/12/16 14:58:15  pico
	Pfiou
	
	Revision 1.42  2004/12/16 14:57:29  pico
	
	oups
	
	Revision 1.41  2004/12/16 14:55:37  pico
	Rajout des recherches par binet et par section comme sur l'ancien tol
	
	Revision 1.40  2004/12/16 14:30:10  pico
	Recherche trombi par ordre alphabétique
	
	Revision 1.39  2004/12/16 13:00:41  pico
	INNER en LEFT
	
	Revision 1.38  2004/12/15 23:57:35  pico
	On fait comme ça, c'est décidé une fois pour toutes (Kikx + psycow + pico)
	
	Revision 1.37  2004/12/15 23:12:39  pico
	correction warning
	
	Revision 1.36  2004/12/15 23:07:05  pico
	Correction recherche tel sur le trombi
	
	Revision 1.35  2004/12/15 21:09:51  pico
	On peut rechercher les gens dont c'est l'anniversaire dans le trombi
	
	Revision 1.34  2004/12/15 04:32:44  falco
	Bugfix TOL #2 : " " converts into "-"
	
	Revision 1.33  2004/12/15 04:27:46  falco
	c du html
	
	Revision 1.32  2004/12/15 04:25:22  falco
	mieux ainsi
	
	Revision 1.31  2004/12/15 04:24:41  falco
	Bugfix lien xorg TOL apostrophes
	
	Revision 1.30  2004/12/15 03:48:23  kikx
	Sinon ca merde
	
	Revision 1.29  2004/12/12 15:18:59  psycow
	Rechangement blah
	
	Revision 1.28  2004/12/12 13:17:01  psycow
	Modification du Trombino
	
	Revision 1.27  2004/12/09 19:58:59  pico
	Suppression du code en double
	
	Revision 1.26  2004/12/09 19:56:48  pico
	La boite de recherche tol fait aussi les requetes de tel.
	
	Pourquoi ce bout de code est en double ?
	
	Sinon, j'ai importé la base des numéros de tel dans frankiz2.
	
	Revision 1.25  2004/12/09 19:29:13  pico
	Rajoute le tel dans le trombino, ça pourrait être utile...
	
	Revision 1.24  2004/11/27 20:49:10  pico
	Affichage des liens du trombino
	
	Revision 1.23  2004/11/27 15:39:54  pico
	Ajout des droits trombino
	
	Revision 1.22  2004/11/25 02:22:02  schmurtz
	esthetisme (trop long)
	
	Revision 1.21  2004/11/25 01:46:10  schmurtz
	Bug sur l'affichage des liens admin dans le trombino. C'est bien de tester quand
	on fait des modifs.
	
	Revision 1.20  2004/11/22 18:44:08  pico
	Recherche par promo promo présente
	
	Revision 1.19  2004/11/22 10:15:03  pico
	Ajout d'un lien vers le trombi d'X.org
	
	Revision 1.18  2004/11/19 23:04:27  alban
	
	Rajout du module lien_tol
	
	Revision 1.17  2004/11/13 00:25:26  schmurtz
	Rajout du lien d'ace au su
	
	Revision 1.16  2004/11/12 23:32:14  schmurtz
	oublie dans le deplacement du trombino
	
	Revision 1.15  2004/10/24 20:13:22  kikx
	Pour afficher la photo original ...
	
	Revision 1.14  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.13  2004/09/17 13:12:07  schmurtz
	Suppression des <![CDATA[...]>> car les donneÌes des GET et POST (et donc de la base de donneÌes) sont maintenant eÌchappeÌes avec des &amp; &lt; &apos;...
	
	Revision 1.12  2004/09/16 15:32:49  schmurtz
	Suppression de la fonction afficher_identifiant(), utilisation de <![CDATA[......]]> aÌ la place.
	
	Revision 1.11  2004/09/15 23:19:17  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.10  2004/09/15 21:42:01  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "include/global.inc.php";
demande_authentification(AUTH_INTERNE);

$tol_admin = false;
if(verifie_permission('admin')||verifie_permission('windows')||verifie_permission('trombino'))
	$tol_admin = true;

if(isset($_REQUEST['toladmin']))
	demande_authentification(AUTH_FORT);

// Récupération d'une image
if((isset($_GET['image']))&&($_GET['image'] == "true") && ($_GET['image'] != "")){
	require_once "include/global.inc.php";
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

require "include/page_header.inc.php";
echo "<page id='trombino' titre='Frankiz : Trombino'>\n";

// Récupération d'une image dans une page
if((isset($_GET['image']))&&($_GET['image'] == "show") && ($_GET['image'] != "")){
	if (!isset($_GET['original'])) {
		echo "<image source=\"trombino.php?image=true&amp;login={$_GET['login']}&amp;promo={$_GET['promo']}\" texte=\"photo\"  legende=\"{$_GET['login']} ({$_GET['promo']})\"/>";
		echo "<lien url=\"trombino.php?original&amp;image=show&amp;login={$_GET['login']}&amp;promo={$_GET['promo']}\" titre=\"Voir l'image originale\"/><br/>\n" ;
	} else {
		echo "<image source=\"trombino.php?original&amp;image=true&amp;login={$_GET['login']}&amp;promo={$_GET['promo']}\"  texte=\"photo originale\" legende=\"{$_GET['login']} ({$_GET['promo']}) - originale\"/>";
		echo "<lien url=\"trombino.php?image=show&amp;login={$_GET['login']}&amp;promo={$_GET['promo']}\" titre=\"Voir l'image actuelle\"/><br/>\n" ;
	}
}

// Affichage des réponses
if(isset($_REQUEST['chercher'])||isset($_REQUEST['sections'])||isset($_REQUEST['binets'])||(isset($_REQUEST['anniversaire'])&&isset($_REQUEST['promo']))||isset($_REQUEST['anniversaire_week'])||(isset($_REQUEST['cherchertol'])&&(!(empty($_REQUEST['q_search']))))) {
		
	$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
	list($promo_temp) = $DB_web->next_row() ;

	$where = "";
		$join = "LEFT JOIN sections ON eleves.section_id=sections.section_id LEFT JOIN pieces ON eleves.piece_id = pieces.piece_id ";
		$champs = "eleves.eleve_id,eleves.nom,prenom,surnom,date_nais,eleves.piece_id,sections.nom,eleves.section_id,cie,promo,login,mail,pieces.tel";
	
	// Création de la requête si anniversaire appelle
	if(isset($_REQUEST['anniversaire'])) {
		$where .= " MONTH(date_nais)=MONTH(NOW()) AND DAYOFMONTH(date_nais)=DAYOFMONTH(NOW()) AND promo='{$_REQUEST['promo']}'";
	}
	
	// Création de la requête si anniversaire appelle
	if(isset($_REQUEST['anniversaire_week'])) {
		if(isset($_REQUEST['depart'])) 
			$date1=$_REQUEST['depart']; 
		else 
			$date1=date("Y-m-d");
		$date2=date("Y-m-d",strtotime($date1)+7*24*3600);
		echo "<commentaire>Liste des personnes fêtant leur anniversaire entre le ".date("d/m",strtotime($date1))." et le ".date("d/m",strtotime($date2))."</commentaire>";
		$where .= " DAYOFYEAR(date_nais + INTERVAL (YEAR(NOW()) - YEAR(date_nais)) YEAR)>=DAYOFYEAR('$date1') 
			AND DAYOFYEAR(date_nais + INTERVAL (YEAR(NOW()) - YEAR(date_nais)) YEAR)<=DAYOFYEAR('$date1'+ INTERVAL 7 DAY) 
			AND (promo=$promo_temp OR promo=".($promo_temp -1).")";
	}
	
	// Création de la requête si sections appelle
	if(isset($_REQUEST['sections'])) {
		$where .= " sections.nom='{$_REQUEST['sections']}'  AND (promo=$promo_temp OR promo=".($promo_temp -1).")";
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
			if($_REQUEST['promo'] == "")
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
		
		if($tol_admin){
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
					$where .= (empty($where) ? "" : " AND") . " 0";
				}
			}
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
				."tel='$tel' mail='".(empty($mail)?"$login@poly.polytechnique.fr":$mail)."' casert='$piece_id' "
				."section='$section' cie='$cie'>\n";
			
			if($tol_admin && isset($_REQUEST['toladmin'])){
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
			}

			// Génération de la liste des binets
			$DB_trombino->push_result();
			$DB_trombino->query("SELECT remarque,nom,membres.binet_id FROM membres "
							   ."LEFT JOIN binets USING(binet_id) WHERE eleve_id='$eleve_id'");
			while(list($remarque,$binet_nom,$binet_id) = $DB_trombino->next_row())
				echo "<binet nom='$binet_nom' id='$binet_id'>$remarque</binet>\n";
			$DB_trombino->pop_result();
			
			// Supprime les accents
			$nompolyorg = str_replace( "&apos;" , "" , $nom );
			$nompolyorg    = htmlentities(strtolower(utf8_decode($nompolyorg)));
			$nompolyorg    = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $nompolyorg);
			$nompolyorg = str_replace( " " , "-" , $nompolyorg );
			
			$prenompolyorg = str_replace( "&apos;" , "" , $prenom );
			$prenompolyorg    = htmlentities(strtolower(utf8_decode($prenompolyorg)));
			$prenompolyorg    = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $prenompolyorg);
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
	<formulaire id="trombino" action="trombino.php">
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

<? if($tol_admin){ ?>
		<champ titre="Prise" id="prise" valeur="" />
		<champ titre="IP" id="ip" valeur="" />
		<champ titre="Nom Rezix" id="dns" valeur="" />
		<champ titre="Mac" id="mac" valeur="" />
		<choix titre="Tol Admin" id="admin" type="checkbox" valeur="<? if(isset($_REQUEST['toladmin'])) echo 'toladmin' ;?>">
				<option id="toladmin" titre=""/>
		</choix>
<? } ?>

		<bouton titre="Effacer" id="reset" />
		<bouton titre="Chercher" id="chercher" />
	</formulaire>
	<lien url="trombino.php?anniversaire_week&amp;depart=<?echo date("Y-m-d"); ?>" titre="Anniversaires à souhaiter dans la semaine"/><br/>
	<lien url="num_utiles.php" titre="Numéros Utiles"/>
</page>
<?php require "include/page_footer.inc.php" ?>
