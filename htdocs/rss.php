<?
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
	Affichage de flux rss externes.

	$Id$
	
*/


require_once "include/global.inc.php";
require_once "include/rss_func.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="rss" titre="Frankiz : News Externes">

<?

$liens = $_SESSION['rss'];
if(is_array($liens)){
	foreach($liens as $value => $mode){
		if($mode == 'complet' || $mode == 'sommaire') rss_xml($value,$mode);
	}
}
?>
</page>
<?
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
