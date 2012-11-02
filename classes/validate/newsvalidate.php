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

class NewsValidate extends ItemValidate
{
    protected $type = 'news';

    protected $writer;
    protected $target;
    protected $image;
    // either a group on behalf of wich the news is posted or false
    protected $origin;
    protected $title;
    protected $content;
    protected $begin;
    protected $end;
    protected $comment;

    // if the admin of the group origin must validate the news
    protected $valid_origin = false;

    protected $idIfValid;

    public function __construct($datas)
    {
        foreach ($datas as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }

    public function objects() {
        return Array('writer' => UserSelect::base(),
                     'target' => CasteSelect::validate(),
                      'image' => FrankizImageSelect::base(),
                     'origin' => GroupSelect::base());
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
        $this->title   = Env::t('title', '');
        $this->content = Env::t('news_content', '');
        $this->begin   = new FrankizDateTime(Env::t('begin'));
        $this->end     = new FrankizDateTime(Env::t('end'));
        if (Env::has('image')) {
            $image = new ImageFilter(new PFC_And(new IFC_Id(Env::i('image')), new IFC_Temp()));
            $image = $image->get(true);
            if (!$image) {
                throw new Exception("This image doesn't exist anymore");
            }
            $image->select(FrankizImageSelect::caste());
            $image->label($this->title);
            $image->caste($this->target);
            $this->image($image);
        }

        return true;
    }

    public function sendmailadmin()
    {
        if ($this->writer->bestEmail() === null)
            $this->writer->select(UserSelect::base());

        $mail = new FrankizMailer('validate/mail.admin.news.tpl');
        $mail->assign('valid_origin', $this->valid_origin);
        $mail->assign('user', $this->writer);
        $mail->assign('title', $this->title);
        $mail->assign('targetGroup', $this->target->group());

        if ($this->valid_origin) {
            $mail->assign('origin', $this->origin);
            $mail->subject("[Frankiz] Validation d'un groupe d'origine");
        }
        else {
            $mail->subject("[Frankiz] Validation d'une annonce");
        }
        $mail->setFrom($this->writer->bestEmail(), $this->writer->displayName());
        $mail->addAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->send(false);
    }
    
    /** Sends the news items to the newsgroups via mail.
     */
    public function sendnewsgroupmail()
    {
        global $globals;

        if ($this->target->group()->ns() == Group::NS_BINET) {
            $mail = new FrankizMailer();

            $suffix = $globals->mails->newsgroup_suffix;
            if (!$suffix)
                $suffix = 'news.eleves.polytechnique.fr';
            $mail->addAddress('br.binet.' . $this->target->group()->name() . '@' . $suffix);

            if ($this->origin)
                $mail->setFrom($this->origin->name() .'@' . $globals->mails->group_suffix, $this->origin->label());
            else
                $mail->setFrom($this->writer->bestEmail(), $this->writer->displayName());

            $mail->subject('[Frankiz] ' . $this->title);
            $mail->body(MiniWiki::wikiToText($this->content, false, 0, 80));
            $mail->send(false);
        }
        //But what else
    }

    public function sendmailfinal($valid)
    {
        if ($this->writer->bestEmail() === null)
            $this->writer->select(UserSelect::base());

        $mail = new FrankizMailer('validate/mail.valid.news.tpl');

        $mail->assign('valid_origin', $this->valid_origin);
        $mail->assign('valid', $valid);
        $mail->assign('comm', Env::v('ans', ''));
        $mail->assign('text', false);
        $mail->assign('targetGroup', $this->target->group());
        $mail->assign('origin', $this->origin);

        if ($valid && !$this->valid_origin) {
            $mail->Subject = '[Frankiz] Ton annonce a été validée';
            $mail->assign('idIfValid', $this->idIfValid);
        } elseif ($valid){
            $mail->Subject = '[Frankiz] Le groupe d\'origine de ton annonce a été validé';
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
        if ($this->valid_origin) {
            return 'Frankiz - ' . $this->origin->label() . '';
        }
        else {
            return 'Frankiz - ' . $this->target->group()->label() . '';
        }
    }

    public function _mail_from_addr()
    {
        global $globals;

        if ($this->valid_origin) {
            return $this->origin->name() .'@' . $globals->mails->group_suffix;
        }
        else {
            return $this->target->group()->name() .'@' . $globals->mails->group_suffix;
        }
    }

    public function delete()
    {
        if ($this->image !== false) {
            $this->image->delete();
        }
        return true;
    }

    public function commit()
    {
        if ($this->valid_origin) {
            $nv = new NewsValidate(array(
                'writer'    => $this->writer,
                'target'    => $this->target,
                'image'     => $this->image,
                'origin'    => $this->origin,
                'title'     => $this->title,
                'content'   => $this->content,
                'begin'     => $this->begin,
                'end'       => $this->end,
                'comment'   => $this->comment));
            $v = new Validate(array(
                'writer'  => $this->writer,
                'group'   => $this->target->group(),
                'item'    => $nv,
                'type'    => 'news'));
            $v->insert();
        }
        else {
            $n = new News(array(
                    'writer'  => $this->writer,
                    'target'  => $this->target,
                    'image'   => $this->image,
                    'origin'  => $this->origin,
                    'title'   => $this->title,
                    'content' => $this->content,
                    'begin'   => $this->begin,
                    'end'     => $this->end,
                    'comment' => $this->comment));
            $n->insert();
            $this->idIfValid = $n->id();

            // This code is used to post news on a newsgroup server
            //if ($this->target->rights()->isMe(Rights::everybody())) {
            //    $this->sendnewsgroupmail();
            //}
        }
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
