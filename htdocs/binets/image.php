<?
////////////////////////////////////////////////////////////////////////////
//
// Petit bout de code permettant d'afficher les images de la base mysql !
//
////////////////////////////////////////////////////////////////////////////


include("../include/binets.inc.php") ;
    connecter_binets();

    $requete = "SELECT image,format FROM binets WHERE id=".$_GET['id'];
    $resultat = @mysql_query($requete);
	$res = mysql_fetch_array ($resultat) ;
    deconnecter_binets();
    header( "content-type: ".$res['format']);
    echo $res['image'];
?> 
