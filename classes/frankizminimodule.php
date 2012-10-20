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

/**
 * Base class for Frankiz MiniModules (these are the small boxes displayed on the left and right column 
 * of the website)
 */

abstract class FrankizMiniModule
{
    const COL_LEFT   = 'COL_LEFT';
    const COL_MIDDLE = 'COL_MIDDLE';
    const COL_RIGHT  = 'COL_RIGHT';
    const COL_FLOAT  = 'COL_FLOAT';

    const ERROR_TPL = 'minimodules/error.tpl';

    private $name    = null;
    private $tplVars = array();

    private $error = null;

    private static $minimodules = array();

    private function __construct($name)
    {
        $this->name = $name;
    }

    public function name()
    {
        return $this->name;
    }

    public function tplVars()
    {
        return $this->tplVars;
    }

    protected function assign($key, $value)
    {
        $this->tplVars[$key] = $value;
    }

    public function template()
    {
        if ($this->checkAuthAndPerms() && $this->error === null)
            return $this->tpl();
        else
            return self::ERROR_TPL;
    }

    // Must return the minimodule's template
    abstract protected function tpl();

    public function title()
    {
        return '';
    }

    public function auth()
    {
        return AUTH_PUBLIC;
    }

    public function perms()
    {
        return '';
    }

    public function js()
    {
        return false;
    }

    public function css()
    {
        return false;
    }

    public function run()
    {

    }

    final function checkAuthAndPerms()
    {
        return Platal::session()->checkAuthAndPerms($this->auth(), $this->perms());
    }

    private static function instantiate($name)
    {
        global $globals;

        $cls = ucfirst($name) . 'MiniModule';
        $path = $globals->spoolroot . '/minimodules/' . strtolower($name) . ".php";
        if (file_exists($path)) {
            include_once $path;
            return new $cls($name);
        } else {
            return false;
        }
    }

    public static function get($names, $run = true)
    {
        global $globals;

        $array_passed = is_array($names);
        $names = unflatten($names);
        $minimodules = array();
        foreach($names as $name) {
            $m = self::instantiate($name);
            if ($m !== false) {
                $minimodules[$m->name] = $m;
                self::$minimodules[$m->name] = $m;
                if ($m->checkAuthAndPerms() && $run) {
                    try {
                        $m->run();
                    } catch (Exception $e) {
                        $m->error = $e;
                        if ($globals->debug & DEBUG_BT) {
                            if (!isset(PlBacktrace::$bt['Minimodule']))
                                new PlBacktrace('Minimodule');
                            PlBacktrace::$bt['Minimodule']->newEvent($name, 0, $e->getMessage(),
                                array(array('file' => $e->getFile(),
                                            'line' => $e->getLine())));
                        }
                    }
                }
            } else {
                // Unable to instantiate a minimodule
                $minimodules[$name] = false;
            }
        }
        return ($array_passed) ? $minimodules : flatten($minimodules);
    }

    public static function batchJs()
    {
        $res = array();
        foreach(self::$minimodules as $m)
            $res[$m->name] = $m->js();

        return $res;
    }

    public static function batchCss()
    {
        $res = array();
        foreach(self::$minimodules as $m)
            if ($m->css())
                $res[$m->name] = $m->css();

        return $res;
    }

    public static function emptyLayout()
    {
        return array(self::COL_LEFT => array(), self::COL_MIDDLE => array(), self::COL_RIGHT => array(), self::COL_FLOAT => array());
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
