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

/**
 * Weather conditions of a day
 */
class WeatherConditions
{
    // Day of the week name
    public $day = null;

    // Day temperatures, in Celsius degrees
    public $temperature = null;
    public $low = null;
    public $high = null;

    // Weather description (text and image URL)
    public $label = null;
    public $icon = null;
}

/**
 * Weather forecats
 */
abstract class Weather implements IteratorAggregate
{
    protected $today = null;
    protected $forecasts = null;

    abstract public function __construct();

    /**
     * Retrieve a weather instance from cache or from an API
     * @return Weather instance
     */
    public static function get()
    {
        global $globals;
        if (!PlCache::hasGlobal('meteo')) {
            $classname = get_called_class();
            PlCache::setGlobal('meteo', new $classname(), $globals->cache->meteo);
        }
        return PlCache::getGlobal('meteo');
    }

    /**
     * @return WeatherConditions today's weather
     */
    public function today()
    {
        return $this->today;
    }

    /**
     * @return array of WeatherConditions forecasts
     */
    public function forecasts()
    {
        return $this->forecasts;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->forecasts);
    }
}

/**
 * Retrieve weather data from Google API
 */
class GoogleWeather extends Weather
{
    public function __construct($city_code = 'Palaiseau', $lang= 'fr') {
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

/**
 * Retrieve weather data from Yahoo API
 *
 * Documentation: http://developer.yahoo.com/weather/
 */
class YahooWeather extends Weather
{
    const IMAGE_PREFIX = 'http://l.yimg.com/a/i/us/we/52/';

    private static $days_en2fr = array(
        'Mon' => 'Lun',
        'Tue' => 'Mar',
        'Wed' => 'Mer',
        'Thu' => 'Jeu',
        'Fri' => 'Ven',
        'Sat' => 'Sam',
        'Sun' => 'Dim'
    );

    private static $labels = array (
        0 => 'tornade',
        1 => 'tempête tropicale',
        2 => 'ouragan',
        3 => 'orages violents',
        4 => 'orages',
        5 => 'pluie et neige',
        6 => 'pluie et neige fondue',
        7 => 'mêlée de neige et de grésil',
        8 => 'bruine verglaçante',
        9 => 'bruine',
        10 => 'pluie verglaçante',
        11 => 'douches',
        12 => 'douches',
        13 => 'averses de neige',
        14 => 'légères averses de neige',
        15 => 'neige en poudre',
        16 => 'neige',
        17 => 'grêle',
        18 => 'neige fondue',
        19 => 'poussière',
        20 => 'brumeux',
        21 => 'brume',
        22 => 'enfumé',
        23 => 'tempête',
        24 => 'venteux',
        25 => 'froid',
        26 => 'nuageux',
        27 => 'la plupart du temps nuageux (nuit)',
        28 => 'la plupart du temps nuageux (jour)',
        29 => 'partiellement nuageux (nuit)',
        30 => 'partiellement nuageux (jour)',
        31 => 'dégagé (nuit)',
        32 => 'ensoleillé',
        33 => 'beau (nuit)',
        34 => 'beau (jour)',
        35 => 'pluie et grêle',
        36 => 'chaud',
        37 => 'orages isolés',
        38 => 'orages dispersés',
        39 => 'orages dispersés',
        40 => 'averses intermittentes',
        41 => 'fortes chutes de neige',
        42 => 'averses de neige éparses',
        43 => 'fortes chutes de neige',
        44 => 'partiellement nuageux',
        45 => 'orages',
        46 => 'averses de neige',
        47 => 'orages isolés'
    );

    public function __construct($woeid = 615525, $unit = 'c') {
        $url = 'http://weather.yahooapis.com/forecastrss?w=' . ((integer)$woeid) . '&u=' . $unit;

        $api = new API($url);
        $xml = simplexml_load_string($api->response());
        if ($xml === false)
            throw new Exception("Unable to read Yahoo Weather data");

        $nss = $xml->getNamespaces(true);
        if (empty($nss['yweather']))
            throw new Exception("No namespace in Yahoo Weather data");
        $yweatherns = $nss['yweather'];

        if (!isset($xml->channel->item))
            throw new Exception("No channel in Yahoo Weather data");

        $item = $xml->channel->item->children($yweatherns);
        if (isset($item->condition)) {
            $this->today = self::yahooConditions($item->condition->attributes());
        }
        $this->forecasts = array();
        if (isset($item->forecast)) {
            foreach($item->forecast as $aforecast) {
                $this->forecasts[] = self::yahooConditions($aforecast->attributes());
            }
        }
    }

    private static function yahooConditions(SimpleXMLElement $attributes) {
        $conditions = new WeatherConditions();
        $text = null;
        foreach ($attributes as $key => $value) {
            $value = (string)$value;
            switch (strtolower($key)) {
                case 'temp':
                    $conditions->temperature = $value;
                    break;
                case 'low':
                    $conditions->low = $value;
                    break;
                case 'high':
                    $conditions->high = $value;
                    break;
                case 'day':
                    if (array_key_exists($value, self::$days_en2fr)) {
                        $conditions->day = self::$days_en2fr[$value];
                    } else {
                        $conditions->day = $value . ' (?)';
                    }
                    break;
                case 'code':
                    $conditions->icon = self::IMAGE_PREFIX . '/' . $value . '.gif';
                    if (array_key_exists($value, self::$labels)) {
                        $conditions->label = ucfirst(self::$labels[$value]);
                    }
                    break;
                case 'text':
                    $text = $value;
                    break;
                case 'date':
                    break;
                default:
                    echo "$key = $value\n";
            }
        }
        if (is_null($conditions->label)) {
            $conditions->label = $text;
        }
        return $conditions;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
