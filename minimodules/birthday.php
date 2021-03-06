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

class BirthdayMiniModule extends FrankizMiniModule
{
    public function tpl()
    {
        return 'minimodules/birthday/birthday.tpl';
    }

    public function css()
    {
        return 'minimodules/birthday.css';
    }

    public function title()
    {
        return 'Joyeux anniversaire !';
    }

    public function run()
    {
        $promoNames = array();
        $studies = S::user()->select(UserSelect::birthday())->studies();
        foreach ($studies as $s) {
            $promoNames[] = $s->groupName();
        }
        $promos = Group::batchFrom($promoNames);
        $promos->add(Group::from('on_platal'));

        $uf = new UserFilter(new PFC_And(new UFC_Birthday('=', new FrankizDateTime()),
                                         new UFC_Group($promos)));
        $us = $uf->get();
        $us->select(UserSelect::birthday());

        $formations = array();
        $users = array();
        foreach ($us as $u) {
            $study = $u->studies();
            // HOTFIX by NTag
            if (count($study) > 0) {
                $first = array_shift($study);
                $formations[$first->formation()->abbrev()] = $first->formation();
                $users[$first->formation()->abbrev()][$first->promo()][] = $u;
            }
        }

        $this->assign('formations', $formations);
        $this->assign('users', $users);
    }
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
