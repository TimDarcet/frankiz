<?php
/***************************************************************************
 *  Copyright (C) 2003-2008 Polytechnique.org                              *
 *  http://opensource.polytechnique.org/                                   *
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

class PlatalGlobals
{
    public $session;

    /** The x.org version */
    public $version = '0.9.17beta';
    public $debug   = 7;
    public $mode    = 'ro';    // 'rw' => read/write,
                               // 'r'  => read/only
                               // ''   => site down

    /** db params */
    public $dbdb               = 'x4dat';
    public $dbhost             = 'localhost';
    public $dbuser             = 'x4dat';
    public $dbpwd              = 'x4dat';
    public $dbcharset          = 'utf8';

    /** default skin */
    public $skin;
    public $register_skin;

    /** paths */
    public $baseurl;
    public $baseurl_http;
    public $spoolroot;

    public $locale;
    public $timezone;

    public function __construct($sess)
    {
        $this->session   = $sess;
        $this->spoolroot = dirname(dirname(__FILE__));

        $this->read_config();
        if (isset($_SERVER) && isset($_SERVER['SERVER_NAME'])) {
            $base = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
            $this->baseurl      = @trim($base    .$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']), '/');
            $this->baseurl_http = @trim('http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']), '/');
        }

        $this->setlocale();
    }

    private function read_ini_file($filename)
    {
        $array = parse_ini_file($filename, true);
        if (!is_array($array)) {
            return;
        }
        foreach ($array as $cat => $conf) {
            $c = strtolower($cat);
            foreach ($conf as $k => $v) {
                if ($c == 'core' && property_exists($this, $k)) {
                    $this->$k=$v;
                } else {
                    if (!isset($this->$c)) {
                        $this->$c = new stdClass;
                    }
                    $this->$c->$k = $v;
                }
            }
        }
    }

    private function read_config()
    {
        $this->read_ini_file($this->spoolroot.'/configs/platal.ini');
        $this->read_ini_file($this->spoolroot.'/configs/platal.conf');
        if (file_exists($this->spoolroot.'/spool/conf/platal.dynamic.conf')) {
            $this->read_ini_file($this->spoolroot.'/spool/conf/platal.dynamic.conf');
        }
    }

    /** Writes an ini file separated in categories
     * @param filename the name of the file to write (overwrite existing)
     * @param categories an array of categories (array of keys and values)
     */              
    private static function write_ini_file($filename, &$categories)
    {
        // [category]
        // key = value
        $f = fopen($filename, 'w');
        foreach ($categories as $cat => $conf) {
            fwrite($f, '; {{{ '.$cat."\n\n");
            fwrite($f, '['.$cat.']'."\n\n");
            foreach ($conf as $k => $v) {
                fwrite($f, $k.' = "'.str_replace('"','\\"',$v).'"'."\n");
            }
            fwrite($f, "\n".'; }}}'."\n");
        }
        fwrite($f, '; vim:set syntax=dosini foldmethod=marker:'."\n");
        fclose($f);
    }

    /** Change dynamic config file
     * @param conf array of keys and values to add or replace
     * @param category name of category to change
     * 
     * Opens the dynamic conf file and set values from conf in specified
     * category. Updates config vars too.
     */ 
    public function change_dynamic_config($conf, $category = 'Core')
    {
        $dynamicfile = $this->spoolroot.'/spool/conf/platal.dynamic.conf';
        if (file_exists($dynamicfile)) {
            $array = parse_ini_file($dynamicfile, true);
        } else {
            $array = null;
        }
        if (!is_array($array)) {
            // dynamic conf is empty
            $array = array($category => $conf);
        } else {
            // looks for a category that looks the same (case insensitive)
            $same = false;
            foreach ($array as $m => &$c) {
                if (strtolower($m) == strtolower($category)) {
                    $same = $m;
                    break;
                }
            }
            if (!$same) {
                // this category doesn't exist yet
                $array[$category] = $conf;
            } else {
                // this category already exists
                $conflower = array();
                foreach ($conf as $k => $v) {
                    $conflower[strtolower($k)] = $v;
                }
                // $conflower is now same as $conf but with lower case keys
                // replaces values of keys that already exists
                foreach ($array[$same] as $k => $v) {
                    if (isset($conflower[strtolower($k)])) {
                        $array[$same][$k] = $conflower[strtolower($k)];
                        unset($conflower[strtolower($k)]);
                    }
                }
                // add new keys
                foreach ($conf as $k => $v) {
                    if (isset($conflower[strtolower($k)])) {
                        $array[$same][$k] = $v;
                    }
                } 
            }
        }
        // writes the file over
        PlatalGlobals::write_ini_file($dynamicfile, $array);
        // rereads the new config to correctly set vars
        $this->read_ini_file($dynamicfile);
    }

    public function bootstrap($conf, $callback, $category = 'Core')
    {
        $bootstrap = false;
        $category = strtolower($category);
        foreach ($conf as $key) {
            if (!isset($this->$category->$key)) {
                $bootstrap = true;
                break;
            }
        }
        if ($bootstrap) {
            call_user_func($callback);
        }
    }

    private function setlocale()
    {
        setlocale(LC_MESSAGES, $this->locale);
        setlocale(LC_TIME,     $this->locale);
        setlocale(LC_CTYPE,    $this->locale);
        date_default_timezone_set($this->timezone);
        mb_internal_encoding("UTF-8");
    }
/*
    public function asso($key=null)
    {
        static $aid = null;

        if (is_null($aid)) {
            $gp = Get::v('n');
            if ($p = strpos($gp, '/')) {
                $gp = substr($gp, 0, $p);
            }

            if ($gp) {
                $res = XDB::query('SELECT  a.*, d.nom AS domnom, FIND_IN_SET(\'wiki_desc\', a.flags) AS wiki_desc
                                     FROM  groupex.asso AS a
                                LEFT JOIN  groupex.dom  AS d ON d.id = a.dom
                                    WHERE  diminutif = {?}', $gp);
                if (!($aid = $res->fetchOneAssoc())) {
                    $aid = array();
                }
            } else {
                $aid = array();
            }
        }
        if (empty($key)) {
            return $aid;
        } elseif ( isset($aid[$key]) ) {
            return $aid[$key];
        } else {
            return null;
        }
    }*/
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
