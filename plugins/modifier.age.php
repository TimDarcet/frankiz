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


function smarty_modifier_age($datetime, $format = 'auto') {
    $s = function($c) {
        return ($c > 1) ? 's' : '';
    };

    $age = $datetime->age();
    $inv = ($age->invert) ? '- ' : '';
    if ($format == 'auto') {
        if ($age->y > 0) {
            return $inv . $age->format('%y an' . $s($age->y));
        }
        if ($age->m > 0) {
            return $inv . $age->format('%m mois');
        }
        if ($age->d > 0) {
            return $inv . $age->format('%d jour' . $s($age->d));
        }
        if ($age->h > 0) {
            return $inv . $age->format('%h heure' . $s($age->h));
        }
        if ($age->i > 0) {
            return $inv . $age->format("%i'");
        }
        if ($age->s > 0) {
            return $inv . $age->format("%s''");
        }
    } else {
        return $age->format($format);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
