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
class ForumModule extends PLModule
{
    function handlers()
    {
        return array(
            'forum'               => $this->make_hook('forum',         AUTH_INTERNAL, ''),
            'forum/ajax/nodes'    => $this->make_hook('ajax_nodes',    AUTH_INTERNAL, ''),
            'forum/ajax/contents' => $this->make_hook('ajax_contents', AUTH_INTERNAL, ''),
            'forum/ajax/add'      => $this->make_hook('ajax_add',      AUTH_INTERNAL, ''),
            'forum/ajax/remove'   => $this->make_hook('ajax_remove',   AUTH_INTERNAL, ''),
            'forum/ajax/update'   => $this->make_hook('ajax_update',   AUTH_INTERNAL, '')
        );
    }

    function handler_forum($page)
    {
        $page->setTitle('test');
        $page->changeTpl('forum/test.tpl');
    }

    function handler_ajax_nodes($page, $nodeId, $topicId)
    {
        $node = new ForumNode($nodeId);
        $node->fillFromArray(array('topic' => $topicId));

        $page->jsonAssign('ancestors', $node->getAncestors());
        $page->jsonAssign('children', $node->getDescendants());

        return PL_JSON;
    }

    function handler_ajax_contents($page)
    {
        $json = json_decode(Env::v('json'));
        //$json = new ForumNode();
        //$json->nodeIds = array(1, 2, 4);

       if(!empty($json->nodeIds)) {

            $collected = array();

            foreach(ForumContent::batchFrom($json->nodeIds)->toArray() as $cid => $content)
                $collected[$cid] = $content->toArray();

            $page->jsonAssign('contents', $collected);
        }

        return PL_JSON;
    }

    function handler_ajax_add($page)
    {
        $json = json_decode(Env::v('json'));
        //$json = new ForumNode();
        //$json->father = 0;
        //$json->title = 'test';
        //$json->content = 'Contenu de test'."\n".'Contenu de test';
        if(isset($json->father, $json->title, $json->content)) {
            $content = new ForumContent();
            $content->insert();
            $content->title((string)$json->title);
            $content->message((string)$json->content);

            $node = new ForumNode(array(
                'parent_id' => (int)$json->father,
                'content_id' => $content->id()
                )
            );

            $node->insert();
        }

        return PL_JSON;
    }

    function handler_forum_ajax_remove($page, $nodeId)
    {
        $node = new ForumNode($nodeId);
        $node->delete();
    }

    function handler_forum_ajax_update($page, $nodeId)
    {
        $json = json_decode(Env::v('json'));

        $node = new ForumNode($json->node);
        $node->updateContent($json->content);
    }
}
