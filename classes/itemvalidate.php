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


/*
 * item for a Validate object
 */
abstract class ItemValidate
{
    //comments on the validation : each entry is an array including the name of the admin and the comment
    protected $comments = array();

    // if the request can be multiple
    protected $unique = false;
    // enable the refuse button
    protected $refuse = true;

    protected $type;

    // returns an array with the attributes to construct as objects when retrieving from database
    // works only for meta object
    // must be name => class
    public function objects() {
        return array();
    }

    public function __call($method, $arguments)
    {
        if (!property_exists($this, $method)) {
            throw new Exception("$method is not a property of " . get_class($this));
        }

        if (!empty($arguments)) {
            $this->$method = $arguments[0];
        }

        return $this->$method;
    }

    // Prepares the item to be inserted in a Db
    // WARNING : this destroys the objects
    public function toDb(){
        foreach ($this->objects() as $name => $object) {
            if ($this->$name != false)
                $this->$name = $this->$name->id();
        }
    }

    public static function label() {
        return 'Inconnue';
    }

    public function type()
    {
        return $this->type;
    }

    public function comments()
    {
        return $this->comments;
    }

    public function unique()
    {
        return $this->unique;
    }

    public function refuse()
    {
        return $this->refuse;
    }

    public function sendmailcomment($user) 
    {    
        $mail = new FrankizMailer('validate/mail.comment.tpl');
        $mail->assign('type', $this->type);
        $mail->assign('user', $user->displayName());
        if (Env::has('comm'))
            $mail->assign('comm', Env::v('comm'));
            
        $mail->Subject = "Commentaires de validation de type \"{$this->label()}\"";
        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function add_comment($name, $comment)
    {
        $this->comments[] = array('name'=>$name, 'com'=>$comment);
    }

    /*
     * to send emails
     * sendmailadmin : mail to send when the Validate item is inserted
     * sendmailfinal : to send when the item is accepted ($isok = true) or rejected ($isok = false)
     * _mail_from_disp : name to display for mails
     * _mail_from_addr : address to use for mails
     */
    abstract public function sendmailadmin();

    abstract public function sendmailfinal($isok);

    abstract public function _mail_from_disp();
    
    abstract public function _mail_from_addr();
    
    abstract public function handle_editor();

    /** 
     * to insert datas 
     */
    abstract public function commit();

    /**
     * if there are datas to delete
     */
    public function delete()
    {
        return true;
    }

    /**
     *  name of the template that contains the form
     */
    abstract public function show();

    /** 
     * name of the editor
     */
    public function editor()
    {
        return null;
    }
}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
