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

class MailValidate extends ItemValidate
{
    protected $type = 'mail';
    protected $group;
    protected $subject;
    protected $body;
    protected $nowiki;

    public function __construct(Group $group, String $subject, String $body, $nowiki)
    {

        parent::__construct();

        $this->group = $group;
        $this->subject = $subject;
        $this->body = $body;
        $this->nowiki = $nowiki;
    }

    public function group()
    {
        return $this->group;
    }

    public function subject()
    {
        return $this->subject;
    }

    public function body()
    {
        return $this->body;
    }
    public function nowiki()
    {
        return $this->nowiki;
    }

    public function show()
    {
        return 'validate/form.show.mail.tpl';
    }

    public function editor()
    {
        return 'validate/form.edit.mail.tpl';
    }

    public function handle_editor()
    {
        $this->subject = Env::t('subject');
        $this->body = Env::t('body');
        return true;
    }

    public function sendmailadmin()
    {
        print_r($this->user);
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.admin.mail.tpl');
        $mail->assign('user', $this->user->displayName());
        $mail->assign('subject', $this->subject);

        $mail->Subject = "[Frankiz] Validation d'un mail";
        $mail->SetFrom($this->user->bestEmail(), $this->user->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function sendmailfinal($isok)
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.valid.mail.tpl');
        if (Env::has("ans"))
            $mail->assign('comm', Env::v('ans'));

        if ($isok)
            $mail->Subject = '[Frankiz] Ton mail a été accepté';
        else
        {
            $mail->Subject = '[Frankiz] Ton mail a été refusé';
            $mail->assign('text', $this->body);
        }

        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($this->user->bestEmail(), $this->user->displayName());
        $mail->AddCC($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function _mail_from_disp()
    {
        return 'Les webmestres';
    }

    public function _mail_from_addr()
    {
        return 'brice.gelineau@polytechnique.edu';
    }

    public function commit()
    {
        $this->group->select(Group::SELECT_USER);
        foreach($this->group->users() as $user)
        {
            $mail = new FrankizMailer();
            $mail->subject('[Mail groupé] ' . $this->subject);
            $mail->body($this->body);
            $mail->setFrom($this->user->bestEmail(), $this->user->displayName());
            $mail->AddAddress($user, $user->displayName());
            $mail->send($this->nowiki);
        }
        return true;
    }
}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>