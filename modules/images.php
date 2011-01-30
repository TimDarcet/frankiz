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
        $gf = new GroupFilter(new GFC_User(S::user(), Rights::admin()));
        $groups = $gf->get()->select(Group::SELECT_BASE);
        $page->assign('groups', $groups);

        if (FrankizUpload::has('file'))
        {
            $group = new Group(Env::v('group'));

            if($groups->get($group) === false) {
                throw new Exception("You don't have the credential to upload an image in this group");
            }

            $group->select(Group::SELECT_CASTES);
            $upload = FrankizUpload::v('file');

            // Create and store an empty FrankizImage
            $i = new FrankizImage();
            $i->insert();

            // Assign a group to it
            $i->caste($group->caste(Rights::everybody()));
            // A label
            $i->label(Env::v('label', ''));
            // And a description
            $i->description(Env::v('description', ''));
            // Don't forget to stores the actual image…
            $i->image($upload);

            $page->assign('last_upload', $i);
        }

        $page->addCssLink('images.css');
        $page->assign('title', 'Envoyer une image');
        $page->changeTpl('images/upload.tpl');
    }

    function handler_image($page, $size, $iid = null)
    {
        $image = new FrankizImage($iid);
        $image->select(array(FrankizImage::SELECT_BASE => CasteSelect::base()));
        $image->caste()->group()->select(GroupSelect::base());

        if (S::i('auth') == AUTH_PUBLIC && !$image->caste()->external()) {
                // TODO: show an 'invalid credential' picture instead
                throw new Exception("This image is not accessible from the outside");
                exit;
        }

        // If the image isn't public & we are not member of the group
        if (!$image->caste()->rights()->isMe(Rights::everybody())
            && (!S::user()->hasRights($image->caste()->group(), Rights::member()))) {
                // TODO: show an 'invalid credential' picture instead
                throw new Exception("You don't have the credential to view this image");
                exit;
        }

        $select = ImageInterface::SELECT_FULL;
        switch ($size) {
            case 'micro':
                $select = ImageInterface::SELECT_MICRO;
                break;
                
            case 'small':
                $select = ImageInterface::SELECT_SMALL;
                break;
        }

        $image->select($select);
        $image->send();
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
