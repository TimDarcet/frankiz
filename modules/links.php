<?php
/***************************************************************************
 *  Copyright (C) 2010 Binet Réseau                                       *
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

class LinksModule extends PlModule
{
    public function handlers()
    {
        return array(
            'links'                         => $this->make_hook('links',AUTH_PUBLIC),
            'links/admin'                   => $this->make_hook('links_admin',AUTH_MDP),
            'links/new'                     => $this->make_hook('links_new',AUTH_MDP),
        );
    }

    function handler_links($page, $type)
    {
        $collec = Link::all($type);
        $collec->select(LinkSelect::all());

        $page->assign('type', $type);
        $page->assign('admin', S::user()->perms()->hasFlag('admin'));
        $page->assign('links', $collec);
        $page->assign('title', 'Liens extérieurs');
        $page->addCssLink('links.css');
        $page->changeTpl('links/link.tpl');
    }

    function handler_links_new($page)
    {
        if (!S::user()->perms()->hasFlag('admin'))
            return PL_FORBIDDEN;

        $label = Env::t('label', '');
        $link = Env::t('link', '');
        $description = Env::t('description', '');
        $comment = Env::t('comment', '');
        $type = Env::t('type', '');
        trace($type);

        if (Env::has('create') && $label!='' && $link!='' && ($type == 'partners' || $type == 'usefuls'))
        {
            $l = new Link();
            $l->insert($type);
            if (FrankizUpload::has('image') && $type == 'partners')
            {
                try
                {
                    $group = Group::from('partnership');
                    $group->select(GroupSelect::castes());
                    $image = new FrankizImage();
                    $image->insert();
                    $image->label($label);
                    $image->caste($group->caste(new Rights('everybody')));
                    $image->image(FrankizUpload::v('image'));
                    $l->image($image);
                }
                catch (Exception $e)
                {
                    $page->assign('err', $e->getMessage());
                }
            }
            $l->label($label);
            $l->link($link);
            $l->description($description);
            $l->comment($comment);
            pl_redirect('links/' . $type);
        }

        $page->assign('title', 'Nouveau lien');
        $page->addCssLink('links.css');
        $page->changeTpl('links/new_link.tpl');
    }

    function handler_links_admin($page)
    {
        if (!S::user()->perms()->hasFlag('admin'))
            return PL_FORBIDDEN;

        $collec = Link::all();
        $collec->select(LinkSelect::all());

        $results = $collec->split('ns');

        if (Env::has('modify'))
        {
            $id = Env::i('id');
            $link = $collec->get($id);
            if ($link !== false)
            {
                if (Env::has('image'))
                {
                    try
                    {
                        $group = Group::from('partnership');
                        $group->select();
                        $image = new FrankizImage();
                        $image->insert();
                        $image->label($link->label());
                        $image->caste($group->caste('everybody'));
                        $image->image(FrankizUpload::v('image'));
                        $link->image($image);
                    }
                    catch (Exception $e)
                    {
                        $page->assign('err', $e->getMessage());
                    }
                }
                $link->label(Env::t('label'));
                $link->link(Env::t('link'));
                $link->description(Env::t('description'));
                $link->comment(Env::t('comment'));
            }
            else
            {
                $err = 'Le lien modifié n\'existe plus.';
                $page->assign('err', $err);
            }
        }

        $page->addCssLink('links.css');
        $page->assign('links', $results);
        $page->assign('title', 'Administrer les liens');
        $page->changeTpl('links/admin_links.tpl');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
