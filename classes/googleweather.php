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
        if ($xml === false)
            throw new Exception("Unable to read Google Weather data");

        if(isset($xml->weather->problem_cause))
            throw new Exception($xml->weather->problem_cause);

        if (isset($xml->weather->current_conditions)) {
            $current_conditions = $xml->weather->current_conditions;
            $this->today = new WeatherConditions();
            $this->today->label = (isset($current_conditions->condition) ? $this->getAttrData($current_conditions->condition) : null);
            $this->today->temperature = (isset($current_conditions->temp_c) ? $this->getAttrData($current_conditions->temp_c) : null);
            $icon = (isset($current_conditions->icon) ? $this->getAttrData($current_conditions->icon) : null);
            $this->today->icon = ($icon ? $prefix_images . $icon : null);
        } else {
            $this->today = null;
        }

        $this->forecasts = array();
        foreach($xml->weather->forecast_conditions as $aforecast) {
            $forecast = new WeatherConditions();
            $forecast->label = (isset($aforecast->condition) ? $this->getAttrData($aforecast->condition) : null);
            $forecast->low   = (isset($aforecast->low) ? $this->getAttrData($aforecast->low) : null);
            $forecast->high  = (isset($aforecast->high) ? $this->getAttrData($aforecast->high) : null);
            $icon = (isset($current_conditions->icon) ? $this->getAttrData($current_conditions->icon) : null);
            $forecast->icon  = ($icon ? $prefix_images . $icon : null);
            $forecast->day   = (isset($aforecast->day_of_week) ? $this->getAttrData($aforecast->day_of_week) : null);
            $this->forecasts[] = $forecast;
        }
    }

    private function getAttrData($xmlNode) {
        if (!$xmlNode)
            return null;
        $attributes = $xmlNode->attributes();
        if (!isset($attributes->data))
            return null;
        return (string) $attributes->data;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
