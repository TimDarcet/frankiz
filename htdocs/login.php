<?php
require "include/global.inc.php";
demande_authentification(AUTH_MDP);

// G�n�ration de la page
require_once BASE_LOCAL."/include/page_header.inc.php";

echo "<page id='accueil' titre='Frankiz : accueil'>\n";
?>


<h2>Tu es bien authentifi� !</h2>

<p>&nbsp;</p>
<p>Te voil� pr�t � acc�der au fabuleux monde du campus de l'X</p>
<p>Si tu veux �viter de te r�-identifier � chaque fois que tu acc�des � cette page, alors vas dans tes <a href="profil/profil.php">profils</a> et selectionne l'authentification par cookie</p>
<p>Sinon navigue sur la page en tout tranquilit� ...</p>
<?
echo "</page>\n";
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
