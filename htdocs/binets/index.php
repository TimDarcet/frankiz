<?php
require_once "../include/global.inc.php";
require_once "../include/binets.inc.php";

// demande_authentification(AUTH_MINIMUM);

// Récupération d'une image
if(isset($_REQUEST['image']) && ($_REQUEST['image'] == "true") && ($_REQUEST['image'] != "")){
  $is_image = true;
}


// Affichage de la liste des binets
require "../include/page_header.inc.php";
?>
<page id="binets" titre="Frankiz : Binets" image="true">

  <?PHP

  connecter_binets();
  $query = "SELECT id,catego FROM categ_binet ORDER BY id";
  $categos = mysql_query($query,$db_binets);

  while($cat = mysql_fetch_assoc($categos)) {
  echo $cat['catego'];
    
    $query = "SELECT id FROM binets WHERE catego=".$cat['id'];
    $ids_binets = mysql_query($query, $db_binets);

    while($binet=mysql_fetch_assoc($ids_binets))
      affiche_binet($binet['id']);

  }
  deconnecter_binets();
  
  ?>
</page>
<? require "../include/page_footer.inc.php" ?>
