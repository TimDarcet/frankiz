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
    protected $rules = 'La nouvelle image doit être reconnaissable (lunettes de soleil déconseillées).';
    protected $type = 'tol';
    protected $image;

    protected $unique = true;

    public function __construct(FrankizImage $image)
    {
        $this->image = $image->id();
        parent::__construct();
    }

    public function image()
    {
        $img = new FrankizImage($this->image);
        $img->select(FrankizImage::SELECT_BASE);
        return $img;
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
            $this->user->select(User::SELECT_BASE);

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
            $this->user->select(User::SELECT_BASE);

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
        return 'brice.gelineau@polytechnique.edu';
    }

    public function commit()
    {
        $img = new FrankizImage($this->image);
        $this->user->photo($img);
        return true;
    }

    public function delete()
    {
        $img = new FrankizImage($this->image);
        $img->delete();
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>