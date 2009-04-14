<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                       *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                     *
 *                                                                         *
 *  This program is free software; you can redistribute it and/or modify   *
 *  it under the terms of the GNU General Public License as published by   *
 *  the Free Software Foundation; either version 2 of the License, or      *
 *  (at your option) any later version.                                    *
 *                                                                         *
 *  This program is distributed in the hope that it will be useful,        *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 *  GNU General Public License for more details.                           *
 *                                                                         *
 *  You should have received a copy of the GNU General Public License      *
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/


function smarty_function_print_dir_aux($dirpath)
{
	$dir = opendir($dirpath);

	while (($fich = readdir($dir)) !== FALSE)
	{
		if ($fich == "." || $fich == "..")
			continue;

		$pathfich = "$dirpath/$fich";

		if (is_dir($pathfich))
		{
			echo "<li class='noeud_ouvert'>$fich</li>\n";
			echo "<ul>\n";
			smarty_function_print_dir_aux($pathfich);
			echo "</ul>\n";
		}
		else
			echo "<li class='feuille'>$fich</li>\n";
	}
}

function smarty_function_print_dir($params, &$smarty)
{
	echo "<ul>\n";
	if (isset($params['dir']))
		smarty_function_print_dir_aux($params['dir']);
	echo "</ul>\n";
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
