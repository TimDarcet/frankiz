<?php
/*
	$Id$
	
	Recherche dans le trombino.
*/

require_once "../include/global.inc.php";

demande_authentification(AUTH_MINIMUM);

// Récupération d'une image
if((isset($_REQUEST['image']))&&($_REQUEST['image'] == "true") && ($_REQUEST['image'] != "")){
	require_once("../include/global.inc.php");
	header('content-type: image/jpeg');
	readfile(BASE_PHOTOS.$_REQUEST['promo']."/".$_REQUEST['login'].".jpg");	
	exit;
}

// Affichage des réponses
if(isset($_REQUEST['chercher'])) {

	// Création de la requète
	$where = "";
	$join = "INNER JOIN sections ON eleves.section_id=sections.section_id";
	$champs = "eleves.eleve_id,eleves.nom,prenom,surnom,piece_id,sections.nom,eleves.section_id,cie,promo,login,mail,0";
	
	$where_exact = array(
			'section' => 'eleves.section_id',	'cie' => 'cie',			/*'type' => '',*/
			'promo' => 'promo');
	foreach($where_exact as $post_arg => $db_field)
		if(!empty($_REQUEST[$post_arg]))
			$where .= (empty($where) ? "" : " AND") . " $db_field='".$_REQUEST[$post_arg]."'";

	$where_like = array(
			'nom' => 'eleves.nom',	'prenom' => 'prenom',   'casert' => 'piece_id',
			/*'phone' => '',*/		'surnom' => 'surnom',   'mail' => 'mail',
			'loginpoly' => 'login');
	foreach($where_like as $post_arg => $db_field)
		if(!empty($_REQUEST[$post_arg]))
			$where .= (empty($where) ? "" : " AND") . " $db_field LIKE '%".$_REQUEST[$post_arg]."%'";
		
	if(!empty($_REQUEST['binet'])) {
		$join = "INNER JOIN membres USING(eleve_id) " . $join;
		$where .= (empty($where) ? "" : " AND") . " binet_id='".$_REQUEST['binet']."'";
	}
	
	// Génération de la page si il y a au moins un critère, sinon on raffiche le formulaire.
	if(!empty($where)) {
		require "../include/page_header.inc.php";
		echo "<page id='trombino' titre='Frankiz : Trombino'>\n";
		
		
		$DB_web->query("SELECT $champs FROM eleves $join WHERE $where");
		while(list($eleve_id,$nom,$prenom,$surnom,$piece_id,$section,$section_id,$cie,$promo,$login,$mail,$tel) = $DB_web->next_row()) {
			echo "<eleve nom='$nom' prenom='$prenom' promo='$promo' login='$login' surnom='$surnom' "
				."tel='$tel' mail='".(empty($mail)?"$login@poly.polytechnique.fr":$mail)."' casert='$piece_id' "
				."section='$section' cie='$cie'>\n";
			
			$DB_web->query("SELECT remarque,nom,membres.binet_id FROM membres "
									 ."LEFT JOIN binets USING(binet_id) WHERE eleve_id='$eleve_id'");
			while(list($remarque,$binet_nom,$binet_id) = $DB_web->next_row())
				echo "<binet nom='".afficher_identifiant($binet_nom)."' id='$binet_id'>".afficher_identifiant($remarque)."</binet>\n";
			
			echo "</eleve>\n";
			if(verifie_permission('admin')) {
				echo "<a href='".BASE_URL."/admin/user.php?id=$eleve_id'>Administrer $prenom $nom</a>" ;
			}
			
		}		
		
		echo "</page>\n";
		require "../include/page_footer.inc.php";
		exit;
	}
}

// Affichage du formulaire de recherche
require "../include/page_header.inc.php";
?>
<page id="trombino" titre="Frankiz : Trombino">
	<formulaire id="trombino" action="trombino/">
		<champ titre="Nom" id="nom" valeur="" />
		<champ titre="Prénom" id="prenom" valeur="" />
		<champ titre="Surnom" id="surnom" valeur="" />
		
		<choix titre="Promo" id="promo" type="combo" valeur="">
			<option titre="Toutes" id="" />
			<option titre="2003" id="2003" />
			<option titre="2002" id="2002" />
			<option titre="2001" id="2001" />
			<option titre="2000" id="2000" />
			<option titre="1999" id="1999" />
			<option titre="1998" id="1998" />
		</choix>
		
		<choix titre="Section" id="section" type="combo" valeur="">
			<option titre="Toutes" id=""/>
<?php
			$DB_web->query("SELECT section_id,nom FROM sections ORDER BY nom ASC");
			while( list($section_id,$section_nom) = $DB_web->next_row() )
				echo "\t\t\t<option titre=\"$section_nom\" id=\"$section_id\"/>\n";
?>
		</choix>
			
		<choix titre="Binet" id="binet" type="combo" valeur="">
			<option titre="Tous" id=""/>
<?php
			$DB_web->query("SELECT binet_id,nom FROM binets ORDER BY nom ASC");
			while( list($binet_id,$binet_nom) = $DB_web->next_row() )
				echo "\t\t\t<option titre=\"$binet_nom\" id=\"$binet_id\"/>\n";
?>
		</choix>
		
		<champ titre="Login poly" id="loginpoly" valeur="" />
		<champ titre="Téléphone" id="phone" valeur="" />
		<champ titre="Casert" id="casert" valeur="" />
		
		<bouton titre="Effacer" id="reset" />
		<bouton titre="Chercher" id="chercher" />
	</formulaire>
</page>
<?php require "../include/page_footer.inc.php" ?>
