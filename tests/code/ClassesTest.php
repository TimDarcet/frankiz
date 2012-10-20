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

require_once(dirname(__FILE__) . '/../../include/test.inc.php');

/**
 * Checks the files in classes/ directory
 */
class ClassesTest extends PHPUnit_Framework_TestCase
{
    public function getFiles()
    {
        global $globals;
        $files = glob($globals->spoolroot . '/classes/*.php');
         // Return an array of parameters
        $data = array();
        foreach ($files as $f) {
            $data[] = array($f, substr(basename($f), 0, -4));
        }

        $files = glob($globals->spoolroot . '/classes/*/*.php');
        foreach ($files as $f) {
            $data[] = array($f, substr(basename($f), 0, -4), basename(dirname($f)));
        }
        return $data;
    }

    /**
     * @dataProvider getFiles
     */
    public function testClassFile($filename, $basename, $namespace = '')
    {
        $filters = array('g'  => 'groupfilter',
                         'c'  => 'castefilter',
                         'n'  => 'newsfilter',
                         'u'  => 'userfilter',
                         'v'  => 'validatefilter',
                         'a'  => 'activityfilter',
                         'ai' => 'activityinstancefilter',
                         'i'  => 'imagefilter',
                         'r'  => 'roomfilter');

        // Get all classes from file
        foreach (file($filename) as $line) {
            $matches = array();
            if (!preg_match('/^\s*(abstract\s+)?class\s+([a-zA-Z0-9_]+)/', $line, $matches)) {
                continue;
            }
            $classname = strtolower($matches[2]);

            // Ignore exceptions
            if (ends_with($classname, 'exception')) {
                continue;
            }

            $this->assertTrue(class_exists($classname),
                "Class $classname can't be loaded");

            // Check that $classname is a legal name in $namespace/$basename
            switch ($namespace) {
                case 'feed':
                    $this->assertStringEndsWith('feed', $classname);
                    $this->assertEquals($classname, $basename);
                    break;
                case 'validate':
                    $this->assertStringEndsWith('validate', $classname);
                    $this->assertEquals($classname, $basename);
                    break;
                case 'filters':
                    $known_classses = array(
                        $basename,
                        $basename . 'condition',
                        $basename . 'order'
                    );
                    if (in_array($classname, $known_classses))
                        break;
                    $letter = (($basename == 'activityinstancefilter')
                        ? 'ai' : $basename[0]);
                    $this->assertRegExp('/^'.$letter.'f[co]_[a-z]+$/', $classname);
                    break;
                case '':
                    $allowed_names = array(
                        $basename,
                        $basename . 'schema',
                        $basename . 'select'
                    );
                    if (in_array($classname, $allowed_names))
                        break;
                    switch ($basename) {
                        case 'frankizfilter':
                            $this->assertStringStartsWith($basename, $classname);
                            break;
                        case 'weather':
                            if (!ends_with($classname, 'weather'))
                                $this->assertStringStartsWith('weather', $classname);
                            break;
                        case 'imageinterface':
                            $this->assertStringStartsWith('image', $classname);
                            break;
                        default:
                            $this->fail("Unknown class $classname");
                    }
                    break;
                default:
                    $this->fail("Unknown namespace $namespace");
            }
        }
    }
}
