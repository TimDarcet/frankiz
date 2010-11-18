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

function __autoload($cls)
{
    if (!pl_autoload($cls)) {
        $cls = strtolower($cls);

        $filters = array('p' => 'plfilter',
                         'g' => 'groupfilter',
                         'n' => 'newsfilter',
                         'u' => 'userfilter',
                         'v' => 'validatefilter',
                         'a' => 'activityfilter');

        foreach ($filters as $key => $class)
        {
            if (substr($cls, 0, 4) == $key . 'fc_' || substr($cls, 0, 4) == $key . 'fo_' || substr($cls, 0, strlen($class)) == $class) {
                __autoload($class);
                return;
            }
        }

        if (substr($cls, -8) == 'validate' && $cls != 'validate') {
            __autoload('itemvalidate');
            return;
        }

        include "$cls.inc.php";
    }
}

function format_phone_number($tel)
{
    $tel = trim($tel);

    if (substr($tel, 0, 3) === '(0)')
        $tel = '33' . $tel;

    $tel = preg_replace('/\(0\)/',  '', $tel);
    $tel = preg_replace('/[^0-9]/', '', $tel);
    if (substr($tel, 0, 2) === '00') {
        $tel = substr($tel, 2);
    } else if(substr($tel, 0, 1) === '0') {
        $tel = '33' . substr($tel, 1);
    }
    return $tel;
}

function flatten($var)
{
    if (is_array($var) && count($var) <= 1)
        return array_pop($var);
    else
        return $var;
}

function unflatten($var)
{
    if (!is_array($var))
        return array($var);
    else
        return $var;
}

function isId($mixed)
{
    return !is_object($mixed) && (intval($mixed).'' == $mixed);
}

function trace($mixed)
{
    if (!isset(PlBacktrace::$bt['Trace']))
        new PlBacktrace('Trace');

    ob_start();
    var_dump($mixed);
    $dump = ob_get_clean();

    foreach (debug_backtrace() as $i => $trace) {
        $file     = isset($trace["file"])     ? $trace["file"]     : "null";
        $line     = isset($trace["line"])     ? $trace["line"]     : "null";
        $class    = isset($trace["class"])    ? $trace["class"]    :  null;
        $function = isset($trace["function"]) ? $trace["function"] : "null";
        $type     = isset($trace["type"])     ? $trace["type"]     :  null;
        $args     = isset($trace["args"])     ?
                        implode(", ", array_map(
                            function($arg)
                            {
                                if (is_int($arg) || is_double($arg))
                                    return $arg;

                                if (is_string($arg)) {
                                    if (mb_strlen($arg) > 15) {
                                        return '"' . mb_substr($arg, 0, 15) . '"[..]';
                                    }
                                    return '"' . $arg . '"';
                                }

                                if (is_bool($arg))
                                    return $arg ? "true" : "false";

                                if (is_array($arg))
                                    return "array(" . count($arg) . ")";

                                if (is_object($arg))
                                    return get_class($arg);

                                return gettype($arg);
                            }, $trace["args"])) : null;

        $output[] = sprintf("[%2s] %s:%s\n     %s%s%s(%s)",
                    $i, $file, $line, $class, $type, $function, $args);
    }
    $output = array_slice($output, 1);

    PlBacktrace::$bt['Trace']->newEvent($dump, 0, implode("\n\n", $output));
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
