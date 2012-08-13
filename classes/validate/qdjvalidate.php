<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
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

class QDJValidate extends ItemValidate
{
    protected $type = 'qdj';
    protected $question;
    protected $answer1;
    protected $answer2;
    protected $writer;

    public function __construct($question, $answer1, $answer2)
    {
        $this->question = $question;
        $this->answer1  = $answer1;
        $this->answer2  = $answer2;
        $this->writer   = S::user();
    }

    public function objects() {
        return Array('writer' => UserSelect::base());
    }

    public static function label() {
        return "QDJ";
    }

    public function question()
    {
        return $this->question;
    }

    public function answer1()
    {
        return $this->answer1;
    }

    public function answer2()
    {
        return $this->answer2;
    }

    public function show()
    {
        return 'validate/form.show.qdj.tpl';
    }

    public function editor()
    {
        return 'validate/form.edit.qdj.tpl';
    }

    public function handle_editor()
    {
        $this->question  = Env::t('question', '');
        $this->answer1   = Env::t('answer1', '');
        $this->answer2   = Env::t('answer2', '');
        return true;
    }

    public function sendmailadmin()
    {
        $mail = new FrankizMailer('validate/mail.admin.qdj.tpl');
        $mail->assign('user', $this->writer->displayName());

        $mail->subject("[Frankiz] Validation d'une QDJ");
        $mail->setFrom($this->writer->bestEmail(), $this->writer->displayName());
        $mail->addAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->send(false);
    }

    public function sendmailfinal($valid)
    {
        $mail = new FrankizMailer('validate/mail.valid.qdj.tpl');

        $mail->assign('valid', $valid);
        $mail->assign('comm', Env::s('ans', ''));

        if ($valid)
            $mail->Subject = '[Frankiz] Ta QDJ a été acceptée';
        else
            $mail->Subject = '[Frankiz] Ta QDJ a été refusée';

        $mail->setFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->addAddress($this->writer->bestEmail(), $this->writer->displayName());
        $mail->addCC($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->send(false);
    }

    public function _mail_from_disp()
    {
        return 'Le QDJmestre';
    }

    public function _mail_from_addr()
    {
        return 'qdj@frankiz.polytechnique.fr';
    }

    public function commit()
    {
        $qdj = new QDJ(array('question' => $this->question,
                              'answer1' => $this->answer1,
                              'answer2' => $this->answer2,
                               'writer' => $this->writer));
        $qdj->insert();
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
