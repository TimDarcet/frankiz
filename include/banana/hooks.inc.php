<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                       *
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

require_once dirname(__FILE__) . '/../../banana/nntp.inc.php';
require_once dirname(__FILE__) . '/../../banana/message.func.inc.php';
require_once dirname(__FILE__) . '/../../banana/page.inc.php';

function hook_formatDisplayHeader($_header, $_text, $in_spool = false)
{
    switch ($_header) {
      case 'from': case 'to': case 'cc': case 'reply-to':
        $addresses = preg_split("/ *, */", $_text);
        $text = '';
        foreach ($addresses as $address) {
            $address = BananaMessage::formatFrom(trim($address), Banana::$message->getHeaderValue('subject'));
            if ($_header == 'from') {
                    return $address;
            }
            if (!empty($text)) {
                $text .= ', ';
            }
            $text .= $address;
        }
        return $text;

      case 'subject':
        $link = null;
        $text = stripslashes($_text);
        if (preg_match('/^(.+?)\s*\[=> (.*?)\]\s*$/u', $text, $matches)) {
            $text = $matches[1];
            $group = $matches[2];
            if (Banana::$group == $group) {
                $link = ' [=>&nbsp;' . $group . ']';
            } else {
                $link = ' [=>&nbsp;' . Banana::$page->makeLink(array('group' => $group, 'text' => $group)) . ']';
            }
        }
        $text = banana_catchFormats(banana_htmlentities($text));
        if ($in_spool) {
            return array($text, $link);
        }
        return $text . $link;
    }
    return null;
}

function hook_platalMessageLink($params)
{
    $base = '';
    if (isset($params['first'])) {
        return $base . '/from/' . $params['first'];
    }
    if (isset($params['artid'])) {
    	if (@$params['action'] == 'new') {
            $base .= '/reply';
        } elseif (@$params['action'] == 'cancel') {
            $base .= '/cancel';
        } elseif (@$params['part']) {
            if (strpos($params['part'], '.') !== false) {
                $params['artid'] .= '?part=' . urlencode($params['part']);
                $base = '/read';
            } else {
                $base .= '/' . str_replace('/', '.', $params['part']);
            }
        } else {
            $base .= '/read';
        }
        return $base . '/' . $params['artid'];
    }

    if (@$params['action'] == 'new') {
        return $base . '/new';
    }
    return $base;
}

function hook_makeImg($img, $alt, $height, $width)
{
    global $globals;
    $url = $globals->baseurl . '/data/banana/' . $img;

    if (!is_null($width)) {
        $width = ' width="' . $width . '"';
    }
    if (!is_null($height)) {
        $height = ' height="' . $height . '"';
    }

    return '<img src="' . $url . '"' . $height . $width . ' alt="' . $alt . '" />';
}

if (!function_exists('hook_makeLink')) {
function hook_makeLink($params)
{
    global $globals, $platal;
    $base = $globals->baseurl . '/banana';
    if (isset($params['page'])) {
        return $base . '/' . $params['page'];
    }
    if (@$params['action'] == 'subscribe') {
        return $base . '/subscribe';
    }

    if (!isset($params['group'])) {
        return $base;
    }
    $base .= '/' . $params['group'];
    $base = $base . hook_platalMessageLink($params);
    if (@$params['action'] == 'showext') {
        $base .= '?action=showext';
    }
    return $base;
}
}

function get_banana_params(array &$get, $group = null, $action = null, $artid = null)
{
    if ($group == 'forums') {
        $group = null;
    } else if ($group == 'thread') {
        $group = S::v('banana_group');
    } else if ($group == 'message') {
        $action = 'read';
        $group  = S::v('banana_group');
        $artid  = S::i('banana_artid');
    } else if ($action == 'message') {
        $action = 'read';
        $artid  = S::i('banana_artid');
    } else if ($group == 'subscribe' || $group == 'subscription') {
        $group  = null;
        $action = null;
        $get['action'] = 'subscribe';
    } else if ($group == 'profile') {
        $group  = null;
        $action = null;
        $get['action'] = 'profile';
    } else if ($group == 'updateall') {
    	$group = null;
    	$action = null;
    	$get['updateall'] = 'updateall';
    }
    if (!is_null($group)) {
        $get['group'] = $group;
    }
    if (!is_null($action)) {
        if ($action == 'new') {
            $get['action'] = 'new';
        } elseif (!is_null($artid)) {
            $get['artid'] = $artid;
            if ($action == 'reply') {
                $get['action'] = 'new';
            } elseif ($action == 'cancel') {
                $get['action'] = $action;
            } elseif ($action == 'from') {
                $get['first'] = $artid;
                unset($get['artid']);
            } elseif ($action == 'read') {
                $get['part']  = @$_GET['part'];
            } elseif ($action == 'source') {
                $get['part'] = 'source';
            } elseif ($action) {
                $get['part'] = str_replace('.', '/', $action);
            }
            if (Get::v('action') == 'showext') {
                $get['action'] = 'showext';
            }
        }
    }
}

function run_banana(&$page, $class, array $args)
{
    $banana = new $class($args);
    $page->assign('banana', $banana->run());
    $bt = $banana->backtrace();
    if ($bt) {
        new PlBacktrace(Banana::$protocole->name(), $banana->backtrace(), 'response', 'time');
    }
}

//just a simple method to send a message whithout having to create a Banana object

function send_message($group, $subject, $body)
{
	Banana::$nntp_host = 'nntp://frankiz.polytechnique.fr:119/';
	$mailer = new BananaNNTP();
	Banana::$profile['headers']['From'] = S::user()->displayName() . ' <' . S::user()->bestEmail() .'>';
	$headers = array('From' => S::user()->displayName() . ' <' . S::user()->bestEmail() .'>',
					'Subject' => $subject,
					'Newsgroups' => $group);
	$message = BananaMessage::newMessage($headers, $body);
	echo $mailer->send($message);
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
