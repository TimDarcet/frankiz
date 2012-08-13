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

/**
 * Mail management class
 *
 * Use cases :
 *
 *  // Html mail, set in-line
 *  $m = new FrankizMailer();
 *  $m->addAddress('riton@melix.net');
 *  $m->setFrom('robot@frankiz.net', 'Robot Frankiz');
 *  $m->subject('This is an automated message');
 *  $m->body("This mail is in HTML <br><br> I can use html tags !");
 *  $m->send();
 *  --------------
 *
 *  // Text mail, set in-line
 *  $m = new FrankizMailer();
 *  $m->body("This mail is in plain text \n\n I can't use html tags !");
 *  $m->send(false); // Don't forget to send the mail in text mode
 *  --------------
 *
 *  // Text mail, set in a template
 *  $m = new FrankizMailer('mail.default.tpl');
 *  $m->assign('body', 'This is a spam');
 *  $m->send();
 *
 * ------------------------------
 * Important signatures of the parent Class
 *
 * addAddress($address, $name = '')
 * addCC($address, $name = '')
 * addBCC($address, $name = '')
 * addReplyTo($address, $name = '')
 * setFrom($address, $name = '',$auto=1)
 *
 */
class FrankizMailer extends PHPMailer
{

    protected $tpl  = null;
    protected $page = null;

    protected $To;
    protected $Cc;

    /**
    * @param $tpl Template to be used to generate the mail
    */
    public function __construct($tpl = null)
    {
        global $globals;

        $this->CharSet = 'utf-8';

        if ($globals->debug & DEBUG_BT) {
            if (!isset(PlBacktrace::$bt['Mails']))
                new PlBacktrace('Mails');
        }

        $this->tpl = (empty($tpl)) ? 'mail.default.tpl' : $tpl;
        $this->page = new Smarty();

        $this->CharSet  = "UTF-8";
        $this->WordWrap = 80;

        $this->page->caching       = false;
        $this->page->compile_check = true;
        $this->page->template_dir  = $globals->spoolroot . "/templates/";
        $this->page->compile_dir   = $globals->spoolroot . "/spool/mails_c/";
        $this->page->config_dir    = $globals->spoolroot . "/configs/";
        array_unshift($this->page->plugins_dir, $globals->spoolroot."/plugins/");
    }

    public function addAddress($address, $name = '')
    {
        global $globals;

        if (!empty($globals->mails->debug)) {
            return parent::addAddress($globals->mails->debug, $name . '(debug: ' . $address . ')');
        }
        return parent::addAddress($address, $name);
    }

    public function addCC($address, $name = '')
    {
        global $globals;

        if (!empty($globals->mails->debug)) {
            return parent::AddCC($globals->mails->debug, $name . '(debug: ' . $address . ')');
        }
        return parent::AddCC($address, $name);
    }

    public function toUserFilter(UserFilter $uf)
    {
        $this->To = $uf;
    }

    /**
    * Assign a variable in the chosen template
    *
    * @param $var
    * @param $value
    */
    public function assign($var, $value)
    {
        $this->page->assign($var, $value);
    }

    /**
    * If you don't use a template, you can set here the content of the mail
    *
    * @param $content
    */
    public function body($content)
    {
        $this->assign('body', $content);
    }

    /**
    * Set or Get the subject (ie. the title)
    *
    * @param $subject
    */
    public function subject($subject = null)
    {
        if ($subject !== null)
            $this->Subject = $subject;

        return $this->Subject;
    }

    /**
    * Send the mail. Returns the sent content
    *
    * @param $html Boolean specifying if the mail should be send in html or not
    */
    public function send($html = true)
    {
        global $globals, $platal;

        $this->page->assign('isHTML', $html);

        $this->page->assign_by_ref('platal', $platal);
        $this->page->assign_by_ref('globals', $globals);
        $tpl = FrankizPage::getTplPath($this->tpl);
        $content = $this->page->fetch($tpl);

        if ($html) {
            $this->MsgHTML($content);
        } else {
            $this->Body = trim($content);
        }

        if ($globals->debug & DEBUG_BT) {
            PlBacktrace::$bt['Mails']->start($this->Subject);
        }

        parent::send();

        if ($globals->debug & DEBUG_BT) {
            $datas = array(array('from' => $this->From, 'body' => $this->Body, 'AltBody' => $this->AltBody));
            PlBacktrace::$bt['Mails']->stop(round(strlen($this->Body) / 64), null, $datas);
        }

        return $content;
    }

    public function sendLater($html = true)
    {
        global $globals;

        $this->page->assign('isHTML', $html);

        $tpl = FrankizPage::getTplPath($this->tpl);
        $content = $this->page->fetch($tpl);
        XDB::execute('INSERT INTO  mails
                              SET  target = {?}, writer = {?}, writername = {?}, title = {?}, body = {?}, ishtml = {?}',
                              json_encode($this->To->export()), $this->From, $this->FromName,
                              $this->Subject, trim($content), $html);
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
