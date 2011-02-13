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

class LicensesValidate extends ItemValidate
{
    protected $type = 'licenses';
    protected $software;
    protected $reason;
    protected $writer;

    public function __construct($software, $reason)
    {
        $this->software  = $software;
        $this->reason = $reason;
        $this->writer = S::user();
    }
    
    public static function label()
    {
        return 'Attribution d\'une licence MSDNAA';
    }
    
    public function objects() {
        return Array('writer' => UserSelect::base());
    }
    
    public function softwareName()
    {
        $s =  License::getSoftwares();
        return $s[$this->software];
    }

    public function software()
    {
        return $this->software;
    }

    public function reason()
    {
        return $this->reason;
    }

    public function show()
    {
        return 'validate/form.show.licenses.tpl';
    }

    public function editor()
    {
        return 'validate/form.edit.licenses.tpl';
    }

    public function handle_editor()
    {
        $this->reason   = Env::t('reason', '');
        return true;
    }

    public function sendmailadmin()
    {
        if (is_null($this->writer->bestEmail()))
            $this->writer->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.admin.licenses.tpl');
        $mail->assign('user', $this->writer->displayName());
        $mail->assign('software_name', $this->softwareName());

        $mail->subject("[Frankiz] Validation d'une demande de licence");
        $mail->SetFrom($this->writer->bestEmail(), $this->writer->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function sendmailfinal($isok)
    {
        if (is_null($this->writer->bestEmail()))
            $this->writer->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.valid.licenses.tpl');
        $mail->assign('isok', $isok);
        $mail->assign('software_name', $this->softwareName());
        if (Env::has("ans"))
            $mail->assign('comm', Env::v('ans'));

        if ($isok)
            $mail->Subject = '[Frankiz] Ta demance de licence a été acceptée';
        else
            $mail->Subject = '[Frankiz] Ta demance de licence a été refusée';

        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($this->writer->bestEmail(), $this->writer->displayName());
        $mail->AddCC($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function _mail_from_disp()
    {
        return 'admin@windows';
    }

    public function _mail_from_addr()
    {
        return 'msdnaa-licences@frankiz.polytechnique.fr';
    }

    public function commit()
    {
        $free_keys = License::fetch(array('software' => $this->software, 'uid' => null, 'admin' => false));
        if(count($free_keys) == 0){
            return false;
        }
        $key = array_pop($free_keys);
        $key->uid($this->uid);
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
