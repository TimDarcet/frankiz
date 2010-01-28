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

/** This function will add an attribute to a <a> tag if
 *     the current path matches the argument
 * @param $path     The path to write in the href attribute
 * @param $iftrue   The attribute to add if current path is $path
 * @param $iffalse  The attribute to add otherwise (defaults to '')
 */
function smarty_function_path_to_href_attribute($params, &$smarty) {
    $on = (isset($params['iftrue'])) ? $params['iftrue'] : 'on';
    $off = (isset($params['iffalse'])) ? $params['iffalse'] : '';
    
    if (trim($params['path'],'/') == trim(Get::v('n'),'/')) {
        $attribute = $on;
    } else {
        $attribute = $off;
    }
    $attribute = ($attribute == '') ? '' : ' '.$attribute;    
    
    return 'href="' . $params['path'] . '" class="link' . $attribute . '"';
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
