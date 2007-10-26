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
	Code permettant de parser du code XML. La fonction xml_get_tree renvoit la traduction sous
	forme d'arbre du contenu du fichier XML.
	
	$Id$
	
*/

function xpath_evaluate_context($xpath, $str, $context)
{
	return $xpath->query($str, $context)->item(0)->nodeValue;
}

function xpath_evaluate($xpath, $str)
{
	return $xpath->query($str)->item(0)->nodeValue;
}
