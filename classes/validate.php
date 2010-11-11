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
class Validate extends meta
{
    const SELECT_INFOS = 0x01;
    const SELECT_ITEM = 0x02;
    
    
    //user asking a validation
    public $user;
    // group that should validate
    public $gid;
    public $type;
    public $created;

    //item to validate : ItemValidate object
    public $item;
    
    public static function batchSelect(array $val, $fields)
    {
        if (empty($val))
            return;

        $val = array_combine(self::toIds($val), $val);
            
        $request = 'SELECT id';
        if ($fields & SELECT_INFOS)
            $request .= ', user, gid, type, created';
        if ($fields & SELECT_ITEM)
            $request .= ', item';
        
        $iter = XDB::iterator($request .
                        ' FROM  validate
                         WHERE  id IN {?}',
                         array_keys($val));
                         
        while ($array_datas = $iter->next())
        {
            $val[$array_datas['id']]->fillFromArray($array_datas);
            if ($fields & SELECT_INFOS)
                $val[$array_datas['id']]->user = unserialize($val[$array_datas['id']]->user);
            if ($fields & SELECT_ITEM)
                $val[$array_datas['id']]->item = unserialize($val[$array_datas['id']]->item);
            
        }
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
        
        if ($this->item->unique) {
            XDB::execute('DELETE FROM  validate
                                WHERE  user = {?} AND gid = {?} AND type = {?}',
                         $this->user, $this->gid, $this->type);
        }

        XDB::execute('INSERT INTO  validate
                              SET  user = {?}, gid = {?}, type = {?}, 
                                   item = {?}, created = {?}',
                            $this->user, $this->gid, $this->type, 
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
        if(!is_null($this->item))
            return;
        if ($this->unique) {
            $success = XDB::execute('DELETE FROM  validate
                                           WHERE  user = {?} AND gid = {?} AND type = {?}',
                                    $this->user, $this->gid, $this->type);
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
        if(!is_null($this->item))
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
            $this->item->comments[] = Array(S::user()->login(), env::v('comm'));
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

    /** automatic answers table for this type of validation */
    public function answers()
    {
        static $answers_table;
        if (!isset($answers_table[$this->type])) {
            $r = XDB::query("SELECT id, title, answer FROM requests_answers WHERE category = {?} AND gid = {?}", $this->type, $this->gid);
            $answers_table[$this->type] = $r->fetchAllAssoc();
        }
        return $answers_table[$this->type];
    }

    public function ruleText()
    {
        return str_replace('\'', '\\\'', $this->rules);
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
