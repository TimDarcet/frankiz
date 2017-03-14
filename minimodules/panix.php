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

class PanixMiniModule extends FrankizMiniModule
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
        return 'minimodules/panix/panix.tpl';
    }

    public function css()
    {
        return 'minimodules/panix.css';
    }

    public function title()
    {
        return 'Statut des serveurs du BR';
    }

    public function run()
    {
        $content = PanixLoader::get();
	$this->assign('content', $content);
    }

    public function ajaxRefresh()
    {
        return 'innerHTML';
    }
}

