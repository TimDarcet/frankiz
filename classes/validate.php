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


/** 
 * class to use for validations
 */
class Validate extends Meta
{
    const SELECT_BASE = 0x01;
    const SELECT_ITEM = 0x02;

    //user asking a validation
    protected $user;
    // group that should validate
    protected $group;

    protected $type;
    protected $created;

    //item to validate : ItemValidate object
    protected $item;

    public function user()
    {
        return $this->user;
    }

    public function group()
    {
        return $this->group;
    }

    public function type()
    {
        return $this->type;
    }

    public function created()
    {
        return $this->created;
    }

    public function item()
    {
        return $this->item;
    }

    public function fillFromArray(array $values)
    {
        if (isset($values['uid'])) {
            $this->user = new User($values['uid']);
            $this->user->select(User::SELECT_BASE);
            unset($values['uid']);
        }

        if (isset($values['gid'])) {
            $this->group = new Group($values['gid']);
            $this->group->select(Group::SELECT_BASE);
            unset($values['gid']);
        }

        if (isset($values['item']) && is_string($values['item'])) {
            $this->item = unserialize($values['item']);
            unset($values['item']);
        }

        parent::fillFromArray($values);
    }

    /** 
     * to use to send the data for moderation
     * if $this->item->unique is true, then the database will be clean before
     */
    public function insert()
    {
        if(is_null($this->item))
            return;
        $this->created = date("Y-m-d H:i:s");
        
        if ($this->item->unique()) {
            XDB::execute('DELETE FROM  validate
                                WHERE  uid = {?} AND gid = {?} AND type = {?}',
                         $this->user->id(), $this->group->id(), $this->type);
        }

        XDB::execute('INSERT INTO  validate
                              SET  uid = {?}, gid = {?}, type = {?}, 
                                   item = {?}, created = {?}',
                            $this->user->id(), $this->group->id(), $this->type, 
                            $this->item, $this->created);
                           
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
                                           WHERE  uid = {?} AND gid = {?} AND type = {?}',
                                    $this->user->id(), $this->group->id(), $this->type);
        } else {
            $success =  XDB::execute('DELETE FROM  validate
                                            WHERE  id = {?}',
                                      $this->id);
        }
        return $success;
    }

    /** 
     * to validate a form
     */
    public function handle_form()
    {
        if(is_null($this->item))
            return false;

        // edit informations
        if (env::has('edit')) {
            if ($this->item->handle_editor()) {
                $this->update();
                $this->trigSuccess('Requête mise à jour');
                return true;
            }
            return false;
        }

        // add a comment
        if (env::has('add_comm')) {
            if (!strlen(env::t('comm'))) {
                return false;
            }
            $this->item->add_comment(S::user()->displayName(), env::v('comm'));
            $this->item->sendmailcomment();
            
            $this->update();
            $this->trigSuccess('Commentaire ajouté');
            return true;
        }

        if (env::has('accept')) {
            if ($this->item->commit()) {
                $this->item->sendmailfinal(true);
                $this->clean();
                $this->trigSuccess('Email de validation envoyé');
                return true;
            } else {
                $this->trigError('Erreur lors de la validation');
                return false;
            }
        }

        if (env::has('refuse')) {
            if (env::v('ans')) {
                $this->item->sendmailfinal(false);
                $this->clean();
                $this->trigSuccess('Email de refus envoyé');
                return true;
            } else {
                $this->trigError('Pas de motivation pour le refus !!!');
            }
        }

        return false;
    }

    protected function trigError($msg)
    {
        Platal::page()->trigError($msg);
    }

    protected function trigWarning($msg)
    {
        Platal::page()->trigWarning($msg);
    }

    protected function trigSuccess($msg)
    {
        Platal::page()->trigSuccess($msg);
    }

    public static function batchSelect(array $val, $fields = null)
    {
        if (empty($val))
            return;

        $val = array_combine(self::toIds($val), $val);

        $request = 'SELECT id';
        if ($fields & self::SELECT_BASE)
            $request .= ', uid, gid, type, created';
        if ($fields & self::SELECT_ITEM)
            $request .= ', item';

        $iter = XDB::iterator($request .
                        ' FROM  validate
                         WHERE  id IN {?}',
                         array_keys($val));

        while ($array_datas = $iter->next())
            $val[$array_datas['id']]->fillFromArray($array_datas);

    }
}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
