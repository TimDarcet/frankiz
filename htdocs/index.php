<?php
require_once "include/global.inc.php";
if (est_authentifie(AUTH_COOKIE)) 
	rediriger_vers("/annonces.php");

// g�n�ration de la page
require "include/page_header.inc.php";
echo "<page id='accueil' titre='Frankiz : accueil'>\n";
?>

<h2>Bienvenue sur FrankizII</h2>

<p>&nbsp;</p>
<p>Voici la nouvelle page �l�ve qui est en construction...</p>
<p>Si tu veux te connecter et acc�der � la partie r�serv�e aux �l�ves alors clique sur ce <a href="login.php">lien</a></p>
<p>Sinon navigue sur cette page en utilisant les liens qui se situe un peu partout sur cette page :)</p>
<?
echo "</page>\n";
require_once "include/page_footer.inc.php";
?>