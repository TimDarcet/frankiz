<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Page permettant de se loguer sur le site. Cette page ne g�re pas le login elle m�me, mais
	grace � la fonction demande_authentification() d�finie dans login.inc.php. �a permet d'afficher
	la bo�te de login dans d'autres pages qui requiert une authentification.
	
	$Log$
	Revision 1.10  2004/12/14 17:14:52  schmurtz
	modification de la gestion des annonces lues :
	- toutes les annonces sont envoyees dans le XML
	- annonces lues avec l'attribut visible="non"
	- suppression de la page affichant toutes les annonces

	Revision 1.9  2004/11/04 16:36:42  schmurtz
	Modifications cosmetiques
	
	Revision 1.8  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.7  2004/09/16 13:57:49  schmurtz
	word wrap
	
	Revision 1.6  2004/09/15 23:19:45  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.5  2004/09/15 21:42:15  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require "include/global.inc.php";
demande_authentification(AUTH_MDP);

// G�n�ration de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="accueil" titre="Frankiz : accueil">
	<h2>Tu es bien authentifi�!</h2>
	<p>Te voil� pr�t � acc�der au fabuleux monde du campus de l'X.</p>
	<p>Si tu veux �viter de te r�-identifier � chaque fois que tu acc�des � cette page, active l'authentification
	par cookie dans tes <a href="profil/profil.php">pr�f�rences</a>.</p>
</page>
<? require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
