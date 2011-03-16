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
    protected $type_mail;
    protected $origin;
    protected $targets;
    protected $subject;
    protected $body;
    protected $nowiki;
    protected $formation;

    public function __construct($datas)
    {
        foreach ($datas as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }


    public function objects() {
        return array('writer' => UserSelect::base(),
                  'formation' => GroupSelect::base(),
                     'origin' => GroupSelect::base());
    }

    public function collections() {
        return array('targets' => CasteSelect::validate());
    }

    public static function label() {
        return 'Courriel';
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
        $this->body = Env::t('mail_body');
        return true;
    }

    public function sendmailadmin()
    {
        $mail = new FrankizMailer('validate/mail.admin.mail.tpl');
        $mail->assign('user', $this->writer);
        $mail->assign('subject', $this->subject);
        $mail->assign('targetGroup', $this->formation);

        $mail->Subject = "[Frankiz] Validation d'un mail";
        $mail->SetFrom($this->writer->bestEmail(), $this->writer->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function sendmailfinal($isok)
    {
        $mail = new FrankizMailer('validate/mail.valid.mail.tpl');
        $mail->assign('comm', Env::v('ans', ''));
        $mail->assign('targetGroup', $this->formation);

        if ($isok)
            $mail->Subject = '[Frankiz] Ton mail a été accepté';
        else
        {
            $mail->Subject = '[Frankiz] Ton mail a été refusé';
            $mail->assign('text', $this->body);
        }

        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($this->writer->bestEmail(), $this->writer->displayName());
        $mail->AddCC($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function _mail_from_disp()
    {
        return 'Frankiz - ' . $this->formation->label() . '';
    }

    public function _mail_from_addr()
    {
        global $globals;

        return $this->formation->name() .'@' . $globals->mails->group_suffix;
    }

    public function commit()
    {
        $mail = new FrankizMailer();
        $sub = ($this->type_mail == 'promo')?'promo':$this->formation->label();
        $mail->subject('[Mail ' . $sub . '] ' . $this->subject);

        if ($this->origin) {
            global $globals;
            $mail->setFrom($this->origin->name() .'@' . $globals->mails->group_suffix,
                    'Frankiz - ' . $this->origin->label() . '');
        }
        else {
            $mail->setFrom($this->writer->bestEmail(), $this->writer->displayName());
        }
        
        if ($this->type_mail == 'promo' && !$this->targets) {
            $uf = new UserFilter(
                new PFC_AND(
                    new UFC_Group($this->formation),
                    new UFC_Group(Group::from('on_platal'))));
        }
        else if ($this->type_mail == 'promo') {
            $uf = new UserFilter(
                new PFC_AND(
                    new UFC_Group($this->formation),
                    new UFC_Caste($this->targets),
                    new UFC_Group(Group::from('on_platal'))));
        }
        else {
            $uf = new UserFilter(
                new PFC_AND(
                    new UFC_Caste($this->targets->first()),
                    new UFC_Group(Group::from('on_platal'))));
        }
        
        if (!$this->nowiki) {
            $mail->body(MiniWiki::wikiToHTML($this->body, false));
        }
        else {
            $mail->body(MiniWiki::wikiToText($this->body, false, 0, 80));
        }

        $mail->ToUserFilter($uf);
        $mail->sendLater(!$this->nowiki);
        return true;
    }
}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
