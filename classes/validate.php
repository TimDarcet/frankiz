<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
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

class ValidateSchema extends Schema
{
    public function className() {
        return 'Validate';
    }

    public function table() {
        return 'validate';
    }

    public function id() {
        return 'id';
    }

    public function tableAs() {
        return 'v';
    }

    public function scalars() {
        return array('type');
    }

    public function objects() {
        return array('writer' => 'User',
                      'group' => 'Group',
                    'created' => 'FrankizDateTime');
    }
}

class ValidateSelect extends Select
{
    public function className() {
        return 'Validate';
    }

    public static function quick() {
        return new self(array('group', 'type', 'created'), array('group' => GroupSelect::base()));
    }

    public static function base($subs = null) {
        return new self(array('writer', 'type', 'group', 'created'),
                        array('writer' => UserSelect::base(), 'group' => GroupSelect::base()));
    }

    public static function validate($subs = null) {
        
        return new self(array('writer', 'type', 'group', 'created', 'item'),
                        array('writer' => UserSelect::base(), 'group' => GroupSelect::base()));
    }

    protected function handlers() {
        return array('main' => array('writer', 'type', 'group', 'created'),
                     'item' => array('item'));
    }

    protected function handler_item(Collection $validates, $fields) {
        $items = array();
        foreach($validates as $validate) {
            $items[$validate->id()] = array();
        }

        $iter = XDB::iterRow("SELECT  id, item
                                FROM  validate
                               WHERE  id IN {?}", $validates->ids());

        while (list($id, $item) = $iter->next()) {
            $items[$id] = unserialize($item);
        }

        $selects = array();
        $collections = array();
        foreach ($items as $item) {
            foreach ($item->objects() as $field => $select) {
                $hash = $select->hash();
                $selects[$hash] = $select;
                if (empty($collections[$hash])) {
                    $collections[$hash] = new Collection($select->className());
                }

                if ($item->$field() != false) {
                    $item->$field($collections[$hash]->addget($item->$field()));
                }
            }


            foreach ($item->collections() as $field => $select) {
                $hash = $select->hash();
                $selects[$hash] = $select;
                if (empty($collections[$hash])) {
                    $collections[$hash] = new Collection($select->className());
                }

                if ($item->$field() != false) {
                    $temp = new Collection($select->className());
                    foreach($item->$field() as $f) {
                        $temp->add($collections[$hash]->addget($f));
                    }
                    $item->$field($temp);
                }
            }
        }

        foreach ($collections as $hash => $collection) {
            $collection->select($selects[$hash]);
        }

        foreach ($validates as $validate) {
            $validate->fillFromArray(array('item' => $items[$validate->id()]));
        }
    }
}

class Validate extends Meta
{
    protected $writer = null; //user asking for the validation
    protected $group  = null; // group that should validate

    protected $type    = null;
    protected $created = null;

    // Item to validate : ItemValidate object
    protected $item = null;

    // Item doesn't have an auto getter
    public function item()
    {
        return $this->item;
    }

    public function label() {
        $className = $this->type().'Validate';
        return $className::label();
    }

    public function itemToDb() {
        $item = clone $this->item;
        $item->toDb();
        return serialize($item);
    }

    /** 
     * to use to send the data for moderation
     * if $this->item->unique is true, then the database will be cleaned before
     */
    public function insert()
    {
        if(is_null($this->item))
            return;

        if ($this->item->unique()) {
            XDB::execute('DELETE FROM  validate
                                WHERE  writer = {?} AND `group` = {?} AND type = {?}',
                         $this->writer->id(), $this->group->id(), $this->type);
        }

        XDB::execute('INSERT INTO  validate
                              SET  writer = {?}, `group` = {?}, type = {?}, 
                                   item = {?}, created = NOW()',
                            $this->writer->id(), $this->group->id(), $this->type, 
                            $this->itemToDb());

        $this->id = XDB::insertId();

        $this->item->sendmailadmin();
    }

    public function update()
    {
        XDB::execute('UPDATE  validate
                         SET  item = {?}
                       WHERE  id = {?}',
                     $this->item, $this->id);
        return true;
    }

    /** 
     * to clean an entry
     * must have an item
     */
    public function clean()
    {
        if ($this->item->unique()) {
            $success = XDB::execute('DELETE FROM  validate
                                           WHERE  writer = {?} AND `group` = {?} AND type = {?}',
                                    $this->writer->id(), $this->group->id(), $this->type);
        } else {
            $success =  XDB::execute('DELETE FROM  validate
                                            WHERE  id = {?}',
                                      $this->id);
        }
        return $success;
    }

    public function commit() {
        if ($this->item->commit()) {
            $this->item->sendmailfinal(true);
            $this->clean();
            return true;
        } else {
            return false;
        }
    }

    /** 
     * to validate a form
     */
    public function handle_form()
    {
        if(is_null($this->item))
            return false;

        // edit informations
        if (Env::has('edit')) {
            if ($this->item->handle_editor()) {
                $this->update();
                Platal::page()->assign('msg', 'Requête mise à jour');
                return true;
            }
            return false;
        }

        // add a comment
        if (Env::has('add_comm')) {
            if (!strlen(Env::t('comm'))) {
                return false;
            }
            $this->item->add_comment(S::user()->commentatorName(), Env::v('comm'));
            $this->item->sendmailcomment($this->writer);
            
            $this->update();
            Platal::page()->assign('msg', 'Commentaire ajouté');
            return true;
        }

        if (Env::has('accept')) {
            if ($this->commit()) {
                Platal::page()->assign('msg', 'Email de validation envoyé');
                return true;
            }
            else {
                Platal::page()->assign('msg', 'Erreur lors de la validation');
                return false;
            }
        }

        if (Env::has('delete')) {
            if (!Env::v('ans')) {
                Platal::page()->assign('msg', 'Pas de motivation pour le refus !!!');
                return false;
            } else if ($this->item->delete()) {
                $this->item->sendmailfinal(false);
                $this->clean();
                Platal::page()->assign('msg', 'Email de refus envoyé');
                return true;
            } else {
                Platal::page()->assign('msg', 'Erreur lors de la suppression des données');
                return false;
            }
        }

        return false;
    }
}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
