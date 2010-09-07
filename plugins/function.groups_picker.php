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
* @param $root the root group
* @param $depth the initial depth of the tree
* @param $target target url when clicking on a group
* @param $gids groups to load
* @param $restrict disabled groups
* @param $type static, checkbox
*/
function smarty_function_groups_picker($params, &$smarty)
{
    $visibility = (isset($params['visibility'])) ? $params['visibility'] : Group::maxDepth;
    
    $ptid = null;
    if (isset($params['from']))
    {
        $depth = (isset($params['depth'])) ? $params['depth'] : Group::maxDepth;

        $from = $params['from'];
        Group::batchChildren($from, $depth);

        $roots = Group::unflatten(Group::get($from));
    } 
    else if (isset($params['to']))
    {
        $depth = Group::maxDepth;

        $to = $params['to'];
        if ($to == 'all') {
            $to = S::user()->gids();
        }

        $ptid = Group::ascendingPartialTree($to);
        
        // TODO: Allow restriction to the groups under a specified root 
        // (without depth, partialTreeRoots() is useless as Group::root() is *always* the root)
        $roots = Group::partialTreeRoots($ptid);
    }

    // If there is only one root, get rid of it
    if (count($roots) == 1) {
        $visibility--;
        $root = array_pop($roots);
        $roots = $root->partialChildren($ptid);
    }

    $json = array();
    foreach ($roots as $root)
        $json[] = $root->toJson($depth, $visibility, $ptid);

    $smarty->assign($params['out_json'], json_encode($json));
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
