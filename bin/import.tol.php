#!/usr/bin/php -q
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

/*
 * This script import the images for the TOL
 * It's intended to be used in scripts to import pictures into the TOL
 * 
 * Arguments:
 * - UserFilter to define the users to work on
 * - Folder to search the pictures in
 * - Method of the User to use to find the correct picture
 * - Load the original picture or the current photo ?
 * 
 * Example :
 * ./import.tol.php '{"type":"user","condition":{"type":"uid","uids":[2]}}' . poly original
 *
 * Or :
 *
 * ./import.tol.php '{"type":"user","condition":{"type":"promo","comparison":"=","promo":2008}}' /home/2008/riton/dev/tol/2008 poly original
 * 
 */

require 'connect.db.inc.php';

$gf = new GroupFilter(new GFC_Name('tol'));
$group = $gf->get(true);

// Concerned users
$uf = UserFilter::fromExport(json_decode($argv[1], true));
// Folder to look in
$folder = $argv[2];
// Field to use for the "join"
$field = $argv[3];
// Update original or current picture ?
$original = ($argv[4] == 'photo') ? false : true;

$total = $uf->getTotalCount();
$chunk = (empty($argv[5])) ? 100 : $argv[5];
$from = 0;

while (true) {
    echo "Chunk from $from to " . ($from+$chunk) . "\n";

    $users = $uf->get(new PlLimit($chunk, $from));
    if ($users->count() == 0)
        break;
    $users->select(User::SELECT_BASE | User::SELECT_POLY);

    foreach ($users as $u)
    {
        $suffix = ($original) ? '_original' : '';
        $path = $folder . '/' . $u->$field() . $suffix . '.jpg';
        if (file_exists($path)) {
            $upload = FrankizUpload::fromFile($path);
            $i = new FrankizImage();
            $i->insert();
            $i->group($group);
            $i->label($u->firstname() . ' ' . $u->lastname());
            $i->image($upload, false);
            if ($original)
                $u->original($i);
            else
                $u->photo($i);
            echo 'Ok: ' . $u->id() . ' - ' . $u->displayname() . ' - '. $path . "\n";
        } else {
            echo 'Error: ' . $u->id() . ' - ' . $u->displayname() . ' - '. $path . "\n";
        }
    }

    $from += $chunk;
}

echo 'Fini' . "\n";

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
