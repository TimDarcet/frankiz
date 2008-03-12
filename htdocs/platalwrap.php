<?
/*
	Copyright (C) 2008 Binet Réseau
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
	Charge les modules et lance platal
	
	$Id: activites.php 1981 2007-10-26 01:20:26Z elscouta $

*/

require_once dirname(__FILE__)."/include/global.inc.php";
require_once "../platal-classes/platal.php";
require_once "include/minimodules.inc.php";
require_once "include/wiki.inc.php";

$p = new Platal('core', 'activites', 'annonces', 'qdj');
$p->run();

?>
