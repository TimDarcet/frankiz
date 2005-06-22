<?php
/*
	Copyright (C) 2004 Binet Réseau
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
	Page permettant de se loguer sur le site. Cette page ne gère pas le login elle même, mais
	grace à la fonction demande_authentification() définie dans login.inc.php. Ça permet d'afficher
	la boîte de login dans d'autres pages qui requiert une authentification.
	
	$Id$
	
*/

require "include/global.inc.php";
demande_authentification(AUTH_MDP);

// Génération de la page
require_once BASE_LOCAL."/include/page_header.inc.php";
?>
<page id="accueil" titre="Frankiz : accueil">
	<h2>Tu es bien authentifié !</h2>
	<p>Te voilà prêt à accéder au fabuleux monde du campus de l'X.</p>
	<p>Si tu veux éviter de te ré-identifier à chaque fois que tu accèdes à cette page, active l'authentification
	par cookie dans tes <a href="profil/profil.php">préférences</a>.</p>
</page>
<? require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
