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

require_once(dirname(__FILE__) . '/../../include/test.inc.php');

/**
 * Checks the files in modules/ directory
 */
class ModulesTest extends PHPUnit_Framework_TestCase
{
    public function getFiles()
    {
        $files = glob(dirname(__FILE__) . '/../../modules/*.php');
         // Return an array of parameters
        $data = array();
        foreach ($files as $f) {
            $modname = substr(basename($f), 0, -4) . 'Module';
            $data[] = array($f, $modname);
        }
        return $data;
    }

    /**
     * @dataProvider getFiles
     */
    public function testModules($filename, $modname)
    {
        // Load module
        include_once($filename);
        $mod = new $modname;

        // Get handlers which are used
        $known_handlers = array();
        $handlers = $mod->handlers();
        foreach ($handlers as $url => $hook) {
            $props = array(
                'callback' => false,
                'auth'     => 0,
                'perms'    => '',
                'type'     => 0);
            $reflector = new ReflectionClass($hook);
            foreach ($reflector->getProperties() as $p) {
                $this->assertArrayHasKey($p->getName(), $props,
                    "Unknown property " . $p->getName() . " for hook $url");
                // Properties are protected, so make them public
                $p->setAccessible(true);
                $props[$p->getName()] = $p->getValue($hook);
            }

            // Check callback
            $callback = $props['callback'];
            $this->assertNotEmpty($callback);
            $this->assertTrue(is_array($callback));
            $this->assertEquals(2, count($callback));
            $this->assertEquals($mod, $callback[0], "Callback for another module for hook $url ?");
            $fct = $callback[1];
            $this->assertNotEmpty($fct);
            $this->assertTrue(is_string($fct));
            $this->assertArrayNotHasKey($fct, $known_handlers, "Callback $fct has multiple entries");

            // Register mapping
            $known_handlers[$fct] = $url;
        }

        // Get all handlers
        $mod_handlers = array();
        $reflector = new ReflectionClass($modname);
        foreach($reflector->getMethods() as $m) {
            // Filter out inherited methods
            if (strcasecmp($m->class, $modname))
                continue;
            $fct = $m->name;
            // Only keep handlers
            if (substr($fct, 0, 8) == 'handler_')
                $mod_handlers[] = $fct;
        }

        $this->assertEquals(array(), array_diff(array_keys($known_handlers), $mod_handlers),
            "Some handlers do not exist");
        $this->assertEquals(array(), array_diff($mod_handlers, array_keys($known_handlers)),
            "Some handlers are defined but not used");
    }
}
