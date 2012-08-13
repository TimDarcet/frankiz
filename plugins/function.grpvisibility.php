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


function smarty_function_grpvisibility($params, &$smarty) {
    $user = $params['user'];
    $group = $params['group'];
    $grpcoll = $user->groupVisibility($group);
    $visigroup = (($grpcoll->count() == 1) ? $grpcoll->first() : null);

    // if $user is session user, see which visibility option is enabled
    $flagselect = '';
    if (S::user()->isMe($user)) {
        $possib = $user->getAvailVisibilities($group);
        $flagoptions = array();
        foreach ($possib as $gid => $title) {
            $flagoption = '<option value="' . $gid . '"';
            if ($visigroup != null && $visigroup->id() == $gid)
                $flagoption .= ' selected';
            $flagoption .= '>visible par ' . $title . '</option>';
            $flagoptions[] = $flagoption;
        }
        $flagselect = '<select class="visiselect" name="visibility-' .
            $user->id() . '-' . $group->id(). '">' .
            implode($flagoptions) . '</select>';
    }

    // Get color & title
    list($color, $title) = User::visibilitiesColInfo($grpcoll);
    return '<form class="visicontainer" id="visiflag-' . $user->id() . '-' . $group->id() . '">' .
        '<div class="visiflag ' . $color . ' click" title="' . $title .'"></div>' .
        $flagselect . '</form>';
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
