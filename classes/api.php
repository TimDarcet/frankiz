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

class API
{
    private $url;
    private $proxy;

    private $response = null;
    private $infos = null;

    public function __construct($url, $proxy = true, $post_fields = "")
    {
        $this->url   = $url;
        $this->proxy = $proxy;
	$this->post_fields = $post_fields;
    }

    public function exec()
    {
        global $globals;

        if ($this->response === null) {
            if ($globals->debug & DEBUG_BT) {
                if (!isset(PlBacktrace::$bt['API']))
                    new PlBacktrace('API');
            }

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Pragma: no-cache"));
	    if ($this->post_fields != "") {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $this->post_fields);
	    }
            if ($this->proxy) {
                // DIE Kuzh, DIE !
                curl_setopt($curl, CURLOPT_PROXY, $globals->api->http_proxy);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            // TODO: use certificates for https requests
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_URL, $this->url);

            if ($globals->debug & DEBUG_BT) {
                PlBacktrace::$bt['API']->start($this->url);
            }

            $this->response = utf8_encode(curl_exec($curl));
            $this->infos = curl_getinfo($curl);
            curl_close($curl);

            if ($globals->debug & DEBUG_BT) {
                $datas = array(array_intersect_key(
                                $this->infos,
                                array_flip(array("content_type", "http_code", "header_size",
                                                 "request_size", "redirect_count", "size_download"))));
                PlBacktrace::$bt['API']->stop(substr_count($this->response, "\n"), null, $datas);
            }
        }
    }

    public function response()
    {
        $this->exec();
        return $this->response;
    }

    public function infos()
    {
        $this->exec();
        return $this->infos;
    }

    public function http_code()
    {
        $this->exec();
        return $this->infos['http_code'];
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
