<?php
require "include/global.inc.php";
demande_authentification(AUTH_MDP);

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";

echo "<page id='accueil' titre='Frankiz : accueil'>\n";
?>


<h2>Tu es bien authentifié !</h2>

<p>&nbsp;</p>
<p>Te voilà prêt à accéder au fabuleux monde du campus de l'X</p>
<p>Si tu veux éviter de te ré-identifier à chaque fois que tu accèdes à cette page, alors vas dans tes <a href="profil/profil.php">profils</a> et selectionne l'authentification par cookie</p>
<p>Sinon navigue sur la page en tout tranquilité ...</p>
<?
echo "</page>\n";
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
