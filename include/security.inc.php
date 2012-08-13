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

/******************************************************************************
 * Security functions
 *****************************************************************************/

function hash_compare($hash, $password) {
    return crypt($password, $hash) != $hash;
}

function hash_encrypt($string) {
        $hash = '';
        for($i=0;$i<8;$i++){
                $j = mt_rand(0,53);
                if($j<26)
                        $hash .= chr(rand(65,90));
                else if($j<52)
                        $hash .= chr(rand(97,122));
                else if($j<53)
                        $hash .= '.';
                else $hash .= '/';
        }
        return crypt($string,'$6$'.$hash.'$');
}

/*
function check_ip($level)
{
    if (empty($_SERVER['REMOTE_ADDR'])) {
        return false;
    }
    if (empty($_SESSION['check_ip'])) {
        $ips = array();
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        }
        $ips[] = $_SERVER['REMOTE_ADDR'];
        foreach ($ips as &$ip) {
            $ip = '(ip & mask) = (' . ip_to_uint($ip) . '& mask)';
        }
        $res = XDB::query('SELECT  state, description
                             FROM  ip_watch
                            WHERE  ' . implode(' OR ', $ips) . '
                         ORDER BY  state DESC');
        if ($res->numRows()) {
            $state = $res->fetchOneAssoc();
            $_SESSION['check_ip'] = $state['state'];
            $_SESSION['check_ip_desc'] = $state['description'];
        } else {
            $_SESSION['check_ip'] = 'safe';
        }
    }
    $test = array();
    switch ($level) {
      case 'unsafe': $test[] = 'unsafe';
      case 'dangerous': $test[] = 'dangerous';
      case 'ban': $test[] = 'ban'; break;
      default: return false;
    }
    return in_array($_SESSION['check_ip'], $test);
}
*/
/*
function check_email($email, $message)
{
    $res = XDB::query("SELECT state, description
        FROM emails_watch
        WHERE state != 'safe' AND email = {?}", $email);
    if ($res->numRows()) {
        send_warning_mail($message);
        return true;
    }
    return false;
}
*/
/*
function check_account()
{
    return S::v('watch_account');
}
*/

/*
function send_warning_mail($title)
{
    global $globals;
    $mailer = new PlMailer();
    $mailer->setFrom("webmaster@" . $globals->mail->domain);
    $mailer->addTo($globals->core->admin_email);
    $mailer->setSubject("[Plat/al Security Alert] $title");
    $mailer->setTxtBody("Identifiants de session :\n" . var_export($_SESSION, true) . "\n\n"
        ."Identifiants de connexion :\n" . var_export($_SERVER, true));
    $mailer->send();
}
*/
/*
function kill_sessions()
{
    assert(S::has_perms());
    shell_exec('sudo -u root ' . dirname(dirname(__FILE__)) . '/bin/kill_sessions.sh');
}
*/

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
