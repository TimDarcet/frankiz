<?php
/***************************************************************************
* Copyright (C) 2004-2012 Binet Réseau / Xprojets *
* http://br.binets.fr/ *
* *
* This program is free software; you can redistribute it and/or modify *
* it under the terms of the GNU General Public License as published by *
* the Free Software Foundation; either version 2 of the License, or *
* (at your option) any later version. *
* *
* This program is distributed in the hope that it will be useful, *
* but WITHOUT ANY WARRANTY; without even the implied warranty of *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the *
* GNU General Public License for more details. *
* *
* You should have received a copy of the GNU General Public License *
* along with this program; if not, write to the Free Software *
* Foundation, Inc., *
* 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA *
***************************************************************************/

class XprojetsMiniModule extends FrankizMiniModule
{
    public function auth()
    {
        return AUTH_INTERNAL;
    }

    public function tpl()
    {
        return 'minimodules/xprojets/xprojets.tpl';
    }

    public function css()
    {
        return 'minimodules/xprojets.css';
    }

    public function js()
    {
        return 'minimodules/xprojets.js';
    }

    public function title()
    {
        return 'Disponibilité Xprojets';
    }

    public function run()
    {
        global $globals;
        if (empty($globals->xprojets->cle))
            throw new Exception("Problème de configuration");
        $cle = $globals->xprojets->cle;
        $timestamp = time();
        if (S::user() != null) {
            $hash = sha1(S::user()->hruid().$cle.$timestamp);
            $data = array(S::user()->hruid(), S::user()->lastname(), S::user()->firstname(), S::user()->email(), $timestamp);
        } else {
            $hash = '';
            $data = array();
        }
        $this->assign('hash', $hash);
        $this->assign('data', implode(',', $data));
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
