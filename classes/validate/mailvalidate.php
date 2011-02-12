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
    protected $writer;
    protected $target;
    protected $subject;
    protected $body;
    protected $nowiki;

    public function __construct(Caste $target, $subject, $body, $nowiki)
    {
        $this->writer = S::user();
        $this->target = $target;
        $this->subject = $subject;
        $this->body = $body;
        $this->nowiki = $nowiki;
    }

    public function objects() {
        return Array('writer' => UserSelect::base(), 'target' => CasteSelect::validate());
    }
    
    public function writer(User $writer = null) {
        if ($writer != null) {
            $this->writer = $writer;
        }
        return $this->writer;
    }

    public function target(Caste $target = null)
    {
        if ($target != null)
        {
            $this->target = $target;
        }
        return $this->target;
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
        $this->target->group();
        return ($this->target->group()->mail() === false || $this->target->group()->mail() === '')
                ?'web@frankiz.polytechnique.fr':$this->target->group()->mail();
    }

    public function commit()
    {
        $mail = new FrankizMailer();
        $mail->subject('[Mail groupé] ' . $this->subject);
        $mail->body($this->body);
        $mail->setFrom($this->writer->bestEmail(), $this->writer->displayName());
        $mail->ToUserFilter(new UserFilter(new UFC_Caste($this->target->id())));
        $mail->sendLater($this->nowiki);
        return true;
    }
}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
