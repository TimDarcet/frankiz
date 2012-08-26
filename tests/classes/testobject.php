<<?php
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

class TestObjectSchema extends Schema
{
    public function className()
    {
        return 'TestObject';
    }

    public function id()
    {
        return 'testid';
    }

    public function table()
    {
        return 'test';
    }

    public function tableAs()
    {
        return 'testAlias';
    }

    public function scalars()
    {
        return array('key', 'value');
    }
}

class TestObject extends Meta
{
    protected $key;
    protected $value;

    /**
     * Give a default key value, to be used in tests
     */
    private function default_key()
    {
        switch ($this->id()) {
            case 1:
                return 'one';
            case 2:
                return 'two';
            case 42:
                return 'question';
        }
        return $this->id();
    }

    public function key($k = null) {
        if (!is_null($k)) {
            $this->key = $k;
        }
        if (is_null($this->key)) {
            $this->key = $this->default_key();
        }
        return $this->key;
    }
}
