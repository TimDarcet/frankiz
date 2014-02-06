<?php
/***************************************************************************
* Copyright (C) 2004-2012 Binet Réseau *
* http://br.binets.fr/ *
* *
* This program is free software; you can redistribute it and/or modify *
* it under the terms of the GNU General Public License as published by *
* the Free Software Foundation; either version 2 of the License, or *
* (at your option) any later version. *
* *
* This program is distributed in the hope that it will be useful, *
* but WITHOUT ANY WARRANTY; without even the implied warranty of *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the *
* GNU General Public License for more details. *
* *
* You should have received a copy of the GNU General Public License *
* along with this program; if not, write to the Free Software *
* Foundation, Inc., *
* 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA *
***************************************************************************/

class ForumMiniModule extends FrankizMiniModule
{
    public function auth()
    {
        return AUTH_INTERNAL;
    }

    public function tpl()
    {
        return 'minimodules/forum/forum.tpl';
    }
    
    public function css()
    {
        return 'minimodules/forum.css';
    }
    
    public function js()
    {
        return '';//'minimodules/forum.js';
    }
    
    public function title()
    {
        return 'Career Center';
    }

    public function run()
    {
		$api = new API("http://polytechnique.jobteaser.com/fr/evenements.rss");
        $xml = simplexml_load_string(utf8_decode($api->response()));
		$i=1;
		foreach ($xml->channel->item as $item){
			$this->assign('title'.$i, $item->title);
			$this->assign('description'.$i, $item->description);
			$this->assign('link'.$i, $item->link);
			$this->assign('guid'.$i, $item->guid);
			$i++;
		}
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>