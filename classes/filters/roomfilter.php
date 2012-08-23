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


/******************
 * CONDITIONS
 ******************/


/** This interface describe objects which filter users based
 *      on various parameters.
 * The parameters of the filter must be given to the constructor.
 * The buildCondition function is called by RoomFilter when
 *     actually building the query. That function must call
 *     $uf->addWheteverFilter so that the RoomFilter makes
 *     adequate joins. It must return the 'WHERE' condition to use
 *     with the filter.
 */
abstract class RoomFilterCondition extends FrankizFilterCondition
{
}

/** Filters rooms based on their rid
 * @param $val Either an hruid, or a list of those
 */
class RFC_Rid extends RoomFilterCondition
{
    private $rids;

    public function __construct($rs)
    {
        $this->rids = Room::toIds(unflatten($rs));
    }

    public function buildCondition(PlFilter $rf)
    {
        return XDB::format('r.rid IN {?}', $this->rids);
    }

    public function export()
    {
        return array('type' => 'rid', 'rids' => $this->rids);
    }
}

/** Filters rooms based on their IPs
 * @param $ip IP from which connection are checked
 */
class RFC_Ip extends RoomFilterCondition
{
    private $ip;

    public function __construct($ip)
    {
        $this->ip = $ip;
    }

    public function buildCondition(PlFilter $rf)
    {
        $sub = $rf->addIpFilter();
        $right = XDB::formatWildcards(XDB::WILDCARD_CONTAINS, $this->ip);
        return $sub . '.ip ' . $right;
    }
}

class RFC_Comment extends RoomFilterCondition
{
    private $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    public function buildCondition(PlFilter $rf)
    {
        return 'r.comment ' . XDB::formatWildcards(XDB::WILDCARD_CONTAINS, $this->text);
    }
}

/** Filters rooms based on group hosting
 * @param $group Group
 */
class RFC_Group extends RoomFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        if ($gs instanceof Collection) {
            $this->gids = $gs->ids();
        } else {
            $this->gids = Group::toIds(unflatten($gs));
        }
    }

    public function buildCondition(PlFilter $f)
    {
        $sub = $f->addGroupFilter();
        return XDB::format($sub . '.gid IN {?}', $this->gids);
    }

    public function export()
    {
        return array("type" => 'group', "children" => $this->gids);
    }
}

// FIXME not tested (probably wrong)
class RFC_Phone extends RoomFilterCondition
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function buildCondition(PlFilter $rf)
    {
        $right = XDB::formatWildcards(XDB::WILDCARD_CONTAINS, $this->number);
        return 'r.phone ' . $right;
    }
}

class RFC_Prefix extends RoomFilterCondition
{
    private $prefix;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    public function buildCondition(PlFilter $rf)
    {
        $right = XDB::formatWildcards(XDB::WILDCARD_PREFIX, $this->prefix);
        return 'r.rid ' . $right;
    }
}


/******************
 * ORDERS
 ******************/

/** Base class for ordering results of a query.
 * Parameters for the ordering must be given to the constructor ($desc for a
 *     descending order).
 * The getSortTokens function is used to get actual ordering part of the query.
 */
abstract class RoomFilterOrder extends FrankizFilterOrder
{
}

/***********************************
  *********************************
          ROOM FILTER CLASS
  *********************************
 ***********************************/

/** This class provides a convenient and centralized way of filtering users.
 *
 * Usage:
 * $rf = new RoomFilter(new RFC_Blah($x, $y), new RFO_Coin($z, $t));
 *
 * Resulting RoomFilter can be used to:
 * - get a list of Room objects matching the filter
 * - get a list of RIDs matching the filter
 * - get the number of rooms matching the filter
 * - check whether a given Room matches the filter
 * - filter a list of Room objects depending on whether they match the filter
 *
 * Usage for RFC and RFO objects:
 * A RoomFilter will call all private functions named XXXJoins.
 * These functions must return an array containing the list of join
 * required by the various RFC and RFO associated to the RoomFilter.
 * Entries in those returned array are of the following form:
 *   'join_tablealias' => array('join_type', 'joined_table', 'join_criter')
 * which will be translated into :
 *   join_type JOIN joined_table AS join_tablealias ON (join_criter)
 * in the final query.
 *
 * In the join_criter text, $ME is replaced with 'join_tablealias' and $RID with rooms.rid.
 *
 * For each kind of "JOIN" needed, a function named addXXXFilter() should be defined;
 * its parameter will be used to set various private vars of the RoomFilter describing
 * the required joins ; such a function shall return the "join_tablealias" to use
 * when referring to the joined table.
 *
 * For example, if data from profile_job must be available to filter results,
 * the RFC object will call $rf-addJobFilter(), which will set the 'with_pj' var and
 * return 'pj', the short name to use when referring to profile_job; when building
 * the query, calling the jobJoins function will return an array containing a single
 * row:
 *   'pj' => array('left', 'profile_job', '$ME.pid = $RID');
 *
 * The 'register_optional' function can be used to generate unique table aliases when
 * the same table has to be joined several times with different aliases.
 */
class RoomFilter extends FrankizFilter
{
    protected $joinMetas = array('$RID' => 'r.rid');

    protected function schema()
    {
        return array('table' => 'rooms',
                     'as'    => 'r',
                     'id'    => 'rid');
    }

    /** IPS
     */
    private $with_ips = 0;

    public function addIpFilter()
    {
        $this->with_ips++;
        return 'ip' . $this->with_ips;
    }

    protected function ipJoins()
    {
        $joins = array();
        if ($this->with_ips > 0) {
            for ($i = 1; $i <= $this->with_ips; $i++) {
                $joins['ip' . $i] = PlSqlJoin::inner('ips', '$ME.room = r.rid');
            }
        }

        return $joins;
    }

    /** GROUPS
     */
    private $with_groups = 0;

    public function addGroupFilter()
    {
        $this->with_groups++;
        return 'rg' . $this->with_groups;
    }

    protected function groupJoins()
    {
        $joins = array();
        if ($this->with_groups > 0) {
            for ($i = 1; $i <= $this->with_groups; $i++) {
                $joins['rg' . $i] = PlSqlJoin::inner('rooms_groups', '$ME.rid = r.rid');
            }
        }
        return $joins;
    }

    /**
     * EXPORT & IMPORT
     */
    public function export()
    {
        $export = array('type' => 'room');
        if (!empty($this->root))
            $export['condition'] =  $this->root->export();
        if (!empty($this->sort))
            $export['sort'] =  $this->sort->export();
        return $export;
    }

    public static function fromExport(array $export) {
        $condition = null;
        $sort = null;

        if (!empty($export['condition']))
            $condition = self::importCondition($export['condition']);
        if (!empty($export['sort']))
            $sort = self::importSort($export['sort']);

        return new RoomFilter($condition, $sort);
    }

    public static function importCondition($export)
    {
        $obj = null;
        switch ($export['type']) {
            case 'true':
                $obj = new PFC_True();
                break;

            case 'and':
                $obj = new PFC_And();
                break;

            case 'or':
                $obj = new PFC_Or();
                break;

            case 'not':
                $obj = new PFC_Not();
                break;

            case 'rid':
                $obj = new RFC_Rid($export['rids']);
                break;
// TODO cases to add
            case 'group':
                $obj = new RFC_Group($export['children']);
                break;
        }

        if ($obj == null)
         throw new Exception("Object ".$export['type']." doesn't exist");

        if ($obj instanceof PFC_OneChild)
            $obj->setChild(self::importCondition($export['child']));
        elseif ($obj instanceof PFC_NChildren)
            foreach ($export['children'] as $child)
                $obj->addChild(self::importCondition($child));

        return $obj;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
