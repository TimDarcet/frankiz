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
    protected $date;
    protected $begin;
    protected $end;
    protected $priv;

    public function __construct(User $writer, Group $target, String $title,
        String $desc, FrankizDateTime $date, String $begin, String $end, Boolean $priv, $origin = null)
    {
        $this->writer = $writer;
        $this->target = $target;
        if (!is_null($origin))
            $this->origin = $origin;
        $this->title = $title;
        $this->description = $desc;
        $this->date = $date;
        $this->begin = $begin;
        $this->end = $end;
        $this->priv = $priv;

        parent::__construct();
    }

    public function writer()
    {
        return $this->writer;
    }

    public function target()
    {
        return $this->target;
    }

    public function origin()
    {
        return $this->origin;
    }

    public function title()
    {
        return $this->title;
    }

    public function description()
    {
        return $this->description;
    }

    public function date()
    {
        return $this->date;
    }

    public function begin()
    {
        return $this->begin;
    }

    public function end()
    {
        return $this->end;
    }

    public function priv()
    {
        return $this->priv;
    }

    public function show()
    {
        return 'validate/form.show.activity.tpl';
    }

    public function editor()
    {
        return 'validate/form.edit.activity.tpl';
    }

    public function handle_editor()
    {
        $this->title        = Env::t('title', '');
        $this->description  = Env::t('description', '');
        try
        {
            $this->date = new FrankizDateTime(Env::t('date', $this->date));
        }
        catch (Exception $e)
        {
            return false;
        }
        $default_begin = Env::t('begin', $this->begin);
        $default_end = Env::t('end', $this->end);
        if (!(preg_match( '`^\d{2}:\d{2}$`' , $default_begin) && strtotime($default_begin) !== false
                        && preg_match( '`^\d{2}:\d{2}$`' , $default_end) && strtotime($default_end) !== false))
            return false;

        $this->begin        = $default_begin;
        $this->end          = $default_end;
        $this->priv         = Env::has('priv');

        return true;
    }

    public function sendmailadmin()
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.admin.activity.tpl');
        $mail->assign('user', $this->user->displayName());
        $mail->assign('title', $this->title);

        $mail->subject("[Frankiz] Validation d'une activité");
        $mail->SetFrom($this->user->bestEmail(), $this->user->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function sendmailfinal($isok)
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.valid.activity.tpl');
        $mail->assign('isok', $isok);
        if (Env::has("ans"))
            $mail->assign('comm', Env::v('ans'));

        if ($isok)
            $mail->Subject = '[Frankiz] Ton activité a été validée';
        else
            $mail->Subject = '[Frankiz] Ton activité a été refusée';

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
        $begin = new FrankizDateTime($this->date->format('Y-m-d') . ' ' . $this->begin . ':00');
        $end = new FrankizDatetime($this->date->format('Y-m-d') . ' ' . $this->end . ':00');
        $a = new Activity(array(
                'target'        => $this->target,
                'origin'        => $this->origin,
                'title'         => $this->title,
                'description'   => $this->description,
                'days'          => '',
                'priv'          => $this->priv));
        $a->replace();
        $ai = new ActivityInstance(array(
                'aid'           => $a->id(),
                'writer'        => $this->writer->id(),
                'comment'       => '',
                'begin'         => $begin,
                'end'           => $end));
        $ai->replace();
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>