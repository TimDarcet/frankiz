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


function smarty_modifier_group($group, $type = 'micro') {
    global $globals;

    $str = '<a href="groups/see/' . $group->name() . '" title="' . $group->label() . '">';

    if ($type == 'micro' || $type == 'both') {
        $image = $group->image();
        if (!$image) {
            $image = new StaticImage($globals->images->group);
        }
        $str .= '<img src="' . $image->src('micro') . '" />';
    }
    if ($type == 'text' || $type == 'both' || $type == 'textAndNewsNumber') {
        $str .= $group->label();
    }
    if ($type == 'textAndNewsNumber') {
        $news = new NewsFilter(
            new PFC_And(
                new PFC_Not(new NFC_Read(S::user())),
                new NFC_Current(),
                new NFC_Target(S::user()->targetCastes()),
                new NFC_Origin($group->id())
            )
        );
        $n = $news->get()->count();
        if ($n > 0) {
            $str .= '<b> (' . $n . ')</b>';
        }
    }

    $str .= '</a>';

    if ($type == 'premises') {
        $str = '';
        $roomMaster = S::user()->hasRights($group, Rights::admin()) || S::user()->isWeb() || in_array(IP::get(), $group->ips());
        foreach($group->premises() as $key=>$premise) {
            $str .= '<div class="' . ($premise['open'] ? 'open' : 'close') . '" rid="' . $key . '">
                            <div class="traffic_light_switcher' . ($roomMaster ? ' room_master' : '') . '" title="' . $premise['label'] . '"></div>
                     </div>
                     ';
        }
    }

    return $str;
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
