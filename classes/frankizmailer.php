<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                        *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                      *
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

require_once('../include/class.phpmailer.php');

class FrankizMailer extends PHPMailer
{
    /*
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
     */


    /* Important signatures of the parent Class
     *
     * addAddress($address, $name = '')
     * addCC($address, $name = '')
     * addBCC($address, $name = '')
     * addReplyTo($address, $name = '')
     * setFrom($address, $name = '',$auto=1)
     *
     */

    protected $tpl  = null;
    protected $page = null;

    /**
    * @param $tpl Template to be used to generate the mail
    */
    public function __construct($tpl = null)
    {
        global $globals;

        $this->tpl = (empty($this->tpl)) ? 'mail.default.tpl' : $tpl;
        $this->page = new Smarty();

        $this->CharSet  = "UTF-8";
        $this->WordWrap = 80;

        $this->page->caching       = false;
        $this->page->compile_check = true;
        $this->page->template_dir  = $globals->spoolroot . "/templates/";
        $this->page->compile_dir   = $globals->spoolroot . "/spool/mails_c/";
        $this->page->config_dir    = $globals->spoolroot . "/configs/";
        array_unshift($this->page->plugins_dir, $globals->spoolroot."/plugins/");
        $this->assign('globals', $globals);
    }

    /**
    * Assign a variable in the chosen template
    *
    * @param $var
    * @param $avalue
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
        $this->page->assign('isHTML', $html);

        $tpl = FrankizPage::getTplPath($this->tpl);
        $content = $this->page->fetch($tpl);

        if ($html)
            $this->MsgHTML($content);
        else
            $this->Body = trim($content);

        parent::send();

        return $content;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
