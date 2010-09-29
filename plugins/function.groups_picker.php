<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                       *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                     *
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
* return the code necessary to create a group-picker
*
* @param $type ascending, descending or fixed (default: fixed)
* @param $groups groups from which the tree originates (default: main root)
* @param $depth the depth to search for childrens or fathers
* @param $behead boolean defining if the roots must be hidden or not (default: false)
*
* @param $out_json
*/
function smarty_function_groups_picker($params, &$smarty)
{
    $type       = (empty($params['type']))       ? 'fixed'          : $params['type'];
    $depth      = (empty($params['depth']))      ? Group::MAX_DEPTH : $params['depth'];
    $behead     = (empty($params['behead']))     ? false            : $params['behead'];
    $_groups    = (empty($params['groups']))     ? Group::root()    : $params['groups'];

    $groups = new Collection();
    $groups->className('Group');
    $groups->add($_groups);

    if ($type == 'descending')
            $groups->select(array(Group::SELECT_CHILDREN => $depth));
    else if ($type == 'ascending')
            $groups->select(array(Group::SELECT_FATHERS => $depth));

    $roots = $groups->roots();

    if ($behead)
        $roots = $roots->children();

    $roots->select(Group::SELECT_BASE);

    $smarty->assign($params['out_json'], json_encode($roots->toJson()));
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
