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

class ImagesModule extends PlModule
{
    function handlers()
    {
        return array(
            'images/gc'     => $this->make_hook('gc',     AUTH_MDP,    'admin'),
            'images/upload' => $this->make_hook('upload', AUTH_COOKIE),
            'image'         => $this->make_hook('image',  AUTH_PUBLIC, ''),
        );
    }

    function handler_gc($page)
    {
        $if = new ImageFilter(new PFC_And(new IFC_NoSize(), new IFC_Temp()));
        $images = $if->get()->select(FrankizImageSelect::gc());

        $page->assign('images', $images);
        $page->addCssLink('images.css');
        $page->assign('title', 'Images GC');
        $page->changeTpl('images/gc.tpl');
    }

    function handler_upload($page)
    {
        $page->assign('exception', false);
        $page->assign('image', false);
        if (FrankizUpload::has('file')) {
            $g = Group::from('temp')->select(GroupSelect::castes());
            $temp = $g->caste(Rights::everybody());

            try {
                $upload = FrankizUpload::v('file');

                $secret = uniqid();

                $i = new FrankizImage();
                $i->insert();
                $i->caste($temp);
                $i->label($secret);
                $i->image($upload);

                $page->assign('image', $i);
                $page->assign('secret', $secret);
            } catch (Exception $e) {
                try {
                if ($i) {
                    $i->delete();
                }
                } catch(Exception $eb) {
                    $page->assign('exception', $eb);
                }
                $page->assign('exception', $e);
                if ($e instanceof ImageSizeException) {
                    $page->assign('pixels', true);
                } else if ($e instanceof UploadSizeException) {
                    $page->assign('bytes', true);
                } else if ($e instanceof ImageFormatException) {
                    $page->assign('format', true);
                }
            }
        }
        if (Env::has('delete')) {
            $image = FrankizImage::fromId(Env::i('iid'), false);
            if ($image) {
                $image->select(FrankizImageSelect::base());
                if ($image->label() == Env::s('secret')) {
                    $image->delete();
                }
            }
        }

        $page->addCssLink('upload.css');
        $page->changeTpl('images/upload.tpl', SIMPLE);
    }

    function handler_image($page, $size, $iid = null)
    {
        global $globals;

        $image = FrankizImage::fromId($iid, null);
        if ($image) {
            $image->select(FrankizImageSelect::caste());
            $user = S::user();
            if ($user && $user->canSee($image->caste())) {
                $image->send($size);
                return;
            }
        }

        // Not found of error => HTTP 403
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        $img = new StaticImage($globals->images->forbidden);
        $img->send($size);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
