<?php
/*
	$Id$
	
	Page permettant de se loguer sur le site. Cette page ne g�re pas le login elle m�me, mais
	grace � la fonction demande_authentification() d�finie dans login.inc.php. �a permet d'afficher
	la bo�te de login dans d'autres pages qui requiert une authentification.
	
	$Log$
	Revision 1.5  2004/09/15 21:42:15  schmurtz
	Commentaires et ajout de la variable cvs "Log"

*/

require "include/global.inc.php";
demande_authentification(AUTH_MDP);

// G�n�ration de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="accueil" titre="Frankiz : accueil">
	<h2>Tu es bien authentifi� !</h2>
	<p>Te voil� pr�t � acc�der au fabuleux monde du campus de l'X</p>
	<p>Si tu veux �viter de te r�-identifier � chaque fois que tu acc�des � cette page, alors vas dans tes <a href="profil/profil.php">profils</a> et selectionne l'authentification par cookie</p>
	<p>Sinon navigue sur la page en tout tranquilit� ...</p>
</page>
<? require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
