<?php
/*
	$Id$
	
	Page permettant de se loguer sur le site. Cette page ne gère pas le login elle même, mais
	grace à la fonction demande_authentification() définie dans login.inc.php. Ça permet d'afficher
	la boîte de login dans d'autres pages qui requiert une authentification.
	
	$Log$
	Revision 1.5  2004/09/15 21:42:15  schmurtz
	Commentaires et ajout de la variable cvs "Log"

*/

require "include/global.inc.php";
demande_authentification(AUTH_MDP);

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="accueil" titre="Frankiz : accueil">
	<h2>Tu es bien authentifié !</h2>
	<p>Te voilà prêt à accéder au fabuleux monde du campus de l'X</p>
	<p>Si tu veux éviter de te ré-identifier à chaque fois que tu accèdes à cette page, alors vas dans tes <a href="profil/profil.php">profils</a> et selectionne l'authentification par cookie</p>
	<p>Sinon navigue sur la page en tout tranquilité ...</p>
</page>
<? require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
