<?php
/***************************************************************************
 *  Copyright (C) 2010 Binet Réseau                                       *
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

class ActivityMiniModule extends FrankizMiniModule
{
    public function auth()
    {
        return AUTH_INTERNAL;
    }

    public function js()
    {
        return 'minimodules/activities.js';
    }

    public function css()
    {
        return 'minimodules/activity.css';
    }

    public function tpl()
    {
        return 'minimodules/activity/activity.tpl';
    }

    public function title()
    {
        return 'Activités du jour';
    }

    public function run()
    {
        $date = new FrankizDateTime();
        $date->setTime(0,0);
        $date_n = new FrankizDateTime();
        date_add($date_n, date_interval_create_from_date_string('1 day'));
        $date_n->setTime(0,0);
        $activities = new ActivityInstanceFilter(
            new PFC_AND (
                new PFC_Or (
                    new AIFC_User(S::user(), 'restricted'),
                    new AIFC_User(S::user(), 'everybody')),
                new AIFC_Period($date, $date_n)));

        $c = $activities->get();

        $c->select(ActivityInstanceSelect::all());
        $c->order('hour_begin', false);

        $this->assign('day', new FrankizDateTime());
        $this->assign('date', date("Y-m-d"));
        $this->assign('activities', $c);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
