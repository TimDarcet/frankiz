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
 * This script creates and updates the groups corresponding to the promos
 */

require 'connect.db.inc.php';

$gf = new GroupFilter(new GFC_Name('everybody'));
$g = $gf->get(true);
$g->select(GroupSelect::castes());
$c = $g->caste(Rights::member());
$c->select(CasteSelect::base())->compute();
echo 'Groupe everybody mis à jour' . "\n";

$gf = new GroupFilter(new GFC_Name('public'));
$g = $gf->get(true);
$g->select(GroupSelect::castes());
$c = $g->caste(Rights::member());
$c->select(CasteSelect::base())->compute();
echo 'Groupe visibilité extérieure  mis à jour' . "\n";

$gf = new GroupFilter(new GFC_Name('licenses'));
$g = $gf->get(true);
$g->select(GroupSelect::castes());
$c = $g->caste(Rights::member());
$c->select(CasteSelect::base())->compute();
echo 'Groupe licenses mis à jour' . "\n";

