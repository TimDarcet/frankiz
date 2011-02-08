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

class TolValidate extends ItemValidate
{
    protected $type = 'tol';

    protected $user;
    protected $image;

    protected $unique = true;

    public function objects() {
        return Array('user' => userSelect::base(), 'image' => FrankizImageSelect::base());
    }

    public static function label() {
        return 'Changement de photo';
    }

    public function __construct(FrankizImage $image)
    {
        $this->image = $image;
        parent::__construct();
    }

    public function image(FrankizImage $image = null)
    {
        if ($image != null)
        {
            $this->image = $image;
        }
        return $this->image;
    }

    public function show()
    {
        return 'validate/form.show.tol.tpl';
    }

    public function handle_editor()
    {
        return false;
    }

    public function sendmailadmin()
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(UserSelect::base());

        $mail = new FrankizMailer('validate/mail.admin.tol.tpl');
        $mail->assign('user', $this->user->displayName());

        $mail->subject("[Frankiz] Validation d'une annonce");
        $mail->SetFrom($this->user->bestEmail(), $this->user->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function sendmailfinal($isok)
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(UserSelect::base());

        $mail = new FrankizMailer('validate/mail.valid.tol.tpl');
        if (Env::has("ans"))
            $mail->assign('comm', Env::v('ans'));

        if ($isok)
            $mail->Subject = '[Frankiz] Ta photo tol a été validée';
        else
        {
            $mail->Subject = '[Frankiz] Ta photo tol a été refusée';
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
        return 'web@polytechnique.edu';
    }

    public function commit()
    {
        $this->user->photo($this->image);
        return true;
    }

    public function delete()
    {
        $this->image->delete();
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
