<?php
/***************************************************************************
 *  XRAY - Thomas Coquet                                                   *
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

class XRayMiniModule extends FrankizMiniModule
{

    /**
     * xml2array() will convert the given XML text to an array in the XML structure.
     * Link: http://www.bin-co.com/php/scripts/xml2array/
     * Arguments : $contents - The XML text
     *                $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
     *                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
     * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
     * Examples: $array =  xml2array(file_get_contents('feed.xml'));
     *              $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
     */
    public function xml2array($contents, $get_attributes=1, $priority = 'tag') {
        if(!$contents) return array();

        if(!function_exists('xml_parser_create')) {
            //print "'xml_parser_create()' function not found!";
            return array();
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        //xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);

        if(!$xml_values) return;//Hmm...

        //Initializations
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$xml_array; //Refference

        //Go through the tags.
        $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
        foreach($xml_values as $data) {
            unset($attributes,$value);//Remove existing values, or there will be trouble

            //This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data);//We could use the array by itself, but this cooler.

            $result = array();
            $attributes_data = array();

            if(isset($value)) {
                if($priority == 'tag') $result = $value;
                else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }

            //Set the attributes too.
            if(isset($attributes) and $get_attributes) {
                foreach($attributes as $attr => $val) {
                    if($priority == 'tag') $attributes_data[$attr] = $val;
                    else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if($type == "open") {//The starting of the tag '<tag>'
                $parent[$level-1] = &$current;
                if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag.'_'.$level] = 1;

                    $current = &$current[$tag];

                } else { //There was another element with the same tag name

                    if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                        $repeated_tag_index[$tag.'_'.$level]++;
                    } else {//This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag.'_'.$level] = 2;

                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }

                    }
                    $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                    $current = &$current[$tag][$last_item_index];
                }

            } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if(!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

                } else { //If taken, put all things inside a list(array)
                    if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

                        if($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag.'_'.$level]++;

                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag.'_'.$level] = 1;
                        if($priority == 'tag' and $get_attributes) {
                            if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

                                $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                unset($current[$tag.'_attr']);
                            }

                            if($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                    }
                }

            } elseif($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level-1];
            }
        }

        return($xml_array);
    }

    public function auth()
    {
        return AUTH_INTERNAL;
    }

    public function css()
    {
        return 'minimodules/xray_static.css';
    }

    public function tpl()
    {
        return 'minimodules/xray/radio.tpl';
    }

    public function title()
    {
        return 'X-Ray';
    }

    public function run()
    {
        global $globals;

        // google calendar pour la prochaine émission
        // MAJ 1x/jour
        $gc = "http://www.google.com/calendar/feeds/binet.radio.xray%40gmail.com/public/full?alt=json&orderby=starttime&max-results=50&singleevents=true&sortorder=ascending&futureevents=true";

        // Check if cache needs to be refreshed : if emission < now or if last check was 12h ago
        if (PlCache::hasGlobal('xray_calendar')) {
            $xray_calendar = PlCache::getGlobal('xray_calendar');
            if (!isset($xray_calendar['time']) || !isset($xray_calendar['next_time'])
             || (time() > min($xray_calendar['next_time'], $xray_calendar['time'] + 43200)))
                PlCache::invalidateGlobal('xray_calendar');
        }
        if (!PlCache::hasGlobal('xray_calendar')) {
            $calendar_api = new API($gc, true);
            $json_calendar = json_decode($calendar_api->response(), true);

            // First emission
            $feed = $json_calendar['feed']['entry'];

            $next_show = "surprise !";
            // Update no-emission every minute
            $next_time = time() + 60;
            for ($i = 0; $i < count($feed); $i++) {
                $entry = $feed[$i];
                $title = $entry['title']['$t'];
                if ($title{0} == '_') {
                    $name = substr($title, 1);
                    $start = new DateTime($entry['gd$when'][0]['startTime']);
                    $next_show = $name . " à " . strftime('%Hh, %A', $start->getTimestamp());
                    $next_time = $start->getTimestamp();
                    break;
                }
            }

            $xray_calendar = array('emission' => $next_show, 'next_time' => $next_time, 'time' => time());
            PlCache::setGlobal('xray_calendar', $xray_calendar, $globals->cache->xray_calendar);
        }

        /*

        // feed x-ray pour le dernier podcast
        // MAJ 1x/jour
        $podcasts_xml = "http://x-ray/blog/?feed=podcast";

        if (!PlCache::hasGlobal('xray_podcast')) {
            $podcast_api = new API($podcasts_xml, false);
            $podcasts  = $this->xml2array($podcast_api->response());

            $last_podcast = $podcasts['rss']['channel']['item'][0];

            $xray_podcast = array('titre' => utf8_decode($last_podcast['title']), 'url' => $last_podcast['link'], 'description' => utf8_decode($last_podcast['itunes:subtitle']));
            PlCache::setGlobal('xray_podcast', $xray_podcast, $globals->cache->xray_podcast);
        }
        */

        // titre en cours
        // MAJ toutes les 2 mn
        $nowplaying_xml = "http://x-ray/cache/info.xml";

        if (!PlCache::hasGlobal('xray_nowplaying')) {
            $nowplaying_api = new API($nowplaying_xml, false);
            $nowplaying  = $this->xml2array($nowplaying_api->response());
            $song = $nowplaying['info'];

            $xray_nowplaying = array('title' => $song['title'], 'artist' => $song['artist'], 'album' => $song['album'], 'cover' => $song['cover']);
            PlCache::setGlobal('xray_nowplaying', $xray_nowplaying, $globals->cache->xray_nowplaying);
        }

        $this->assign('xray_calendar', PlCache::getGlobal('xray_calendar'));
        //$this->assign('xray_podcast', PlCache::getGlobal('xray_podcast'));
        $this->assign('xray_nowplaying', PlCache::getGlobal('xray_nowplaying'));
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
