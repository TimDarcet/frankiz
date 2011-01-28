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

class AnniversairesMiniModule extends FrankizMiniModule
{
    public function tpl()
    {
        return 'minimodules/anniversaires/anniversaires.tpl';
    }

    public function title()
    {
        return 'Joyeux anniversaire!';
    }

    public function run()
    {
        $uf = new UserFilter(new PFC_And(new UFC_Birthday('=', new FrankizDateTime()),
                                            new UFC_Group(Group::from('on_platal'), Rights::member())));
        $us = $uf->get()->select(User::SELECT_BASE);

        $this->assign('users', $us);
    }
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
