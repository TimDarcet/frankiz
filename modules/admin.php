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

/* contains all admin stuff */
class AdminModule extends PlModule
{
    function handlers()
    {
        return array(
            'admin/su'          => $this->make_hook('su'    , AUTH_MDP, 'admin'),
            'admin/tree'        => $this->make_hook('tree'  , AUTH_MDP, 'admin'),
            'admin/images'      => $this->make_hook('images', AUTH_MDP, 'admin'),
            'admin/image'       => $this->make_hook('image' , AUTH_MDP, 'admin'),
            'admin/validate'    => $this->make_hook('validate' , AUTH_MDP),
            'admin/debug'       => $this->make_hook('debug' , AUTH_PUBLIC)
        );
    }

    function handler_su($page, $uid=0)
    {
        if (S::has('suid')) {
            $page->kill("Déjà en SUID !!!");
        }
        $res = XDB::query("SELECT eleve_id
                             FROM compte_frankiz
                            WHERE eleve_id = {?}", $uid);
        if($res->numRows() == 1){
            if(!Platal::session()->startSUID($uid)) {
                $page->trigError('Impossible d\'effectuer un SUID sur ' . $uid);
            } else {
                $page->kill("SU ok");
                pl_redirect('');
            }
        }
    }

    function handler_tree($page)
    {
        $page->assign('title', 'Arbre des groupes');
        $page->changeTpl('admin/tree.tpl');
    }

    function handler_images($page)
    {
        if (Env::has('comment') && FrankizUpload::has('file'))
        {
            $fu = FrankizUpload::v('file');
            $im = new FrankizImage(array('comment' => Env::v('comment')));
            $im->loadUpload($fu)->insert();
        }

        $res = XDB::query('SELECT iid FROM images LIMIT 30');

        $images = Collection::fromClass('FrankizImage');
        $images->add($res->fetchColumn())->select(FrankizImage::SELECT_BASE);

        $page->assign('title', 'Gestion des images');
        $page->assign('images', $images);
        $page->addCssLink('admin.css');
        $page->changeTpl('admin/images.tpl');
    }

    function handler_image($page, $iid)
    {
        $image = new FrankizImage($iid);
        if (Env::has('small'))
            $image->select(FrankizImage::SELECT_SMALL);
        else
            $image->select(FrankizImage::SELECT_FULL);

        $image->send();
    }
    

    function handler_validate($page, $action = 'list', $id = null) 
    {        
        if(env::has('id'))
        {
            $el  = Validate::create_from_id(env::v('id'));
            if (!is_null($el))
            {
                $el->select(SELECT_INFOS | SELECT_ITEM);
                $el->handle_form();
            }
            else
                $page->assign('msg', 'La validation a déjà été effectuée.');
        }
        
        //must create the collection of items to validate
        // for example
        
        $el  = new Validate(43);
        $el->select(SELECT_INFOS | SELECT_ITEM);
        $c = new Collection(Validate);
        $c->add($el);
        
        $page->assign('val', $c);
        $page->addJsLink('validate.js');
        $page->addCssLink('validate.css');
        $page->changeTpl('validate/validate.tpl');
    }

    function handler_debug($page)
    {
        global $globals;

        $page->assign('title', 'Debug');
        $page->changeTpl('debug.tpl');

    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
