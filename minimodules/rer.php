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

class RerMiniModule extends FrankizMiniModule
{
    public function auth()
    {
        return AUTH_INTERNAL;
    }

    public function perms()
    {
        return 'user';
    }

    public function tpl()
    {
        return 'minimodules/rer/rer.tpl';
    }
    
    public function css()
    {
        return 'minimodules/rer.css';
    }
    
    public function title()
    {
        return 'Prochains RER';
    }

    public function run()
    {
        if(!$trains = RerLoader::get())
            $trains = array();
        $this->assign('trains', $trains);
        $this->assign('currentTime', date("H:i"));
    }

    public function ajaxRefresh()
    {
        return 'innerHTML';
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
