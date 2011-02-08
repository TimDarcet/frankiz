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

class ActivityValidate extends ItemValidate
{
    protected $rules = 'Seule les activités ponctuelles doivent passer par la validation. Si ce n\'est pas le cas, il faut créer l\'activité régulière qui va bien.';
    protected $type = 'activity';
    protected $writer;
    protected $target;
    protected $origin;
    protected $title;
    protected $description;
    protected $begin;
    protected $end;

    public function __construct(User $writer, Caste $target, $title, $desc, $begin, $end, $origin = false) {
        $this->writer = $writer;
        $this->target = $target;
        if (!is_null($origin))
            $this->origin = $origin;
        $this->title = $title;
        $this->description = $desc;
        $this->begin = $begin;
        $this->end = $end;

        parent::__construct();
    }

    public function objects() {
        return array('user' => 'User',
                   'writer' => 'User',
                   'target' => 'Caste',
                   'origin' => 'Group');
    }

    public function writer(User $writer = null) {
        if ($writer !== null) {
            $this->writer = $writer;
        }
        return $this->writer;
    }

    public function target(Caste $g = null) {
        if($g !== null) {
            $this->target = $g;
        }
        return $this->target;
    }

    public function origin(Group $g = null) {
        if($g !== null) {
            $this->origin = $g;
        }
        return $this->origin;
    }

    public function title() {
        return $this->title;
    }

    public function description() {
        return $this->description;
    }

    public function begin() {
        return $this->begin;
    }

    public function end() {
        return $this->end;
    }

    public static function label() {
        return 'Validation d\'activité';
    }

    public function show() {
        return 'validate/form.show.activity.tpl';
    }

    public function editor() {
        return 'validate/form.edit.activity.tpl';
    }

    public function handle_editor() {
        $this->title        = Env::t('title', '');
        $this->description  = Env::t('description', '');
        try {
            $this->begin = new FrankizDateTime(Env::t('begin', $this->begin));
            $this->end = new FrankizDateTime(Env::t('end', $this->end));
        }
        catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function sendmailadmin() {
        $mail = new FrankizMailer('validate/mail.admin.activity.tpl');
        $mail->assign('user', $this->user->displayName());
        $mail->assign('title', $this->title);

        $mail->subject("[Frankiz] Validation d'une activité");
        $mail->SetFrom($this->writer->bestEmail(), $this->writer->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function sendmailfinal($isok) {
        $mail = new FrankizMailer('validate/mail.valid.activity.tpl');
        $mail->assign('isok', $isok);
        if (Env::has("ans"))
            $mail->assign('comm', Env::v('ans'));

        if ($isok)
            $mail->Subject = '[Frankiz] Ton activité a été validée';
        else
            $mail->Subject = '[Frankiz] Ton activité a été refusée';

        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($this->writer->bestEmail(), $this->writer->displayName());
        $mail->AddCC($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function _mail_from_disp() {
        return 'Les webmestres';
    }

    public function _mail_from_addr() {
        $this->target->group()->select(GroupSelect::see());
        return ($this->target->group()->mail() === false || $this->target->group()->mail() === '')
                ?'web@frankiz.polytechnique.fr':$this->target->group()->mail();
    }

    public function commit() {
        $a = new Activity(array(
                'target'        => $this->target,
                'origin'        => $this->origin,
                'title'         => $this->title,
                'description'   => $this->description,
                'days'          => ''));
        $a->insert();
        $ai = new ActivityInstance(array(
                'activity'      => $a,
                'writer'        => $this->writer,
                'comment'       => '',
                'begin'         => $this->begin,
                'end'           => $this->end));
        $ai->insert();
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>