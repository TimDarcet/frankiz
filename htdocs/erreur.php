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
	$Log$
	Revision 1.1  2004/12/17 17:25:08  schmurtz
	Ajout d'une belle page d'erreur.

*/

$erreur_textes = array(
	403 => 'Acc�s interdit',
	404 => 'Page inexistante',
	500 => 'Erreur interne du serveur');

$erreur_num = empty($_GET['erreur']) ? 500 : $_GET['erreur'];
$erreur_texte = isset($erreur_textes[$erreur_num]) ? "({$erreur_textes[$erreur_num]})" : "";

require "include/page_header.inc.php";
?>
<page id="erreur" titre="Frankiz : erreur <?=$erreur_num?>">
	<warning>Une erreur <?=$erreur_num?> <?=$erreur_texte?> est survenue, emp�chant
		l'acc�s � la page demand�e.</warning>
</page>
<?php require "include/page_footer.inc.php" ?>
