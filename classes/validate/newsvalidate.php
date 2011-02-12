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
    }

    public function objects() {
        return Array('writer' => UserSelect::base(), 'target' => CasteSelect::validate(),
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
        $this->title = Env::t('title', '');
        $this->content = Env::t('news_content', '');
        $this->begin = new FrankizDateTime(Env::t('begin'));
        $this->end = new FrankizDateTime(Env::t('end'));
        if (Env::has('image'))
            $this->image   = new FrankizImage(Env::i('image'));

        return true;
    }

    public function sendmailadmin()
    {
        if ($this->writer->bestEmail() === null)
            $this->writer->select(UserSelect::base());

        $mail = new FrankizMailer('validate/mail.admin.news.tpl');
        $mail->assign('user', $this->writer->displayName());
        $mail->assign('title', $this->title);

        $mail->subject("[Frankiz] Validation d'une annonce");
        $mail->SetFrom($this->writer->bestEmail(), $this->writer->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function sendmailfinal($isok)
    {
        if ($this->writer->bestEmail() === null)
            $this->writer->select(UserSelect::base());

        $mail = new FrankizMailer('validate/mail.valid.news.tpl');
        if (Env::has("ans")) {
            $mail->assign('comm', Env::v('ans'));
        }

        $mail->assign('text', false);
        $mail->assign('group_label', $this->target->group()->label());

        if ($isok) {
            $mail->Subject = '[Frankiz] Ton annonce a été validée';
        } else {
            $mail->Subject = '[Frankiz] Ton annonce a été refusée';
            $mail->assign('text', $this->content());
        }

        $mail->setFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->addAddress($this->writer->bestEmail(), $this->writer->displayName());
        $mail->addCC($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->send(false);
    }

    public function _mail_from_disp()
    {
        return 'Frankiz / "' . $this->target->group()->label() . '"';
    }

    public function _mail_from_addr()
    {
        return ($this->target->group()->mail() === false || $this->target->group()->mail() === '')
                ?'web@frankiz.polytechnique.fr':$this->target->group()->mail();
    }

    public function commit()
    {
        $n = new News();
        $n->insert();
        $n->writer($this->writer);
        $n->target($this->target);
        $n->image($this->image);
        $n->origin($this->origin);
        $n->title($this->title);
        $n->content($this->content);
        $n->begin($this->begin);
        $n->end($this->end);
        $n->comment($this->comment);
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
