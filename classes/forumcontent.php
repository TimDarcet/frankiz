<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                        *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                      *
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

class ForumContentSchema extends Schema
{
    public function className() {
        return 'ForumContent';
    }

    public function table() {
        return 'forum_content';
    }

    public function id() {
        return 'id';
    }

    public function tableAs() {
        return 'fc';
    }

    public function scalars() {
        return array('title', 'message', 'creation_date', 'last_modification_date', 'node_id');
    }
}

class ForumContent extends Meta
{
    protected $node_id;

    protected $title;
    protected $message;

    protected $creation_date;
    protected $last_modification_date;

    public static function batchFrom(array $nodeIds)
    {
        $nodeIds = unflatten($nodeIds);

        $collec = new Collection();
        if (!empty($nodeIds)) {
            $iter = XDB::iterator('SELECT title, message, creation_date, last_modifiation_date, node_id
                                     FROM  groups
                                    WHERE  node_id IN {?}', $nodeIds);
            while ($g = $iter->next())
                $collec->add(new self($g));
        }

        if (count($mixed) != $collec->count()) {
            throw new ItemNotFoundException('Asking for ' . implode(', ', $mixed) . ' but only found ' . implode(', ', $collec->ids()));
        }

        return $collec;
    }
}
