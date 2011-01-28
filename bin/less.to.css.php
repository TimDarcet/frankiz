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
 * This script compils the .less files to .css files
 */

require 'connect.db.inc.php';

const CSS_PATH = '../htdocs/css';
const LESS_PATH = '../less';

function path_less_to_css($less) {
    return CSS_PATH . substr($less, strlen(LESS_PATH));
}

function make_dir($less) {
    $css = path_less_to_css($less);
    if (!file_exists($css)) {
        mkdir($css);
        echo $css . "\n";
    }
}

function make_file($less) {
    $css = path_less_to_css($less);
    if (!file_exists($css) || filemtime($css) < filemtime($less)) {
        copy($less, $css);
        echo $css . "\n";
    }
}

function make_less($less) {
    $css = path_less_to_css($less);
    $css = substr($css, 0, strlen($css) - 4) . 'css';
    if (!file_exists($css) || filemtime($css) < filemtime($less)) {
        exec('lessc ' . escapeshellarg($less) . ' --compress > ' . escapeshellarg($css));
        echo $less . ' => ' . $css . "\n";
    }
}

function work($root) {
    $files = glob($root . '/*');
    foreach($files as $file) {
        if (is_dir($file)) {
            make_dir($file);
            work($file);
        } else {
            if (substr($file, -5) != '.less') {
                make_file($file);
            } else {
                make_less($file);
            }
        }
    }
}

work(LESS_PATH);

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
