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

class Less
{
    const CSS_PATH = '../htdocs/css';
    const LESS_PATH = '../less';

    private static function path_less_to_css($less) {
        return self::CSS_PATH . substr($less, strlen(self::LESS_PATH));
    }

    private static function make_dir($less) {
        $css = self::path_less_to_css($less);
        if (!file_exists($css)) {
            mkdir($css);
            chmod($css, 0770);
        }
    }

    private static function make_file($less) {
        $css = self::path_less_to_css($less);
        if (!file_exists($css) || filemtime($css) < filemtime($less)) {
            copy($less, $css);
            chmod($css, 0770);
        }
    }

    private static function make_less($less, $force) {
        global $globals;

        $css = self::path_less_to_css($less);
        $css = substr($css, 0, strlen($css) - 4) . 'css';
        if ($force || (!file_exists($css) || filemtime($css) < filemtime($less))) {
            if ($globals->debug & DEBUG_BT) {
                PlBacktrace::$bt['Less']->start($less);
            }

            exec('lessc ' . escapeshellarg($less) . ' --compress > ' . escapeshellarg($css));
            chmod($css, 0770);

            if ($globals->debug & DEBUG_BT) {
                PlBacktrace::$bt['Less']->stop(0, null, array());
            }
        }
    }

    private static function work($root) {
        $files = glob($root . '/*');
        if ($files === false)
            return;

        $force = false;
        $lib_less = $root . '/lib.less';
        if (in_array($lib_less, $files)) {
            $lib_css = self::path_less_to_css($lib_less);
            $lib_css = substr($lib_css, 0, strlen($lib_css) - 4) . 'css';
            if (file_exists($lib_css) && filemtime($lib_css) < filemtime($lib_less)) {
                $force = true;
            }
        }

        foreach($files as $file) {
            if (is_dir($file)) {
                self::make_dir($file);
                self::work($file);
            } else {
                if (substr($file, -5) != '.less') {
                    self::make_file($file);
                } else {
                    self::make_less($file, $force);
                }
            }
        }
    }

    public static function make() {
        global $globals;

        if ($globals->debug & DEBUG_BT) {
            new PlBacktrace('Less');
        }

        self::work(self::LESS_PATH);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
