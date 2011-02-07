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

class ImagesModule extends PlModule
{
    function handlers()
    {
        return array(
            'images'        => $this->make_hook('images', AUTH_COOKIE),
            'images/upload' => $this->make_hook('upload', AUTH_COOKIE),
            'image'         => $this->make_hook('image',  AUTH_PUBLIC, ''),
        );
    }

    function handler_images($page)
    {
        $castes = S::user()->castes();

        $page->assign('castes', $castes);
        if ($castes != false) {
            $if = new ImageFilter(new IFC_Caste($castes));
            $images = $if->get()->select(array(FrankizImage::SELECT_BASE => Group::SELECT_BASE));
            $page->assign('images', $images);
        }

        $page->addCssLink('images.css');
        $page->assign('title', 'Images');
        $page->changeTpl('images/images.tpl');
    }

    function handler_upload($page)
    {
        $page->assign('image', false);
        if (FrankizUpload::has('file')) {
            $g = Group::from('temp')->select(GroupSelect::castes());
            $temp = $g->caste(Rights::everybody());

            $upload = FrankizUpload::v('file');

            $secret = uniqid();

            $i = new FrankizImage();
            $i->insert();
            $i->caste($temp);
            $i->label($secret);
            $i->image($upload);

            $page->assign('image', $i);
            $page->assign('secret', $secret);
        }
        if (Env::has('delete')) {
            $image = new FrankizImage(Env::i('iid'));
            $image->select(FrankizImageSelect::base());

            if ($image->label() == Env::s('secret')) {
                $image->delete();
            }
        }

        $page->changeTpl('images/upload.tpl', SIMPLE);
    }

    function handler_image($page, $size, $iid = null)
    {
        $image = new FrankizImage($iid);
        $image->select(FrankizImageSelect::caste());

        /*
         * If the user is not logged-in and outside of the campus,
         * he *must* be member of the specified caste.
         */
        if (S::i('auth') == AUTH_PUBLIC && !S::user()->castes()->get($image->caste())) {
            throw new Exception("This image is not accessible from the outside");
            exit;
        }

        if (!$image->caste()->rights()->isMe(Rights::everybody()) && !S::user()->castes()->get($image->caste())) {
            // TODO: show an 'invalid credential' picture instead
            throw new Exception("You don't have the credential to view this image");
            exit;
        }

        $image->send($size);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
