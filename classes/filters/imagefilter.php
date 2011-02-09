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

class IFC_Id extends ImageFilterCondition
{
    private $ids;

    public function __construct($metas)
    {
        if ($metas instanceof Collection) {
            $this->ids = $metas->ids();
        } else {
            $this->ids = FrankizImage::toIds(unflatten($metas));
        }
    }

    public function buildCondition(PlFilter $f)
    {
        return XDB::format('i.iid IN {?}', $this->ids);
    }
}

class IFC_Caste extends ImageFilterCondition
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
        return XDB::format('i.caste IN {?}', $this->cids);
    }
}

class IFC_Temp extends ImageFilterCondition
{
    public function buildCondition(PlFilter $f)
    {
        $g = Group::from('temp')->select(GroupSelect::castes());
        $temp = $g->caste(Rights::everybody());

        return XDB::format('i.caste = {?}', $temp->id());
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

class IFO_Created extends ImageFilterOrder
{
    public function __construct($desc = false)
    {
        parent::__construct($desc);
    }

    protected function getSortTokens(PlFilter $gf)
    {
        return "i.created";
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
