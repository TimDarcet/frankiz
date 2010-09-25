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
        $today= date('Y-m-d');
        $res = XDB::query("SELECT  a.firstname, a.lastname, s.promo
                             FROM  account AS a
                       INNER JOIN  studies AS s ON  a.uid=s.uid
                            WHERE  a.next_birthday=CURDATE() AND a.on_platal=1
                                   AND s.formation_id=1
                         ORDER BY  s.promo");
        $raw_annivs = $res->fetchAllAssoc();
        $anniversaires = array();
        foreach ($raw_annivs as $anniv) {
            $promo = $anniv['promo'];
            if (!array_key_exists($promo, $anniversaires)) {
                $anniversaires[$promo] = array();
            }
            $anniversaires[$promo][] = $anniv;
        }
        $this->assign('anniversaires', $anniversaires);
    }
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
