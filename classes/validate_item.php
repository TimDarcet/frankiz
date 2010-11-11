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


/*
 * item for a Validate object
 */
abstract class Validate_Item
{
    //comments on the validation : each entry is an array including the name of the admin and the comment
    public $comments = array();
    
    // the validations rules : comments for admins
    public $rules = "Mieux vaut laisser une demande de validation à un autre admin que de valider une requête illégale ou que de refuser une demande légitime";

    // if the request can be multiple
    public $unique = false;
    // enable the refuse button
    public $refuse = true;
    
    public $uid;
    

    public function __construct(News $news)
    {
        $this->uid = s::user()->id();
        parent::construct();
    }
    
    public function sendmailcomment() {
        $user = new User($this->uid);
        $user->select(User::SELECT_BASE);
    
        $mail = new FrankizMailer('validate/mail.comment.tpl');
        $mail->assign('type', $this->type);
        $mail->assign('user', $user->displayName());
        if (env::has('comm'))
            $mail->assign('comm', env::v('comm'));
            
        $mail->Subject = "Commentaires de validation {$this->type}";
        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $maim->Send(false);
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


class Validate_News extends Validate_Item
{
    public $type = 'news';
    public $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }
    
    public function show()
    {
        //return 'validate/form.show.essai.tpl';
    }

    public function editor()
    {
        //return 'validate/form.edit.essai.tpl';
    }
    
    // TO DO
    public function handle_editor()
    {
        //$this->subject = env::t('subject');
        //$this->body = env::t('body');
        return true;
    }

    public function sendmailadmin()
    {
        $user = new User($this->uid);
        $user->select(User::SELECT_BASE);
        
        $mail = new FrankizMailer('validate/mail.admin.news.tpl');
        $mail->assign('user', $user->displayName());
        $mail->assign('title', $this->news->title);
    
        $mail->Subject = "[Frankiz] Validation d'une annonce";
        $mail->SetFrom($user->bestEmail(), $user->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }
    
    public function sendmailfinal($isok)
    {
        $user = new User($this->uid);
        $user->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.valid.news.tpl');
        if (Env::has("ans"))
            $mail->assign('comm', Env::v('ans'));
    
        if ($isok)
            $mail->Subject = '[Frankiz] Ton annonce a été validée';
        else
        {
            $mail->Subject = '[Frankiz] Ton annonce a été refusée';
            $mail->assign('text', $this->news->title);
        }
          
        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($user->bestEmail(), $user->displayName());
        $mail->AddCC($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }    

    public function _mail_from_disp()
    {
        return 'Les webmestres';
    }
     
    public function _mail_from_addr()
    {
        return 'web@frankiz.polytechnique.fr';
    }
    
    public function commit()
    {
        $this->news->replace();
        return true;
    }
    
}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
