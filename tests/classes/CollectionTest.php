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
require_once(dirname(__FILE__) . '/testobject.php');

/**
 * Test of Collection class
 */
class CollectionTest extends PHPUnit_Framework_TestCase
{

    public function testNew()
    {
        $c = new Collection('TestObject');
        $this->assertEquals('TestObject', $c->classname());
        $this->assertEquals(0, $c->count());
    }

    public function testAdd()
    {
        $c = new Collection('TestObject');
        $this->assertEquals(0, $c->count());
        $c2 = $c->add(new TestObject(1));
        $this->assertEquals(1, $c->count());
        $this->assertEquals($c, $c2);
        $c2 = $c->add(2);
        $this->assertEquals(2, $c->count());
        $this->assertEquals($c, $c2);
        $c2 = $c->add(1);
        $this->assertEquals(2, $c->count());
        $this->assertEquals($c, $c2);

        $c = new Collection('TestObject');
        $c->add(array(new TestObject(1), 2));
        $this->assertEquals(2, $c->count());
        return $c;
    }

    public function testAddget()
    {
        $c = new Collection('TestObject');
        $this->assertEquals(0, $c->count());
        $obj1 = new TestObject(42);
        $obj2 = new TestObject(42);
        $this->assertTrue($obj1 !== $obj2);

        $c->add($obj1);
        $this->assertEquals(1, $c->count());

        $obj3 = $c->addget($obj2);
        $this->assertEquals(1, $c->count());
        $this->assertTrue($obj1 === $obj3);
        $this->assertTrue($obj2 !== $obj3);

        $obj3 = $c->addget(42);
        $this->assertEquals(1, $c->count());
        $this->assertTrue($obj1 === $obj3);
        $this->assertTrue($obj2 !== $obj3);
    }

    /**
     * @depends testAdd
     */
    public function testExport(Collection $c)
    {
        $expected = array(
            array('id' => 1),
            array('id' => 2)
        );
        $this->assertEquals($expected, $c->export());

        $expected = array(
            1 => array('id' => 1),
            2 => array('id' => 2)
        );
        $this->assertEquals($expected, $c->export(null, true));
    }

    /**
     * @depends testAdd
     */
    public function testGet(Collection $c)
    {
        $obj = $c->get(1);
        $this->assertEquals(1, $obj->id());
        $obj = $c->get(new TestObject(2));
        $this->assertEquals(2, $obj->id());
        $this->assertFalse($c->get(3));
        return $c;
    }

    /**
     * @depends testAdd
     */
    public function testHas(Collection $c)
    {
        $this->assertTrue($c->has(1));
        $this->assertTrue($c->has(new TestObject(2)));
        $this->assertFalse($c->has(3));
    }

    /**
     * @depends testAdd
     */
    public function testIds(Collection $c)
    {
        $ids = $c->ids();
        sort($ids);
        $this->assertEquals(array(1, 2), $ids);
    }

    /**
     * @depends testGet
     */
    public function testToArray(Collection $c)
    {
        $obj1 = $c->get(1);
        $obj2 = $c->get(2);
        $expected = array(
            1 => $obj1,
            2 => $obj2
        );
        $this->assertEquals($expected, $c->toArray());

        $expected = array(
            'one' => $obj1,
            'two' => $obj2
        );
        $this->assertEquals($expected, $c->toArray('key'));
    }

    /**
     * @depends testHas
     */
    public function testFromArray()
    {
        $c = Collection::fromArray(array(1, 2, new TestObject(42)), 'TestObject');
        $this->assertEquals(3, $c->count());
        $this->assertTrue($c->has(1));
        $this->assertTrue($c->has(2));
        $this->assertTrue($c->has(42));
        $this->assertFalse($c->has(3));
    }

    /**
     * @depends testFromArray
     * @depends testToArray
     */
    public function testPop()
    {
        $c = Collection::fromArray(array(1, 2, 42), 'TestObject');
        $this->assertEquals(3, $c->count());
        $obj = $c->pop();
        $this->assertEquals(new TestObject(1), $obj);
        $expected = array(
            2 => new TestObject(2),
            42 => new TestObject(42)
        );
        $this->assertEquals($expected, $c->toArray());
    }

    /**
     * @depends testFromArray
     * @depends testToArray
     */
    public function testRemove()
    {
        $c = Collection::fromArray(array(1, 2, 42), 'TestObject');
        $this->assertEquals(3, $c->count());
        $c2 = $c->remove(array(1, 2));
        $this->assertEquals($c, $c2);
        $expected = array(
            42 => new TestObject(42)
        );
        $this->assertEquals($expected, $c->toArray());
    }

    /**
     * @depends testFromArray
     * @depends testToArray
     */
    public function testMerge()
    {
        $obj1 = new TestObject(1);
        $obj2 = new TestObject(2);
        $obj2bis = new TestObject(2);
        $obj42 = new TestObject(42);
        $this->assertTrue($obj2 !== $obj2bis);

        $c1 = Collection::fromArray(array($obj1, $obj2), 'TestObject');
        $c2 = Collection::fromArray(array($obj2bis, $obj42), 'TestObject');
        $this->assertEquals(2, $c1->count());
        $this->assertEquals(2, $c2->count());

        $c1->merge($c2);
        $expected = array(
            1 => $obj1,
            2 => $obj2bis,
            42 => $obj42
        );
        $this->assertEquals($expected, $c1->toArray());
        $this->assertTrue($obj2bis === $c1->get(2));
        $expected = array(
            2 => $obj2bis,
            42 => $obj42
        );
        $this->assertEquals($expected, $c2->toArray());
        $this->assertTrue($obj2bis === $c2->get(2));
    }

    /**
     * @depends testFromArray
     * @depends testToArray
     */
    public function testSafeMerge()
    {
        $obj1 = new TestObject(1);
        $obj2 = new TestObject(2);
        $obj2bis = new TestObject(2);
        $obj42 = new TestObject(42);
        $this->assertTrue($obj2 !== $obj2bis);

        $c1 = Collection::fromArray(array($obj1, $obj2), 'TestObject');
        $c2 = Collection::fromArray(array($obj2bis, $obj42), 'TestObject');
        $this->assertEquals(2, $c1->count());
        $this->assertEquals(2, $c2->count());

        $c1->safeMerge($c2);
        $expected = array(
            1 => $obj1,
            2 => $obj2,
            42 => $obj42
        );
        $this->assertEquals($expected, $c1->toArray());
        $this->assertTrue($obj2 === $c1->get(2));
        $expected = array(
            2 => $obj2,
            42 => $obj42
        );
        $this->assertEquals($expected, $c2->toArray());
        $this->assertTrue($obj2 === $c2->get(2));
    }
}
