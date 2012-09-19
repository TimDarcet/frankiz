#!/usr/bin/php -q
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

/*
 * Ce script a été utilisé pour standardiser les noms des photos données
 * par l'administration pour la promotion 2010
 * 
 */

$dir = "photos/"; 
$files = scandir($dir);
foreach($files as $key => $value) {
    $new=strtolower(preg_replace('/ /','-',preg_replace('/([A-Z ]*)\s?-\s?([A-Z ]*).*/','$2.$1',$value)));
    rename($dir.$value,$dir.$new.'_original.jpg'); 
    echo $new ."\n";
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
