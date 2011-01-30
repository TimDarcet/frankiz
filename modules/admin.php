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
            'admin'             => $this->make_hook('admin'   , AUTH_COOKIE),
            'admin/su'          => $this->make_hook('su'      , AUTH_MDP, 'admin'),
            'admin/images'      => $this->make_hook('images'  , AUTH_MDP, 'admin'),
            'admin/image'       => $this->make_hook('image'   , AUTH_MDP, 'admin'),
            'admin/group'       => $this->make_hook('group'   , AUTH_MDP, 'admin'),
            'admin/bubble'      => $this->make_hook('bubble'  , AUTH_MDP, 'admin'),
            'admin/validate'    => $this->make_hook('validate', AUTH_COOKIE),
            'debug'             => $this->make_hook('debug'   , AUTH_PUBLIC)
        );
    }

    function handler_admin($page)
    {
        $page->assign('title', "Administration");
        $page->changeTpl('admin/index.tpl');
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

    function handler_bubble($page, $cid)
    {
        $c = new Caste($cid);
        $c->select();

        $tobeexplored = new Collection('Caste');
        $tobeexplored->add($c);

        $explored = new Collection('Caste');

        while ($c = $tobeexplored->pop()) {
            $explored->add($c);
            $tobeexplored->merge($c->parents());
        }

        $page->assign('title', "Bubbles");
        $page->assign('castes', $explored);
        $page->changeTpl('admin/bubble.tpl');
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
        $filter = new ValidateFilter(new VFC_User(S::user()));

        $collec = $filter->get();
        $collec->select(Validate::SELECT_BASE | Validate::SELECT_ITEM);

        if(Env::has('val_id'))
        {
            $el = $collec->get(Env::v('val_id'));
            if (!$el)
                $page->assign('msg', 'La validation a déjà été effectuée.');
            else
            {
                if ($el->handle_form() && (Env::has('accept') || Env::has('refuse')))
                    $collec->remove(Env::v('val_id'));
            }
                
        }

        $page->assign('val', $collec);
        $page->addJsLink('validate.js');
        $page->addCssLink('validate.css');
        $page->changeTpl('validate/validate.tpl');
    }

    function handler_debug($page)
    {
        global $globals;

        if ($globals->debug & DEBUG_BT) {
            $sessions = array();
            foreach ($_SESSION as $key => $val) {
                ob_start();
                var_dump($val);
                $str = ob_get_clean();

                  $str = str_replace("\n", '', $str);
                  $str = str_replace('{', '</span><ul><li><span>', $str);
                  $str = str_replace('[', '</span></li><li><span>[', $str);
                  $str = str_replace('}', '</li></span></ul>', $str);
                  $str = preg_replace('/<span> *<\/span>/i', '', $str);
                  $str = preg_replace('/<li> *<\/li>/i', '', $str);

                $sessions[$key] = $str;
            }
            $page->assign('session', $sessions);
        }

        $page->assign('title', 'Debug');
        $page->changeTpl('admin/debug.tpl');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
