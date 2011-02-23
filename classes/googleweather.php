<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                        *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                      *
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

class WeatherConditions
{
    public $day;

    public $temperature;
    public $humidity;

    public $low;
    public $high;

    public $label;
    public $icon;
}

abstract class Weather implements IteratorAggregate
{
    protected $today;
    protected $forecasts;

    public static function get() {
        global $globals;

        if (!PlCache::hasGlobal('meteo'))
            PlCache::setGlobal('meteo', new GoogleWeather(), $globals->cache->meteo);

        return PlCache::getGlobal('meteo');
    }

    public function today()
    {
        return $this->today;
    }

    public function forecasts()
    {
        return $this->forecasts;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->forecasts);
    }
}

class GoogleWeather extends Weather
{
    function __construct($city_code = 'Palaiseau', $lang= 'fr') {
        $prefix_images = 'https://www.google.com/';
        $url = 'http://www.google.com/ig/api?weather=' . urlencode($city_code) . '&hl=' . $lang;

        $api = new API($url);
        $xml = simplexml_load_string($api->response());

        if(isset($xml->weather->problem_cause))
            throw new Exception($xml->weather->problem_cause);

        $this->today = new WeatherConditions();
        $this->today->label = (string) $xml->weather->current_conditions->condition->attributes()->data;
        $this->today->temperature = (string) $xml->weather->current_conditions->temp_c->attributes()->data;
        $this->today->icon = $prefix_images . (string) $xml->weather->current_conditions->icon->attributes()->data;

        $this->forecasts = array();
        foreach($xml->weather->forecast_conditions as $aforecast) {
            $forecast = new WeatherConditions();
            $forecast->label = (string) $aforecast->condition->attributes()->data;
            $forecast->low   = (string) $aforecast->low->attributes()->data;
            $forecast->high  = (string) $aforecast->high->attributes()->data;
            $forecast->icon  = $prefix_images . (string) $aforecast->icon->attributes()->data;
            $forecast->day   = (string) $aforecast->day_of_week->attributes()->data;
            $this->forecasts[] = $forecast;
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
