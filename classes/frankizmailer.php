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

require_once('smarty/libs/Smarty.class.php');
require_once('../include/class.phpmailer.php');

class FrankizMailer extends PHPMailer
{
    protected $tpl  = null;
    protected $page = null;
    
    public function __construct($tpl = null)
    {
        global $globals;

        $this->tpl  = $tpl;
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

    public function assign($var, $value)
    {
        $this->page->assign($var, $value);
    }
    
    public function Send($html = true)
    {
        $this->page->assign('isHTML', $html);

        if ($html)
            $this->MsgHTML($this->page->fetch(FrankizPage::getTplPath($this->tpl)));
        else
            $this->Body = trim($this->page->fetch(FrankizPage::getTplPath($this->tpl)));

        parent::Send();
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
