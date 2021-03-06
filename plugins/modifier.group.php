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


function smarty_modifier_group($group, $type = 'micro') {
    global $globals;

    $str = '<a href="groups/see/' . $group->name() . '" title="' . pl_entities($group->label()) . '">';

    if ($type == 'micro' || $type == 'both') {
        $image = $group->image();
        if (!$image) {
            $image = new StaticImage($globals->images->group);
        }
        $str .= '<img src="' . $image->src('micro') . '" />';
    }
    if ($type == 'text' || $type == 'both' || $type == 'textAndNewsNumber') {
        $str .= pl_entities($group->label());
    }
    if ($type == 'textAndNewsNumber') {
        $n = $group->nb_news();
        if ($n > 0) {
            $str .= '<b> (' . $n . ')</b>';
        }
    }

    $str .= '</a>';

    if ($type == 'premises') {
        $str = '';
        $roomMasterCss = ($group->isRoomMaster() ? ' room_master' : '');
        foreach ($group->rooms() as $premise) {
            $state = ($premise->open() ? 'open' : 'close');
            $str .= '<div class="' . $state . '" rid="' . $premise->id() . '">' .
                '<div class="traffic_light_switcher' . $roomMasterCss . '" title="' . pl_entities($premise->comment()) . '">' .
                 '</div></div>' . PHP_EOL;
        }
    }
    return $str;
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
