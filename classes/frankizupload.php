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

class FrankizUpload
{
    // Original filename
    protected $name = null;
    // Path to the temporary file on the server
    protected $path   = null;

    protected function __construct($name)
    {
        $file =  $_FILES[$name];
        $this->name = $file['name'];
        $this->path = $file['tmp_name'];

        $this->checkUploadErrors($file);
    }

    /**
    * Check if the form input exists
    *
    * @param $name  The name of the <input type="file"> tag
    */
    public static function has($name)
    {
        return isset($_FILES[$name]);
    }

    /**
    * Retrieve the FrankizUpload instance linked to the sent file
    *
    * @param $name  The name of the <input type="file"> tag
    */
    public static function v($name)
    {
        if (!self::has($name))
            return false;

        $fu = new FrankizUpload($name);
        return $fu;
    }

    protected function checkUploadErrors(array &$file)
    {
        if (@$file['error']) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE: case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception('File is to big (limit: ' . ini_get('upload_max_filesize') . ')');
                    break;
                case UPLOAD_ERR_PARTIAL: case UPLOAD_ERR_NO_FILE:
                    throw new Exception('File not transmitted in integrality');
                    break;
                default:
                    throw new Exception('Unknow error');
            }
        }
        if (!is_uploaded_file($file['tmp_name']))
            throw new Exception('File is not an uploaded file');
    }

    public function path()
    {
        return $this->path;
    }

    public function name()
    {
        return $this->name;
    }

    public function rm()
    {
        @unlink($this->path);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
