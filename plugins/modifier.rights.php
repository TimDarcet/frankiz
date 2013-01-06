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

function smarty_modifier_rights($rights, $defaults = null, $hoverable = false) {
    $rights = unflatten($rights);
    if ($defaults === null) {
        $defaults = $rights;
    }

    $keys = array();
    foreach ($rights as $right) {
        $keys[(string) $right] = true;
    }

    $strings = array();
    foreach ($defaults as $right) {
        $key = (string) $right;
        $label = $key;
        switch ($key) {
            case 'admin':
                $label = 'Administrateur';
            break;

            case 'member':
                $label = 'Membre';
            break;

            case 'friend':
                $label = 'Sympathisant';
            break;
        }
        $has = ($keys[$key]) ? 'on' : 'off';
        $hoverable = ($hoverable) ? 'hoverable' : '';
        $strings[] = '<div title="' . $label . '" class="rights ' . $key . ' '. $has . ' ' . $hoverable .'"></div>';
    }
    return implode('', $strings);
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
