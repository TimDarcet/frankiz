<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
 ***************************************************************************/

class PixMiniModule extends FrankizMiniModule
{
    public function auth()
    {
        return AUTH_INTERNAL;
    }

    public function js()
    {
        return 'minimodules/pix.js';
    }

    public function css()
    {
        return 'minimodules/pix.css';
    }

    public function tpl()
    {
        return IPAddress::getInstance()->has_x_student()
            ? 'minimodules/pix/internal.tpl'
            : 'minimodules/pix/external.tpl';
    }

    public function title()
    {
        return 'piX : dernières photos postées';
    }

    protected function loadFile($url){
        $api = new API($url, false);
        $photos = simplexml_load_string(utf8_decode($api->response())); //charset fix
        $res = Array();
        if (empty($photos) || !is_array($photos))
            return $res;

        foreach($photos as $photo){
            $res[] = Array(
                "urlPage" => "http://pix/photo/".$photo->id,
                "imgThumb" => "http://pix/media/photo/thumb/".$photo->author->id."/".$photo->link,
                "imgFull" => "http://pix/media/photo/view/".$photo->author->id."/".$photo->link,
                "title" => htmlspecialchars($photo->title." par ".$photo->author->name." ".$photo->author->surname),
                "text" => htmlspecialchars($photo->title." par ".$photo->author->name." ".$photo->author->surname)."<br/><a href='http://pix/photo/".$photo->id."'>voir dans piX</a>"
            );
        }
        return $res;
    }

    protected function getCachedArray() {
        global $globals;

        if (!PlCache::hasGlobal('pix'))
            PlCache::setGlobal('pix', $this->loadFile('http://pix/frankiz.xml'), $globals->cache->pix);

        return PlCache::getGlobal('pix');
    }


    public function run()
    {
        $this->assign('photos', $this->getCachedArray());
        $this->assign('nMontrees', 6);
    }
}

