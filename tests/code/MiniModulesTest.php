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
class MiniModulesTest extends PHPUnit_Framework_TestCase
{
    public function getFiles()
    {
        global $globals;
        $files = glob($globals->spoolroot . '/minimodules/*.php');
         // Return an array of parameters
        $data = array();
        foreach ($files as $f) {
            $data[] = array(substr(basename($f), 0, -4));
        }
        return $data;
    }

    /**
     * @dataProvider getFiles
     */
    public function testMiniModules($mininame)
    {
        global $globals;
        // Load minimodule
        $mini = FrankizMiniModule::get($mininame, false);
        $tplPath = $globals->spoolroot . '/templates/default/' . $mini->template();
        $this->assertTrue(file_exists($tplPath), "Minimodule template does not exist");
    }
}
