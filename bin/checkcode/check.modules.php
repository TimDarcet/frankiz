#!/usr/bin/php -q
<?php
/***************************************************************************
 *  Copyright (C) 2012 Binet Réseau                                        *
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
 * This script checks the files in modules/ directory
 */
// Do not preload modules, but load Plat/al
require_once(dirname(__FILE__) . '/../../core/include/platal.inc.php');
spl_autoload_register('pl_autoload');
pl_autoload('FrankizSession');
pl_autoload('Platal');

$modulesdir = dirname(__FILE__) . '/../../modules';
$d = opendir($modulesdir);
while ($filename = readdir($d)) {
    // Ignore hidden files and directories
    if ($filename[0] == '.') {
        continue;
    }
    if (substr($filename, strlen($filename) - 4, 4) != '.php') {
        echo "Unknown file type for module $filename\n";
        continue;
    }

    // Guess names
    $name = substr($filename, 0, strlen($filename) - 4);
    $modname = $name . 'Module';

    // Load module
    include_once($modulesdir . '/' . $filename);
    $mod = new $modname;
    $handlers = $mod->handlers();
    echo "Reading module $modname...\n";

    // Get methods which are used
    $known_methods = array();
    foreach ($handlers as $url => $hook) {
        $props = array(
            'callback' => false,
            'auth'     => 0,
            'perms'    => '',
            'type'     => 0);
        $reflector = new ReflectionClass($hook);
        foreach ($reflector->getProperties() as $p) {
            if (!isset($props[$p->getName()])) {
                echo "Unknown property " . $p->getName() . " for hook $url\n";
                continue;
            }
            // Properties are potected
            $p->setAccessible(true);
            $props[$p->getName()] = $p->getValue($hook);
        }

        // Here we can display and check the values for auth, perms and type
        //echo "  Hook $url:\n";print_r($props);

        // Check callback
        $callback = $props['callback'];
        if ($callback !== false) {
            if (!is_array($callback) || count($callback) != 2) {
                echo "Invalid callback for hook $url\n";
            } elseif ($callback[0] !== $mod) {
                echo "Callback for another module for hook $url ?\n";
            } else {
                $fct = $callback[1];
                if (empty($fct) || !is_string($fct)) {
                    echo "Bad callback dor hook $url\n";
                } elseif (isset($known_methods[$fct])) {
                    echo "Callback $fct set for hooks $url and " . $known_methods[$fct] . "\n";
                } else {
                    // Register mapping
                    $known_methods[$fct] = $url;
                }
            }
        }
    }

    // Get all methods
    $mod_methods = array();
    $reflector = new ReflectionClass($modname);
    foreach($reflector->getMethods() as $m) {
        // Filter inheritd methods
        if (strcasecmp($m->class, $modname))
            continue;
        $fct = $m->name;
        // Only keep handlers
        if (substr($fct, 0, 8) == 'handler_')
            $mod_methods[] = $fct;
    }

    $undefined = array_diff(array_keys($known_methods), $mod_methods);
    $unused = array_diff($mod_methods, array_keys($known_methods));
    foreach ($undefined as $fct) {
        echo "Undefined method $modname -> $fct for " . $known_methods[$fct] . "\n";
    }
    foreach ($unused as $fct) {
        echo "Unused method $modname -> $fct\n";
    }
//    print_r($known_methods);
}
?>
