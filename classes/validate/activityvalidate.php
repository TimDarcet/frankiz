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

class ActivityValidate extends ItemValidate
{
    protected $type = 'activity';

    protected $writer;
    protected $target;
    protected $origin = false;
    protected $title;
    protected $description;
    protected $begin;
    protected $end;

    protected $valid_origin = false;

    public function __construct($datas)
    {
        foreach ($datas as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }

    public function objects()
    {
        return array('writer' => UserSelect::base(),
                     'target' => CasteSelect::validate(),
                     'origin' => GroupSelect::base());
    }

    public static function label()
    {
        return 'Activité';
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
        // Webmasters can change targets of activities
	$user = S::user();
	if($user && $user->isWeb()){
	    $target_rights = Rights::restricted();
	    if(Env::b('target_everybody_activity')){
	        $target_rights = Rights::everybody();
	    }
	    $target_group = new Group(Env::i('target_group_activity'));
	    $target_filter = new CasteFilter(new PFC_And(new CFC_Group($target_group),
	                                                 new CFC_Rights($target_rights)));
	    $target = $target_filter->get(true);
	    if (!$this->target->isMe($target)){
	        $target->select(CasteSelect::validate());
		$this->target = $target;
	    }
	}

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

    public function sendmailadmin()
    {
        if ($this->writer->bestEmail() === null)
            $this->writer->select(UserSelect::base());
        
        $mail = new FrankizMailer('validate/mail.admin.activity.tpl');
        $mail->assign('valid_origin', $this->valid_origin);
        $mail->assign('user', $this->writer);
        $mail->assign('title', $this->title);
        $mail->assign('targetGroup', $this->target->group());

        if ($this->valid_origin) {
            $mail->assign('origin', $this->origin);
            $mail->subject("[Frankiz] Validation d'un groupe d'origine");
        }
        else {
            $mail->subject("[Frankiz] Validation d'une activité");
        }

        $mail->SetFrom($this->writer->bestEmail(), $this->writer->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }

    public function sendmailfinal($isok)
    {
        if ($this->writer->bestEmail() === null)
            $this->writer->select(UserSelect::base());

        $mail = new FrankizMailer('validate/mail.valid.activity.tpl');
        $mail->assign('isok', $isok);
        $mail->assign('valid_origin', $this->valid_origin);
        $mail->assign('comm', Env::v('ans', ''));
        $mail->assign('targetGroup', $this->target->group());
        $mail->assign('origin', $this->origin);

        if ($isok && !$this->valid_origin) {
            $mail->Subject = '[Frankiz] Ton activité a été validée';
        } elseif ($isok){
            $mail->Subject = '[Frankiz] Le groupe d\'origine de ton activité a été validé';
        } else {
            $mail->Subject = '[Frankiz] Ton activité a été refusée';
        }

        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($this->writer->bestEmail(), $this->writer->displayName());
        $mail->AddCC($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
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

    public function commit() {
        if ($this->valid_origin) {
            $av = new ActivityValidate(array(
                'writer'        => $this->writer,
                'target'        => $this->target,
                'title'         => $this->title,
                'description'   => $this->description,
                'begin'         => $this->begin,
                'end'           => $this->end,
                'origin'        => $this->origin));
            $v = new Validate(array(
                'writer'    => $this->writer,
                'group'     => $this->target->group(),
                'item'      => $av,
                'type'      => 'activity'));
            $v->insert();
        }
        else {
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
        }
        return true;
    }

}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
