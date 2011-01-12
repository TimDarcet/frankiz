<?php
/***************************************************************************
 *  Copyright (C) 2003-2010 Polytechnique.org                              *
 *  http://opensource.polytechnique.org/                                   *
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

abstract class ImageFilterCondition extends FrankizFilterCondition
{
}

class IFC_Group extends ImageFilterCondition
{
    private $gids;

    public function __construct($gs)
    {
        if ($gs instanceof Collection)
            $this->gids = $gs->ids();
        else
            $this->gids = Group::toIds(unflatten($gs));
    }

    public function buildCondition(PlFilter $uf)
    {
        return XDB::format('i.gid IN {?}', $this->gids);
    }
}

abstract class ImageFilterOrder extends FrankizFilterOrder
{
}

class IFO_Seen extends ImageFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "i.seen";
    }
}

/***********************************
  *********************************
          IMAGE FILTER CLASS
  *********************************
 ***********************************/

class ImageFilter extends FrankizFilter
{
    protected function schema()
    {
        return array('table' => 'images',
                     'as'    => 'i',
                     'id'    => 'iid');
    }

    protected function className() {
        return 'FrankizImage';
    }
}
// }}}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>