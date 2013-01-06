<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
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

class xorgUnkonwnUserException extends Exception
{
}

class xorgAPI extends API
{
    public static function isRegistered($forlife)
    {
        global $globals;

        $payload = '';
        $method = 'GET';
        $resource = '/api/1/user/'. $forlife . '/isRegistered';
        $timestamp = time();

        $message = implode('#', array($method, $resource, $payload, $timestamp));
        $token = $globals->xorg->hash;
        $sig = hash_hmac('sha256', $message, $token);

        $get = '?user=' . $globals->xorg->user . '&timestamp=' . $timestamp . '&sig=' . $sig;

        $url = $globals->xorg->url . $resource . $get;

        $a = new xorgAPI($url);
        $a->exec();

        if ($a->http_code() == 404) {
            throw new xorgUnkonwnUserException("$forlife doesn't seem to exist");
        }

        $json = json_decode($a->response());
        return $json->isRegistered;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
