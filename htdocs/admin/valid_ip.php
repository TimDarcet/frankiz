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
if(!verifie_permission('admin')) {
	header("Location: ".BASE_URL."/admin/");
	exit;
}
connecter_mysql_frankiz();

// Gestion des détails d'une personne
 foreach ($_POST AS $keys => $val){
        //echo "<p>$keys # $val</p>";
	$temp = explode("_",$keys) ;
	if ($temp[0] == "detail") {
		header("Location: ".BASE_URL."/trombino/?chercher=1&loginpoly=$temp[1]");
		exit;
	}
}

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_valid_ip" titre="Frankiz : Ajouter une ip à un utilisateur">
	<formulaire id="recherche" titre="Recherche" action="admin/ip.php">
		<champ titre="Pièce" id="rech_kzert" valeur="<? echo $_POST['rech_kzert']?>" />
		<champ titre="Prise" id="rech_prise" valeur="<? echo $_POST['rech_prise']?>" />
		<champ titre="Ip" id="rech_ip" valeur="<? echo $_POST['rech_ip']?>" />
		<bouton titre="Recherche" id="recherche"/>
	</formulaire>
</page>

<?php
deconnecter_mysql_frankiz();
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
