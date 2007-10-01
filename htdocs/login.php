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

// Génération de la page
require_once "include/page_header.inc.php";

call('CoreModule', "login");

require_once "include/page_footer.inc.php" ?>
