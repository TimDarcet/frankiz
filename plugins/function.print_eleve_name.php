<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
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


function smarty_function_print_eleve_name($params, &$smarty)
{
    $user = $params['eleve'];
    $name = $user->displayName();

	if (isset($params['show_promo'])) {
        $name .= " (" . $user->promo() .")";
    }

    if (S::v('auth', AUTH_PUBLIC) >= AUTH_INTERNE) {
        $name = "<a href='tol/" . $user->login() . "'>" . $name . "</a>";
    }

	return $name;
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
