<?php
/***************************************************************************
 *  Copyright (C) 2004-2013 Binet Réseau                                   *
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

class MemberValidate extends ItemValidate
{
    protected $type = 'member';

    protected $user;
    protected $group;

    protected $unique = true;

    public function objects() {
        return Array('user' => userSelect::base(), 'group' => GroupSelect::validate() );
    }

    public static function label() {
        return 'Membre';
    }

    public function __construct(User $user, $group)
    {
        $this->user = $user;
        $this->group = $group;
    }
    
    public function show()
    {
        return false;
    }

    public function handle_editor()
    {
        return false;
    }

    public function sendmailadmin()
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(UserSelect::base());

        $mail = new FrankizMailer('validate/mail.admin.member.tpl');
        $mail->assign('user', $this->user);
        $mail->assign('targetGroup', $this->group);

        $mail->subject("[Frankiz] Demande de membre");
        $mail->SetFrom($this->user->bestEmail(), $this->user->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function sendmailfinal($isok)
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(UserSelect::base());

        $mail = new FrankizMailer('validate/mail.valid.member.tpl');
        if (Env::has("ans"))
            $mail->assign('comm', Env::v('ans'));

        $mail->assign('isOk', $isok);
        $mail->assign('targetGroup', $this->group);

        if ($isok) {
            $mail->Subject = '[Frankiz] Ta demande de membre a été acceptée';
        } else {
            $mail->Subject = '[Frankiz] Ta demande de membre a été refusée';
        }

        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($this->user->bestEmail(), $this->user->displayName());
        $mail->AddCC($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function _mail_from_disp()
    {
        return 'Frankiz - ' . $this->group->label() . '';
    }

    public function _mail_from_addr()
    {
        global $globals;

        return $this->group->name() .'@' . $globals->mails->group_suffix;
    }

    public function commit()
    {
        $mem = $this->group->caste(Rights::member());
        $fri = $this->group->caste(Rights::friend());
        if ($mem->userfilter()) {
            return false;
        }
        $mem->addUser($this->user);
        $fri->removeUser($this->user);
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
