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


/*
 * item for a Validate object
 */
abstract class ItemValidate
{
    //comments on the validation : each entry is an array including the name of the admin and the comment
    protected $comments = array();
    
    // the validations rules : comments for admins
    protected $rules = "Mieux vaut laisser une demande de validation à un autre admin que de valider une requête illégale ou que de refuser une demande légitime.";

    // if the request can be multiple
    protected $unique = false;
    // enable the refuse button
    protected $refuse = true;
    
    protected $user;
    protected $type;
    
    public function user()
    {
        return $this->user;
    }
    
    public function type()
    {
        return $this->type;
    }
    
    public function comments()
    {
        return $this->comments;
    }
    
    public function unique()
    {
        return $this->unique;
    }
    
    public function refuse()
    {
        return $this->refuse;
    }

    public function rules()
    {
        return $this->rules;
    }
    
    public function __construct()
    {
        $this->user = S::user();
    }
    
    public function sendmailcomment() 
    {
        if (is_null($this->user->displayName()))
            $this->user->select(User::SELECT_BASE);
    
        $mail = new FrankizMailer('validate/mail.comment.tpl');
        $mail->assign('type', $this->type);
        $mail->assign('user', $this->user->displayName());
        if (Env::has('comm'))
            $mail->assign('comm', Env::v('comm'));
            
        $mail->Subject = "Commentaires de validation {$this->type}";
        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }
    
    public function add_comment(String $name, String $comment)
    {
        $this->comments[] = array('name'=>$name, 'com'=>$comment);
    }


    /*
     * to send emails
     * sendmailadmin : mail to send when the Validate item is inserted
     * sendmailfinal : to send when the item is accepted ($isok = true) or rejected ($isok = false)
     * _mail_from_disp : name to display for mails
     * _mail_from_addr : address to use for mails
     */
    abstract public function sendmailadmin();

    abstract public function sendmailfinal($isok);

    abstract public function _mail_from_disp();
    
    abstract public function _mail_from_addr();
    
    abstract public function handle_editor();

    /** 
     * to insert datas 
     */
    abstract public function commit();

    /**
     *  name of the template that contains the form
     */
    abstract public function show();

    /** 
     * name of the editor
     */
    public function editor()
    {
        return null;
    }
}


class NewsValidate extends ItemValidate
{
    protected $rules = 'Une annonce ne doit pas être trop longue et respecter les règles typographiques.' ;
    protected $type = 'news';
    protected $news;

    public function __construct(News $news)
    {
        $this->news = $news;
        parent::__construct();
    }

    public function news()
    {
        return $this->news;
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
        $this->news->end(Env::t('end', $this->news->end()));
        if(substr($this->news->end(), 4, 1) != '-')
        {
            $this->news->end(substr_replace($this->news->end(), '-', 6, 0));
            $this->news->end(substr_replace($this->news->end(), '-', 4, 0));
        }
        $this->news->priv(Env::has('priv')?1:0);    
        $this->news->important(Env::has('important')?1:0);
        return true;
    }

    public function sendmailadmin()
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);
        
        $mail = new FrankizMailer('validate/mail.admin.news.tpl');
        $mail->assign('user', $this->user->displayName());
        $mail->assign('title', $this->news->title());
    
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


class ActivityValidate extends ItemValidate
{
    protected $rules = 'Seule les activités ponctuelles doivent passer par la validation. Si ce n\'est pas le cas, il faut créer l\'activité régulière qui va bien.';
    protected $type = 'activity';
    protected $writer;
    protected $target;
    protected $title;
    protected $description;
    protected $date;
    protected $begin;
    protected $end;
    protected $priv;

    public function __construct(User $writer, Group $target, String $title,
        String $desc, String $date, String $begin, String $end, Boolean $priv)
    {
        $this->writer = $writer;
        $this->target = $target;
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
        $this->date         = Env::t('date', $this->date);
        $this->begin        = Env::t('begin', $this->begin);
        $this->end          = Env::t('end', $this->end);
        $this->priv         = Env::has('priv');
    
        if(substr($this->date, 4, 1) != '-')
        {
            $this->date = substr_replace($this->date, '-', 6, 0);
            $this->date = substr_replace($this->date, '-', 4, 0);
        }
        
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
        $begin = $this->date . ' ' . $this->begin . ':00';
        $end = $this->date . ' ' . $this->end . ':00';
        $a = new Activity(array(
                'target'        => $this->target->id(),
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

class MailValidate extends ItemValidate
{
    protected $type = 'mail';
    protected $group;
    protected $subject;
    protected $body;
    protected $nowiki;
    
    public function __construct(Group $group, String $subject, String $body, $nowiki)
    {
        
        parent::__construct();
        
        $this->group = $group;
        $this->subject = $subject;
        $this->body = $body;
        $this->nowiki = $nowiki;
    }

    public function group()
    {
        return $this->group;
    }
    
    public function subject()
    {
        return $this->subject;
    }
    
    public function body()
    {
        return $this->body;
    }
    public function nowiki()
    {
        return $this->nowiki;
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
        $this->body = Env::t('body');
        return true;
    }
    
    public function sendmailadmin()
    {
        print_r($this->user);
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);
        
        $mail = new FrankizMailer('validate/mail.admin.mail.tpl');
        $mail->assign('user', $this->user->displayName());
        $mail->assign('subject', $this->subject);
    
        $mail->Subject = "[Frankiz] Validation d'un mail";
        $mail->SetFrom($this->user->bestEmail(), $this->user->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }
    
    public function sendmailfinal($isok)
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.valid.mail.tpl');
        if (Env::has("ans"))
            $mail->assign('comm', Env::v('ans'));
    
        if ($isok)
            $mail->Subject = '[Frankiz] Ton mail a été accepté';
        else
        {
            $mail->Subject = '[Frankiz] Ton mail a été refusé';
            $mail->assign('text', $this->body);
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
        $this->group->select(Group::SELECT_USER);
        foreach($this->group->users() as $user)
        {
            $mail = new FrankizMailer();
            $mail->subject('[Mail groupé] ' . $this->subject);
            $mail->body($this->body);
            $mail->setFrom($this->user->bestEmail(), $this->user->displayName());
            $mail->AddAddress($user, $user->displayName());
            $mail->send($this->nowiki);
        }
        return true;
    }    
}


class QDJValidate extends ItemValidate
{
    protected $type = 'qdj';
    protected $question;
    protected $answer1;
    protected $answer2;

    public function __construct(String $question, String $answer1, String $answer2)
    {
        $this->question  = $question;
        $this->answer1   = $answer1;
        $this->answer2   = $answer2;
        parent::__construct();
    }

    public function question()
    {
        return $this->question;
    }
    
    public function answer1()
    {
        return $this->answer1;
    }
    
    public function answer2()
    {
        return $this->answer2;
    }
    
    public function show()
    {
        return 'validate/form.show.qdj.tpl';
    }

    public function editor()
    {
        return 'validate/form.edit.qdj.tpl';
    }
    
    public function handle_editor()
    {
        $this->question  = Env::t('question', '');
        $this->answer1   = Env::t('answer1', '');
        $this->answer2   = Env::t('answer2', '');
        return true;
    }

    public function sendmailadmin()
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);
        
        $mail = new FrankizMailer('validate/mail.admin.qdj.tpl');
        $mail->assign('user', $this->user->displayName());
    
        $mail->subject("[Frankiz] Validation d'une QDJ");
        $mail->SetFrom($this->user->bestEmail(), $this->user->displayName());
        $mail->AddAddress($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }
    
    public function sendmailfinal($isok)
    {
        if (is_null($this->user->bestEmail()))
            $this->user->select(User::SELECT_BASE);

        $mail = new FrankizMailer('validate/mail.valid.qdj.tpl');
        $mail->assign('isok', $isok);
        if (Env::has("ans"))
            $mail->assign('comm', Env::v('ans'));
    
        if ($isok)
            $mail->Subject = '[Frankiz] Ta QDJ a été acceptée';
        else
            $mail->Subject = '[Frankiz] Ta QDJ a été refusée';
          
        $mail->SetFrom($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->AddAddress($this->user->bestEmail(), $this->user->displayName());
        $mail->AddCC($this->_mail_from_addr(), $this->_mail_from_disp());
        $mail->Send(false);
    }    

    public function _mail_from_disp()
    {
        return 'Le QDJmestre';
    }
     
    public function _mail_from_addr()
    {
        return 'brice.gelineau@polytechnique.edu';
    }
    
    public function commit()
    {
        XDB::execute('INSERT INTO  qdj
                              SET  question = {?}, answer1 = {?}, answer2 = {?}',
                    $this->question, $this->answer1, $this->answer2);
        return true;
    }
    
}

/* vim: set expandtab shiftwidth=4 tabstop=4 softtabstop=4 foldmethod=marker enc=utf-8: */
?>
