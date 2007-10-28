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
	Loggue les appels à AOC, et redirige vers AOC si autorisé.
*/

require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

demande_permission('interne');

// Récupération de l'IP
$ip = ip_get();

// Log
ajouter_access_log(
	"AppleOnCampus: acces depuis $ip / user " .
	(is_object($_SESSION['user']) ? $_SESSION['uid'] : -1 ));

// Redirection
if (defined('PARTENAIRES_AOC_URL'))
{
	echo "<html>\n";
	echo "<head><title>Boutique Apple On Campus</title></head>";
	echo "<body>Cliquez sur le lien pour entrer dans ";
	echo "<a href=\"". PARTENAIRES_AOC_URL ."\">la boutique Apple On Campus</a>.</body>\n";

	die();
}
// Sinon ...
require '../include/page_header.inc.php';
?>
<page id='partenaires_aoc' titre='Frankiz : Apple On Campus'>
  <cadre titre="Erreur !">
  <p>Nous sommes désolé, mais la page <em>Apple On Campus</em> est actuellement indisponible.</p>
  </cadre>
</page>
<?php require "../include/page_footer.inc.php" ?>
