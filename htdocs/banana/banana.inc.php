<?php
/********************************************************************************
* install.d/config.inc.php : configuration file
* --------------------------
*
* This file is part of the banana distribution
* Copyright: See COPYING files that comes with this distribution
********************************************************************************/

class Banana
{
    var $maxspool  = 3000;

    var $hdecode   = array('from','name','organization','subject');
    var $parse_hdr = array('content-transfer-encoding', 'content-type', 'date', 'followup-to', 'from',
            'message-id', 'newsgroups', 'organization', 'references', 'subject', 'x-face');
    var $show_hdr  = array('from', 'subject', 'newsgroups', 'followup', 'date', 'organization', 'references', 'x-face');


    var $tbefore   = 5;
    var $tafter    = 5;
    var $tmax      = 50;

    var $wrap      = 74;

    var $custom    = "Content-Type: text/plain; charset='UTF-8'\nMime-Version: 1.0\nContent-Transfer-Encoding: 8bit\nUser-Agent: Banana 1.0 The Bearded Release\n";

    var $host      = 'news://frankiz:119/';

    var $profile ;    
    var $state = Array('group' => null, 'artid' => null);
    var $nntp;
    var $groups;
    var $newgroups;
    var $post;
    var $spool;

    function Banana()
    {
        $nom = $_SESSION['user']->nom;
        $prenom = $_SESSION['user']->prenom;
	$uid = $_SESSION['user']->uid;
	$pseudo;
	$mail;
	$login;

	global $DB_trombino;
	$DB_trombino->query("SELECT surnom, login, mail FROM eleves WHERE eleve_id = $uid");
	list($pseudo, $login, $mail) = $DB_trombino->next_row();
	if(is_null($pseudo)) {
		$peudo = "$prenom $nom";
	}
	if(is_null($mail)) {
		$mail = "$login@poly.polytechnique.fr";
	}
        $this->profile   = Array( 'name' => "$pseudo <$mail>",
                        'sig'  => "$pseudo",
                        'org'  => 'BR',
                        'customhdr' =>'',
                        'display' => 0,
                        'lastnews' => 0,
                        'locale'  => 'fr_FR',
                        'subscribe' => array());

	$this->_require('NetNNTP');
        setlocale(LC_ALL,  $this->profile['locale']);
        $this->nntp = new nntp($this->host);
    }

    function run($class = 'Banana')
    {
        global $banana;
        Banana::_require('misc');
        $banana = new $class();

        if (!$banana->nntp) {
            return '<warning>Impossible de contacter le serveur</warning>';
        }

        $group  = empty($_GET['group']) ? null : strtolower($_GET['group']);
        $artid  = empty($_GET['artid']) ? null : strtolower($_GET['artid']);
        $banana->state = Array ('group' => $group, 'artid' => $artid);

        if (is_null($group)) {

            if (isset($_GET['subscribe'])) {
                return $banana->action_listSubs();
            } elseif (isset($_POST['subscribe'])) {
                $banana->action_saveSubs();
            }
            return $banana->action_listGroups();

        } elseif (is_null($artid)) {
            
            if (isset($_REQUEST['submit'])) {
	       	return $banana->action_doFup($group, isset($_POST['artid']) ? intval($_POST['artid']) : -1);
            } elseif (isset($_REQUEST['new']) || (isset($_GET['action']) && $_GET['action']=='new')) {
                return $banana->action_newFup($group);
            } else {
                return $banana->action_showThread($group, isset($_GET['first']) ? intval($_GET['first']) : 1);
            }

        } else {

            if (isset($_REQUEST['cancel'])) {
                $res = $banana->action_cancelArticle($group, $artid);
            } else {
                $res = '';
            }

            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'cancel':
                        $res .= $banana->action_showArticle($group, $artid);
                        if ($banana->post->checkcancel()) {
       /*                     $form = '<warning>Voulez-vous vraiment annuler ce message ?</warning>'
				  ."<form action=\"?group=$group&amp;artid=$artid\" method='post'><p>"
                                  . '<input type="hidden" name="action" value="cancel" />'
                                  . '<input type="submit" value="Annuler !" />'
                                  . '</p></form>';*/
                            return $form.$res;
                        }
                        return $res;

                    case 'new':
                        return $banana->action_newFup($group, $artid);
                }
            }
            return $res . $banana->action_showArticle($group, $artid);
        }
    }

    /**************************************************************************/
    /* actions                                                                */
    /**************************************************************************/

    function action_saveSubs()
    {
        return;
    }

    function action_listGroups()
    {
        $this->_newGroup();
        
        $cuts = displayshortcuts();
        $res  = '<h1>Les forums de Banana</h1>';
	$res .= $cuts;
	$res .= $this->groups->to_html();
        if (count($this->newgroups->overview)) {
            $res .= '<p>Les forums suivants ont ete crees depuis ton dernier passage :</p>';
            $res .= $this->newgroups->to_html();
        }

        $this->nntp->quit();
        return $res.$cuts;
    }

    function action_listSubs()
    {
        $this->_require('groups');
        $this->groups = new BananaGroups(BANANA_GROUP_ALL);
        
        $cuts = displayshortcuts();
        $res  = '<h1>Abonnements</h1>'.$cuts.$this->groups->to_html(true).$cuts;

        $this->nntp->quit();
        return $res;
    }

    function action_showThread($group, $first)
    {
        $this->_newSpool($group, $this->profile['display'], $this->profile['lastnews']);

        if ($first > count($this->spool->overview)) {
            $first = count($this->spool->overview);
        }

        $first = $first - ($first % $this->tmax) + 1;

        $cuts = displayshortcuts($first);
        
        $res  = '<h1>'.$group.'</h1>'.$cuts;
        $res  .= $this->spool->to_html($first, $first+$this->tmax);

        $this->nntp->quit();
        
        return $res.$cuts;
    }

    function action_showArticle($group, $id)
    {
        $this->_newSpool($group, $this->profile['display'], $this->profile['lastnews']);
        $this->_newPost($id);
        if (!$this->post) {
            if ($this->nntp->lasterrorcode == "423") {
                $this->spool->delid($id);
            }
            $this->nntp->quit();
            return displayshortcuts().'<warning>Impossible d\'acceder au message.   Le message a peut-etre annule</warning>';
        }

        $cuts = displayshortcuts();
        $res  = '<h1>Message</h1>'.$cuts;
        $res .= $this->post->to_html();

        $this->nntp->quit();
        
	return $res.$cuts;
    }

    function action_cancelArticle($group, $id)
    {
        $this->_newSpool($group, $this->profile['display'], $this->profile['lastnews']);
        $this->_newPost($id);
        $mid  = array_search($id, $this->spool->ids);

        if (!$this->post->checkcancel()) {
            return '<warning>Vous n\'avez pas les permissions pour annuler ce message</warning>'; 
        }
        $msg = 'From: '.$this->profile['name']."\n"
             . "Newsgroups: $group\n"
             . "Subject: cmsg $mid\n"
             . $this->custom
             . "Control: cancel $mid\n"
             . "\n"
             . "Message canceled with Banana";
        if ($this->nntp->post($msg)) {
            $this->spool->delid($id);
            $this->nntp->quit();
            header("Location: ?group=$group&amp;first=$id");
        } else {
            return '<warning>Impossible d\'annuler le message</warning>';
        }
    }

    function action_newFup($group, $id = -1)
    {
        $subject = $body = '';
        $target  = $group;
        
        if ($id > 0) {
            $this->nntp->group($group);
            $this->_newPost($id);
            if ($this->post) {
                $subject = preg_replace("/^(re\s*:\s*)?/i", 'Re: ', $this->post->headers['subject']);
                $body    = $this->post->name." a ecrit :\n".wrap($this->post->body, "> ");
                $target  = isset($this->post->headers['followup-to']) ? $this->post->headers['followup-to'] : $this->post->headers['newsgroups'];
            }
        }

        $this->nntp->quit();

        $cuts  = displayshortcuts();
        $html  = '<h1>Nouveau message</h1>'.$cuts;
	$html .= "<formulaire id=\"nvmsg\" titre=\"Nouveau Message\" action=\"banana.php?group=$group\">\n";
	$html .= "\t<champ id=\"nom\" titre=\"Nom\" valeur=\"".htmlentities($this->profile['name'])."\" modificable = \"non\"/>\n";
	$html .= "\t<champ id=\"subject\" titre=\"Sujet\" valeur=\"".htmlentities($subject)."\"/>\n";
	$html .= "\t<champ id=\"forum\" titre=\"Forums\" valeur=\"".htmlentities($target)."\"/>\n";
	$html .= "\t<champ id=\"fup\" titre=\"Suivi a\" valeur=\"\"/>\n";
	$html .= "\t<champ id=\"orga\" titre=\"Organisation\" valeur=\"".$this->profile['org']."\"/>\n";
	if($id > 0) {
	    $html .= "\t<champ id=\"artid\" titre=\"Reference\" valeur=\"$id\"/>\n";
	}	
	$html .= "\t<zonetext id=\"body\" titre=\"Texte du message\">".htmlentities($body).($this->profile['sig'] ? "\n\n-- \n".htmlentities($this->profile['sig']) : '')."</zonetext>\n";
	
        $html .= "\t<bouton id=\"new\" titre=\"Nouveau\"/>\n";
        $html .= "\t<bouton id=\"submit\" titre=\"Poster\"/>\n";
	$html .= "</formulaire>\n";

        return $html.$cuts;
    }

    function action_doFup($group, $artid = -1)
    {
        $this->_newSpool($group, $this->profile['display'], $this->profile['lastnews']);
        $body = preg_replace("/\n\.[ \t\r]*\n/m", "\n..\n", $_POST['body']);
	$body = preg_replace("/&gt;/", ">", $_POST['body']);
        $msg  = 'From: '.$this->profile['name']."\n"
              . "Newsgroups: ".$_POST['forum']."\n"
              . "Subject: ".$_POST['subject']."\n"
              . (empty($this->profile['org']) ? '' : "Organization: {$this->profile['org']}\n")
              . (empty($_POST['fup'])    ? '' : 'Followup-To: '.$_POST['fup']."\n");

        if ($artid != -1) {
            $this->_require('post');
            $post = new BananaPost($artid);
            $refs = ( isset($post->headers['references']) ? $post->headers['references']." " : "" );
            $msg .= "References: $refs{$post->headers['message-id']}\n";
        }

        $msg .= $this->custom.$this->profile['customhdr']."\n".wrap($body, "", $this->wrap);

        if ($this->nntp->post($msg)) {
            header("Location: ?group=$group".($artid==-1 ? '' : "&first=$artid"));
        } else {
            return "<warning>Impossible de poster le message</warning>".$this->action_showThread($group, $artid);
        }
    }

    /**************************************************************************/
    /* Private functions                                                      */
    /**************************************************************************/

    function _newSpool($group, $disp=0, $since='') {
        $this->_require('spool');
        if (!$this->spool || $this->spool->group != $group) {
            $this->spool = new BananaSpool($group, $disp, $since);
        }
    }

    function _newPost($id)
    {
        $this->_require('post');
        $this->post = new BananaPost($id);
    }

    function _newGroup()
    {
        $this->_require('groups');
        $this->groups = new BananaGroups(BANANA_GROUP_SUB);
        if ($this->groups->type == BANANA_GROUP_SUB) {
            $this->newgroups = new BananaGroups(BANANA_GROUP_NEW);
        }
    }

    function _require($file)
    {
        require_once (dirname(__FILE__).'/'.$file.'.inc.php');
    }
}

?>
