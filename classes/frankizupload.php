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
    protected $path   = null;
    protected $source = null;

    public function __construct()
    {
    }

    public static function has($res)
    {
        return isset($_FILES[$res]) || file_exists($res);
    }

    public static function v($res)
    {
        if (!self::has($res))
            return false;

        $fu = new FrankizUpload();
        if (isset($_FILES[$res]))
            $fu->upload($_FILES[$res]);
        else
            $fu->download($res);

        return $fu;
    }

    protected function download($url)
    {
        if (!$url || @parse_url($url) === false)
            throw new Exception('Malformed URL given');
            
        //TODO : handle kuzh
        if (size($url) > ini_get('upload_max_filesize'))
            throw new Exception('File is to big (limit: ' . ini_get('upload_max_filesize') . ')');

        $path = '/tmp/fkz_' + uniqid();
        if (!file_put_contents($path, file_get_contents($url)))
            throw new Exception('Unknow error');

        $this->source = $url;
        $this->path   = $path;
    }

    protected function upload(array &$file)
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

        $this->source = $file['name'];
        $this->path   = $file['tmp_name'];
    }

    public function path()
    {
        return $this->path;
    }

    public function source()
    {
        return $this->source;
    }

    public function rm()
    {
        @unlink($this->filename);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
