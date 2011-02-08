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

class NewsValidate extends ItemValidate
{
    protected $type = 'news';

    protected $writer;
    protected $target;
    protected $image;
    protected $origin;
    protected $title;
    protected $content;
    protected $begin;
    protected $end;
    protected $comment;

    public function __construct($datas)
    {
        foreach ($datas as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
        parent::__construct();
    }

    public function objects() {
        return Array('writer' => UserSelect::base(), 'target' => CasteSelect::group(),
                     'image' => FrankizImageSelect::base(), 'origin' => GroupSelect::base());
    }

    public static function label() {
        return 'Annonce';
    }

    public function show()
    {
        return 'validate/form.show.news.tpl';
    }

    public function editor()
    {
        return 'validate/form.edit.news.tpl';
    }

    public function handle_editor()
    {
        $this->news->title(Env::t('title', ''));
        $this->news->content(Env::t('content', ''));
        try
        {
            $this->news->end(new FrankizDateTime(Env::t('end', $this->news->end())));
        }
        catch (Exception $e)
        {
            return false;
        }
        $this->news->important(Env::has('important')?1:0);
        return true;
    }

    public function sendmailadmin()
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(UserSelect::base());

        $mail = new FrankizMailer('validate/mail.admin.news.tpl');
        $mail->assign('user', $this->user->displayName());
        $mail->assign('title', $this->title);

        $mail->subject("[Frankiz] Validation d'une annonce");
        $mail->SetFrom($this->user->bestEmail(), $this->user->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function sendmailfinal($isok)
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.valid.news.tpl');
        if (Env::has("ans"))
            $mail->assign('comm', Env::v('ans'));

        if ($isok)
            $mail->Subject = '[Frankiz] Ton annonce a été validée';
        else
        {
            $mail->Subject = '[Frankiz] Ton annonce a été refusée';
            $mail->assign('text', $this->news->content());
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
        $this->news->replace();
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
