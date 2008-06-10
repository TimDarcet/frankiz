<?php
/***************************************************************************
 *  Copyright (C) 2003-2007 Polytechnique.org                              *
 *  http://opensource.polytechnique.org/                                   *
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

$TIME_BEGIN = microtime(true);

define('PERMS_EXT',     'ext');
define('PERMS_INTERNE', 'interne');
define('PERMS_USER',    'user');

define('NO_AUTH', 0);
define('DO_AUTH', 1);
define('NO_HTTPS', 2);

define('DEBUG_BT', 1);
define('DEBUG_VALID', 2);
define('DEBUG_SMARTY', 4);

function pl_error_handler($errno, $errstr, $errfile, $errline)
{
    static $errortype;
    if (!error_reporting())
        return;

    if (!isset($errortype)) {
        $errortype = array (
            E_ERROR           => "Error",
            E_WARNING         => "Warning",
            E_PARSE           => "Parsing Error",
            E_NOTICE          => "Notice",
            E_CORE_ERROR      => "Core Error",
            E_CORE_WARNING    => "Core Warning",
            E_COMPILE_ERROR   => "Compile Error",
            E_COMPILE_WARNING => "Compile Warning",
            E_USER_ERROR      => "User Error",
            E_USER_WARNING    => "User Warning",
            E_USER_NOTICE     => "User Notice",
            E_STRICT          => "Runtime Notice"
        );
    }

    global $globals;
    if (isset($globals) && !$globals->debug) {
        if ($errno == E_NOTICE || $errno == E_USER_NOTICE || $errno == E_STRICT) {
            return;
        }
    }

    $errstr = utf8_encode(htmlentities($errstr));
    $GLOBALS['pl_errors'][] =
        "<div class='phperror'>".
        "<strong>{$errortype[$errno]}</strong> <em>$errstr</em><br />".
        "<tt>$errfile : $errline</tt>".
        "</div>";
}

function pl_clear_errors()
{
    unset($GLOBALS['pl_errors']);
}

function pl_dump_env()
{
    echo "<div class='phperror'><pre>";
    echo "\nSESSION: "; var_export($_SESSION);
    echo "\nPOST:    "; var_export($_POST);
    echo "\nGET:     "; var_export($_GET);
    echo "\nCOOKIE:  "; var_export($_COOKIE);
    echo "</pre></div>";
}

function pl_print_errors()
{
    if (!empty($GLOBALS['pl_errors'])) {
        print join("\n", $GLOBALS['pl_errors']);
    }
}

set_error_handler('pl_error_handler', E_ALL | E_STRICT);
register_shutdown_function('pl_print_errors');
// register_shutdown_function('pl_dump_env');

/** Check if the string is utf8 
 */
function is_utf8($s)
{
    return @iconv('utf-8', 'utf-8', $s) == $s;
}

/** vérifie si une adresse email est bien formatée  * ATTENTION, cette fonction ne doit pas être appelée sur une chaîne ayant subit un addslashes (car elle accepte le "'" qui it alors un "\'" 
 * @param $email l'adresse email a verifier 
 * @return BOOL  */
function isvalid_email($email) 
{
    // la rfc2822 authorise les caractères "a-z", "0-9", "!", "#", "$", "%", "&", "'", "*", "+", "-", "/", "=", "?", "^",  `", "{", "|", "}", "~" aussi bien dans la partie locale que dans le domaine. 
    // Pour la partie locale, on réduit cet ensemble car il n'est pas utilisé. 
    // Pour le domaine, le système DNS limite à [a-z0-9.-], on y ajoute le "_" car il est parfois utilisé. 
    return preg_match("/^[a-z0-9_.'+-]+@[a-z0-9._-]+\.[a-z]{2,4}$/i", $email); 
}

function pl_url($path, $query = null, $fragment = null)
{
    global $platal;

    $base = $platal->ns . $path . ($query ? '?'.$query : '');
    return $fragment ? $base.'#'.$fragment : $base;
}

function pl_self($n = null) {
    global $platal;
    return $platal->pl_self($n);
}

function http_redirect($fullurl)
{
    if (count($_SESSION)) {
        session_write_close();
    }
    header('Location: '.$fullurl);
    exit;
}

function pl_redirect($path, $query = null, $fragment = null)
{
    global $globals;
    http_redirect($globals->baseurl . '/' . pl_url($path, $query, $fragment));
}

function pl_entities($text, $mode = ENT_COMPAT)
{
    return htmlentities($text, $mode, 'UTF-8');
}

function pl_entity_decode($text, $mode = ENT_COMPAT)
{
    return html_entity_decode($text, $mode, 'UTF-8');
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
