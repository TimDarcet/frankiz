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

class QDJValidate extends ItemValidate
{
    protected $type = 'qdj';
    protected $question;
    protected $answer1;
    protected $answer2;

    public function __construct(String $question, String $answer1, String $answer2)
    {
        $this->question  = $question;
        $this->answer1   = $answer1;
        $this->answer2   = $answer2;
        parent::__construct();
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
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.admin.qdj.tpl');
        $mail->assign('user', $this->user->displayName());

        $mail->subject("[Frankiz] Validation d'une QDJ");
        $mail->SetFrom($this->user->bestEmail(), $this->user->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function sendmailfinal($isok)
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.valid.qdj.tpl');
        $mail->assign('isok', $isok);
        if (Env::has("ans"))
            $mail->assign('comm', Env::v('ans'));

        if ($isok)
            $mail->Subject = '[Frankiz] Ta QDJ a été acceptée';
        else
            $mail->Subject = '[Frankiz] Ta QDJ a été refusée';

        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($this->user->bestEmail(), $this->user->displayName());
        $mail->AddCC($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function _mail_from_disp()
    {
        return 'Le QDJmestre';
    }

    public function _mail_from_addr()
    {
        return 'brice.gelineau@polytechnique.edu';
    }

    public function commit()
    {
        XDB::execute('INSERT INTO  qdj
                              SET  question = {?}, answer1 = {?}, answer2 = {?}',
                    $this->question, $this->answer1, $this->answer2);
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
