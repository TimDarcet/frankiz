<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
 *  http://br.binets.fr/                                                   *
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

function smarty_function_select_domains($params, &$smarty) {
    $userdomain = User::getDomainFromCookie();

    $res = XDB::iterRow("SELECT  f.domain
                           FROM  formations AS f
                      LEFT JOIN  studies AS s ON s.formation_id = f.formation_id
                       GROUP BY  f.domain
                       ORDER BY  COUNT(f.domain) DESC");
    $sel = ' selected="selected"';
    $html = "";
    while (list($domain) = $res->next()) {
        $isSelected = ($userdomain == $domain ? $sel : "");
        $html .= '<option value="' . $domain .'"' . $isSelected . '>' . $domain . '</option>' . "\n";
    }

    return $html;
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
