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

abstract class NewsFilterCondition extends FrankizFilterCondition
{
}

class NFC_Id extends NewsFilterCondition
{
    private $ids;

    public function __construct($ns)
    {
        $this->ids = News::toIds(unflatten($ns));
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('n.id IN {?}', $this->ids);
    }
}

/** Filters news based on their writer
 * @param $us A User, a Uid or an array of it
 */
class NFC_Writer extends NewsFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('n.writer IN {?}', $this->uids);
    }
}

/** Filters news based on their origin group
 * @param $gs A Group, a Gid or an array of it
 */
class NFC_Origin extends NewsFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('n.origin IN {?}', $this->gids);
    }
}

/** Filters news if they have been read by the user
 * @param $user User
 */
class NFC_Read extends NewsFilterCondition
{
    private $uid;

    public function __construct($user)
    {
        $this->uid = $user->id();
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('EXISTS(SELECT * FROM news_read WHERE news = n.id AND uid = {?})', $this->uid);
    }
}

/** Filters news if they have been starred by the user
 * @param $user User
 */
class NFC_Star extends NewsFilterCondition
{
    private $uid;

    public function __construct($user)
    {
        $this->uid = $user->id();
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('EXISTS(SELECT * FROM news_star WHERE news = n.id AND uid = {?})', $this->uid);
    }
}

/** Retrieves instances where the target is owned by the specified groups
 * @param $gs Groups
 */
class NFC_TargetGroup extends NewsFilterCondition
{
    private $cids;

    public function __construct($groups)
    {
        $cf = new CasteFilter(new PFC_And(new CFC_Holder(), new CFC_Group($groups)));
        $this->cids = $cf->get()->ids();
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format("n.target IN {?}", $this->cids);
    }
}

/** Filters news based on their target caste
 * @param $gs A Caste, a Cid or an array of it
 */
class NFC_Target extends NewsFilterCondition
{
    private $cids;

    public function __construct($cs)
    {
        if ($cs instanceof Collection) {
            $this->cids = $cs->ids();
        } else {
            $this->cids = Caste::toIds(unflatten($cs));
        }
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('n.target IN {?}', $this->cids);
    }
}

class NFC_Title extends NewsFilterCondition
{
    // Modes
    const PREFIX   = XDB::WILDCARD_PREFIX;   // 0x001
    const SUFFIX   = XDB::WILDCARD_SUFFIX;   // 0x002
    const CONTAINS = XDB::WILDCARD_CONTAINS; // 0x003

    private $text;
    private $mode;

    public function __construct($text, $mode)
    {
        $this->text = $text;
        $this->mode = $mode;
    }

    public function buildCondition(PlFilter $uf)
    {
        $right = XDB::formatWildcards($this->mode, $this->text);

        return 'n.title' . $right;
    }
}

/** Returns news that begin before (or after) a specified datetime
 */
class NFC_Begin extends NewsFilterCondition
{
    const AFTER  = '>=';
    const BEFORE = '<=';
    const EGAL   = '=';

    private $sign;
    private $begin;

    public function __construct(FrankizDateTime $begin, $sign = self::AFTER)
    {
        $this->begin  = $begin;
        $this->sign = $sign;
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('begin ' . $this->sign . ' {?}', $this->begin->toDb());
    }
}

/** Returns news that end before (or after) a specified datetime
 */
class NFC_End extends NewsFilterCondition
{
    const AFTER  = '>=';
    const BEFORE = '<=';
    const EGAL   = '=';

    private $sign;
    private $end;

    public function __construct(FrankizDateTime $end, $sign = self::AFTER)
    {
        $this->end  = $end;
        $this->sign = $sign;
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('n.end ' . $this->sign . ' {?}', $this->end->toDb());
    }
}

/** Returns news that are not out-of-date
 */
class NFC_Current extends NewsFilterCondition
{
    public function buildCondition(PlFilter $uf)
    {
        return 'NOW() BETWEEN n.begin AND n.end';
    }
}

/** Returns news that the users are allowed to see
 * (ie the user is in the group or the news is public)
 * @param $us     A User, a uid or an array
 */
class NFC_CanBeSeen extends NewsFilterCondition
{
    private $uids;

    public function __construct($us)
    {
        $this->uids = User::toIds(unflatten($us));
    }

    public function buildCondition(PlFilter $f)
    {
        $c = $f->addCasteFilter();
        $cu = $f->addUserFilter();
        return XDB::format("$c.rights = {?} OR ($c.rights = {?} AND $cu.uid IN {?})",
                        (string) Rights::everybody(), (string) Rights::restricted(), $this->uids);
    }
}

abstract class NewsFilterOrder extends FrankizFilterOrder
{
}

class NFO_Begin extends NewsFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "n.begin";
    }
}

class NFO_End extends NewsFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "n.end";
    }
}

/***********************************
  *********************************
          NEWS FILTER CLASS
  *********************************
 ***********************************/

class NewsFilter extends FrankizFilter
{
    protected function schema()
    {
        return array('table' => 'news',
                     'as'    => 'n',
                     'id'    => 'id');
    }

    private $with_caste = false;

    public function addCasteFilter()
    {
        $this->with_caste = true;
        return 'c';
    }

    protected function casteJoins()
    {
        $joins = array();
        if ($this->with_caste) {
            $joins['c'] = PlSqlJoin::inner('castes', '$ME.cid = n.target');
        }
        return $joins;
    }

    private $with_user = false;

    public function addUserFilter()
    {
        $this->with_user = true;
        return 'cu';
    }

    protected function userJoins()
    {
        $joins = array();
        if ($this->with_user) {
            $joins['cu'] = PlSqlJoin::left('castes_users',
                '$ME.cid = c.cid AND ($ME.visibility IN {?} OR $ME.uid = {?})',
                S::user()->visibleGids(), S::user()->id());
        }
        return $joins;
    }
}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
