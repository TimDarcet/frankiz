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
* @param $type ascending or descending or fixed (default: fixed)
* @param $groups groups from which the tree originates (default: main root)
* @param $depth the depth to search for childrens or fathers
* @param $visibility the depth to which the nodes are open
* @param $behead boolean defining if the roots must be hidden or not (default: false)
*
* @param $out_json
*/
function smarty_function_groups_picker($params, &$smarty)
{
    $gI = GroupsTreeInfo::get();

    $type       = (empty($params['type']))       ? 'fixed'                : $params['type'];
    $visibility = (empty($params['visibility'])) ? $gI->maxDepth()        : $params['visibility'];
    $depth      = (empty($params['depth']))      ? $gI->maxDepth()        : $params['depth'];
    $behead     = (empty($params['behead']))     ? false                  : $params['behead'];
    $groups     = (empty($params['groups']))     ? unflatten($gI->root()) : Group::fromIds(unflatten($params['groups']));

    $tree = new Tree($gI);

    switch ($type) {
        case 'descending':
            $tree->descending($groups, $depth);
            break;
        case 'ascending':
            $tree->ascending($groups, $depth);
            break;
        default:
            $tree->fixed($groups);
    }

    $tree->load(Group::BASE);

    if (!$behead) {
        $json = $tree->toJson($visibility);
    } else {
        $visibility--;
        $tree->behead();
    }

    $smarty->assign($params['out_json'], json_encode($tree->toJson($visibility)));
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
