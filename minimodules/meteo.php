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

// Since 26 Aug 2012, Google Weather no longer serves a free API
// Since 30 Aug 2012, Frankiz uses Yahoo Weather API
define('WEATHER_DOWN', false);

class MeteoMiniModule extends FrankizMiniModule
{
    private $meteo;

    public function css()
    {
        return 'minimodules/meteo.css';
    }

    public function tpl()
    {
        return 'minimodules/meteo/' . (WEATHER_DOWN ? 'down.tpl' : 'meteo.tpl');
    }

    public function title()
    {
        return 'Météo platal';
    }

    public function run()
    {
        if (WEATHER_DOWN) {
            // Pick a random message
            $messages = array(
                "On est sur le plâtal, donc il pleut toute la semaine.",
                "Après la pluie le beau temps, enfin sauf sur le plâtal : après la pluie le brouillard",
                "Sors le nez de ton ordi et vois par toi-même, espèce de geek !",
                "Les nuit sont belles actuellement. Il est temps de sortir dormir à la belle étoile !",
                "Jusqu'à nouvel ordre, il pleut sur le plâtal",
                "Aujourd'hui : même temps qu'hier",
                "Soleil à Saclay mais orage à Palaiseau",
                "Le niveau de radioactivité est bas aujourd'hui. La fin de la pluie pour la semaine prochaine ?",
                "Le module météo est maintenant à la charge du binet Rue",
            );
            $this->assign("message", $messages[rand(0, count($messages)-1)]);
            return;
        }
        $meteo = YahooWeather::get();
        $this->assign("meteo", $meteo);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
