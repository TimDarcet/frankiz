<?php
/********************************************************************************
* banana/banana.inc.php : banana main file
* --------------------------
*
* This file is part of the banana distribution
* Copyright: See COPYING files that comes with this distribution
********************************************************************************/

require_once dirname(__FILE__) . '/text.func.inc.php';

class Banana
{

#######
# Configuration variables
#######

### General ###
    static public $profile = Array( 'signature'  => '',
                                    'headers' => array('From' => 'Anonymous <anonymouse@example.com>'),
                                    'display' => 0,
                                    'lastnews' => 0,
                                    'locale'  => 'fr_FR.UTF-8',
                                    'subscribe' => array(),
                                    'autoup' => 1);
    static public $boxpattern;
    static public $withtabs = true;
    static public $mimeparts = array();

### Spool ###
    static public $spool_root    = '/var/spool/banana';
    static public $spool_max     = 3000;
    static public $spool_tbefore = 5;
    static public $spool_tafter  = 5;
    static public $spool_tmax    = 50;
    static public $spool_boxlist = true;

### Message processing ###
    static public $msgparse_headers = array('content-disposition', 'content-transfer-encoding',
                                       'content-type', 'content-id', 'date', 'followup-to',
                                       'from', 'message-id', 'newsgroups', 'organization',
                                       'references', 'subject', 'x-face', 'in-reply-to',
                                       'to', 'cc', 'reply-to');

### Message display ###
    static public $msgshow_headers   = array('from', 'newsgroups', 'followup-to', 'to', 'cc', 'reply-to',
                                       'organization', 'date', 'references', 'in-reply-to');
    static public $msgshow_mimeparts = array('multipart/report', 'multipart/mixed', 
                                             'text/html', 'text/plain', 'text/enriched', 'text', 'message');
    static public $msgshow_xface     = true;
    static public $msgshow_wrap      = 80;
    static public $msgshow_externalimages = false;
    static public $msgshow_hasextimages   = false;
    static public $msgshow_withthread = true;

    /** Match an url
     * Should be included in a regexp delimited using /, !, , or @ (eg: "/$url_regexp/ui")
     * If it matches, return 3 main parts :
     *  \\1 and \\3 are delimiters
     *  \\2 is the url
     *
     * eg : preg_match("!$url_regexp!i", "[http://www.polytechnique.org]", $matches);
     *   $matches[1] = "["
     *   $matches[2] = "http://www.polytechnique.org"
     *   $matches[3] = "]"
     */
    static public $msgshow_url     = '(["\[])?((?:[a-z]+:\/\/|www\.)(?:[\.\,\;\!\:]*[a-z\@0-9~%$£µ&i#\-+=_\/\?]+)+)(["\]])?';

### Message edition ###
    static public $msgedit_canattach  = true;
    static public $msgedit_maxfilesize = 100000;
    /** Global headers to use for messages
     */
    static public $msgedit_headers  = array('Mime-Version' => '1.0', 'User-Agent' => 'Banana 1.6 The Bearded Release');
    /** Mime type order for quoting
     */
    static public $msgedit_mimeparts = array('multipart/report', 'multipart/mixed', 'text/plain', 'text/enriched', 'text/html', 'text', 'message');

### Feed configuration ###
    static public $feed_active         = true;
    static public $feed_format         = 'rss2';
    static public $feed_updateOnDemand = false; // Update the feed each time sbd check it
    static public $feed_copyright      = null;  // Global copyright informations
    static public $feed_generator      = 'Banana 1.6 The Bearded Release'; // Feed generator
    static public $feed_email          = null;  // Admin e-mail
    static public $feed_namePrefix     = 'Banana :: ';
    static public $feed_size           = 15;    // Number of messages in the feed

### Protocole ###
    /** News serveur to use
     */
    static public $nntp_host   = 'news://localhost:119/';

    static public $mbox_path   = '/var/mail';
    static public $mbox_helper = './mbox-helper';

### Debug ###
    static public $debug_nntp   = false;
    static public $debug_mbox   = false;
    static public $debug_smarty = false;


#######
# Constants
#######    

    // Actions
    const ACTION_BOX_NEEDED = 1; // mask
    const ACTION_BOX_LIST   = 2;
    const ACTION_BOX_SUBS   = 4;
    const ACTION_BOX_FEED   = 8;
    const ACTION_MSG_LIST   = 3;
    const ACTION_MSG_READ   = 5;
    const ACTION_MSG_NEW    = 9;
    const ACTION_MSG_CANCEL = 17;
    const ACTION_MSG_IMAGES = 33;

    // Box list view
    const BOXES_ALL = 0;
    const BOXES_SUB = 1;
    const BOXES_NEW = 2;

    // Spool view mode
    const SPOOL_ALL    = 0;
    const SPOOL_UNREAD = 1;


#######
# Runtime variables
#######

    static public $protocole   = null;
    static public $spool       = null;
    static public $message     = null;
    static public $page        = null;

    static public $group       = null;
    static public $artid       = null;
    static public $action      = null;
    static public $part        = null;
    static public $first       = null;

    /** Class parameters storage
     */
    public $params;


#######
# Banana Implementation
#######

    /** Build the instance of Banana
     * This constructor only call \ref loadParams, connect to the server, and build the Smarty page
     * @param protocole Protocole to use
     */
    public function __construct($params = null, $protocole = 'NNTP', $pageclass = 'BananaPage')
    {
        if (is_null($params)) {
            $this->params = $_GET;
        } else {
            $this->params = $params;
        }
        $this->loadParams();

        // connect to protocole handler
        $classname = 'Banana' . $protocole;
        if (!class_exists($classname)) {
            Banana::load($protocole);
        }    
        Banana::$protocole = new $classname(Banana::$group);

        // build the page
        if ($pageclass == 'BananaPage') {
            Banana::load('page');
        }
        Banana::$page = new $pageclass;
        $types = array('multipart/report' => _b_('Rapport d\'erreur'),
                       'multipart/mixed'  => _b_('Composition'),
                       'text/html'        => _b_('Texte formaté'),
                       'text/plain'       => _b_('Texte brut'),
                       'text/enriched'    => _b_('Texte enrichi'),
                       'text'             => _b_('Texte'),
                       'message/rfc822'   => _b_('Mail'),
                       'message'          => _b_('Message'),
                       'source'           => _b_('Source'));
        Banana::$mimeparts = array_merge($types, Banana::$mimeparts);
    }

    /** Fill state vars (Banana::$group, Banana::$artid, Banana::$action, Banana;:$part, Banana::$first)
     */
    protected function loadParams()
    {
        Banana::$group = isset($this->params['group']) ? $this->params['group'] : null;
        Banana::$artid = isset($this->params['artid']) ? $this->params['artid'] : null;
        Banana::$first = isset($this->params['first']) ? $this->params['first'] : null;
        Banana::$part  = isset($this->params['part']) ? $this->params['part'] : 'text';

        $action = @$this->params['action'];
        if ($action == 'rss' || $action == 'rss2' || $action == 'atom') {
            if ($action == 'rss') {
                $action = 'rss2';
            }
            Banana::$feed_format = $action;
            Banana::$action = Banana::ACTION_BOX_FEED;
            return;
        }
    
        // Look for the action to execute
        if (is_null(Banana::$group)) {
            if ($action  == 'subscribe') {
                Banana::$action = Banana::ACTION_BOX_SUBS;
            } else {
                Banana::$action = Banana::ACTION_BOX_LIST;
            }
            return;
        }
        
        if (is_null(Banana::$artid)) {
            if ($action == 'new') {
                Banana::$action = Banana::ACTION_MSG_NEW;
            } else {
                Banana::$action = Banana::ACTION_MSG_LIST;
            }
            return;
        }
        switch ($action) {
          case 'new':
            Banana::$action = Banana::ACTION_MSG_NEW;
            return;
          case 'cancel':
            Banana::$action = Banana::ACTION_MSG_CANCEL;
            return;
          case 'showext':
            Banana::$action = Banana::ACTION_MSG_IMAGES;
            return;
          default:
            Banana::$action = Banana::ACTION_MSG_READ;
        }
    }

    /** Run Banana
     * This function need user profile to be initialised
     */
    public function run()
    {
        // Configure locales
        setlocale(LC_ALL,  Banana::$profile['locale']);
        
        // Check if the state is valid
        if (Banana::$protocole->lastErrNo()) {
            return Banana::$page->kill(_b_('Une erreur a été rencontrée lors de la connexion au serveur') . '<br />'
                                      . Banana::$protocole->lastError());
        }
	
	if (!Banana::$protocole->isValid()) {
            return Banana::$page->kill(_b_('Connexion non-valide'));
        }
        if (Banana::$action & Banana::ACTION_BOX_NEEDED) {
            if(Banana::$boxpattern && !preg_match('/' . Banana::$boxpattern . '/i', Banana::$group)) {
                Banana::$page->setPage('group');        
                return Banana::$page->kill(_b_("Ce newsgroup n'existe pas ou vous n'avez pas l'autorisation d'y accéder"));
            }
        }

        // Dispatch to the action handlers
        switch (Banana::$action) {
          case Banana::ACTION_BOX_SUBS:
            $error = $this->action_subscribe();
            break;
          case Banana::ACTION_BOX_LIST:
            $error = $this->action_listBoxes();
            break;
          case Banana::ACTION_BOX_FEED:
            $this->action_feed(); // generate its own xml
            break;
          case Banana::ACTION_MSG_LIST:
            $error = $this->action_showThread(Banana::$group, Banana::$first);
            break;
          case Banana::ACTION_MSG_IMAGES:
            Banana::$msgshow_externalimages = true;
          case Banana::ACTION_MSG_READ:
            $error = $this->action_showMessage(Banana::$group, Banana::$artid, Banana::$part);
            break;
          case Banana::ACTION_MSG_NEW:
            $error = $this->action_newMessage(Banana::$group, Banana::$artid);
            break;
          case Banana::ACTION_MSG_CANCEL:
            $error = $this->action_cancelMessage(Banana::$group, Banana::$artid);
            break;
          default:
            $error = _b_("L'action demandée n'est pas supportée par Banana");
        }

        // Generate the page
        if (is_string($error)) {
            return Banana::$page->kill($error);
        }
        return Banana::$page->run();
    }

    /** Build and post a new message
     * @return postid (or -1 if the message has not been found)
     */
    public function post($dest, $reply, $subject, $body)
    {
        $hdrs = Banana::$protocole->requestedHeaders();
        $headers                 = Banana::$profile['headers'];
        $headers[$hdrs['dest']]  = $dest;
        if ($reply) {
            $headers[$hdrs['reply']] = $reply;
        } 
        $headers['Subject']      = $subject;
        $msg = BananaMessage::newMessage($headers, $body);
        if (Banana::$protocole->send($msg)) {
            Banana::$group = ($reply ? $reply : $dest);
            $this->loadSpool(Banana::$group);
            return Banana::$spool->getPostId($subject);
        }
        return -1;
    }

    /** Return the CSS code to include in the headers
     */
    public function css()
    {
        return Banana::$page->css;
    }

    /** Return the Link to the feed of the page
     */
    public function feed()
    {
        if (!Banana::$feed_active) {
            return null;
        }
        return Banana::$page->makeURL(array('group' => Banana::$group, 'action' => Banana::$feed_format));
    }

    /** Return the execution backtrace of the current BananaProtocole
     */
    public function backtrace()
    {
        if (Banana::$protocole) {
            return Banana::$protocole->backtrace();
        }
        return null;
    }

    /**************************************************************************/
    /* actions                                                                */
    /**************************************************************************/
    protected function action_saveSubs($groups)
    {
        Banana::$profile['subscribe'] = $groups;
        return true;
    }

    protected function action_subscribe()
    {
        Banana::$page->setPage('subscribe');
        if (isset($_POST['validsubs'])) {
            $this->action_saveSubs(array_keys($_POST['subscribe']));
            Banana::$page->redirect();
        }
        $groups = Banana::$protocole->getBoxList(Banana::BOXES_ALL);
        Banana::$page->assign('groups', $groups);
        return true;
    }

    protected function action_listBoxes()
    {
        Banana::$page->setPage('forums');
        $groups    = Banana::$protocole->getBoxList(Banana::BOXES_SUB, Banana::$profile['lastnews'], true);
        Banana::$page->assign('groups', $groups);
        if (empty(Banana::$profile['subscribe']) || Banana::$profile['lastnews']) {
            $newgroups = Banana::$protocole->getBoxList(Banana::BOXES_NEW, Banana::$profile['lastnews'], true);
            Banana::$page->assign('newgroups', $newgroups);
        }
        return true;
    }

    protected function action_feed()
    {
        Banana::load('feed');
        if (Banana::$group) {
            if (Banana::$feed_updateOnDemand) { 
                $this->loadSpool(Banana::$group); 
            } 
            $feed =& BananaFeed::getFeed();
            $feed->toXML();
        }
        if (Banana::$profile['subscribe']) {
            $subfeed = null;
            foreach (Banana::$profile['subscribe'] as $group) {
                Banana::$group = $group;
                if (Banana::$feed_updateOnDemand) {
                    $this->loadSpool($group);
                }
                $feed =& BananaFeed::getFeed();
                $subfeed =& BananaFeed::merge($subfeed, $feed, _b_('Abonnements'), _b_('Mes abonnements Banana'));
            }
            $subfeed->toXML();
        }
        Banana::$page->feed();
    }

    protected function action_showThread($group, $first)
    {
        Banana::$page->setPage('thread');
        if (!$this->loadSpool($group)) {
            return _b_('Impossible charger la liste des messages de ') . $group;
        }
        if (Banana::$spool_boxlist) {
            $groups = Banana::$protocole->getBoxList(Banana::BOXES_SUB, Banana::$profile['lastnews'], true);
            Banana::$page->assign('groups', $groups);
        }
        Banana::$page->assign('msgbypage', Banana::$spool_tmax);
        return true;
    }

    protected function action_showMessage($group, $artid, $partid = 'text')
    {
        Banana::$page->setPage('message');
        $istext = $partid == 'text' || $partid == 'source'
                || preg_match('!^[-a-z0-9_]+/[-a-z0-9_]+$!', $partid);
        if ($istext) {
            $this->loadSpool($group);
        }
        $msg =& $this->loadMessage($group, $artid);
        if (is_null($msg)) {
            $this->loadSpool($group);
            $this->removeMessage($group, $artid);
            return _b_('Le message demandé n\'existe pas. Il est possible qu\'il ait été annulé');
        }
        if ($partid == 'xface') {
            $msg->getXFace();
            exit;
        } elseif (!$istext) {
            $part = $msg->getPartById($partid);
            if (!is_null($part)) {
                $part->send(true);
            }
            $part = $msg->getFile($partid);
            if (!is_null($part)) {
                $part->send();
            }
            exit;
        } elseif ($partid == 'text') {
            $partid = null;
            Banana::$page->assign('body', $msg->getFormattedBody($partid));
        } elseif ($partid == 'source') {
            $text = Banana::$protocole->getMessageSource($artid);
            if (!is_utf8($text)) {
                $text = utf8_encode($text);
            }
            Banana::$page->assign('body', '<pre>' . banana_htmlentities($text) . '</pre>');
        } else {
            Banana::$page->assign('body', $msg->getFormattedBody($partid));
        }

        if (Banana::$profile['autoup']) {
            Banana::$spool->markAsRead($artid);
        }
        if (Banana::$spool_boxlist) {
            $groups    = Banana::$protocole->getBoxList(Banana::BOXES_SUB, Banana::$profile['lastnews'], true);
            Banana::$page->assign('groups', $groups);
        }    
        Banana::$page->assign_by_ref('message', $msg);
        Banana::$page->assign('extimages', Banana::$msgshow_hasextimages);
        Banana::$page->assign('headers', Banana::$msgshow_headers);
        Banana::$page->assign('type', $partid);
        return true;
    }

    protected function action_newMessage($group, $artid)
    {
        Banana::$page->setPage('new');
        if (!Banana::$protocole->canSend()) {
            return _b_('Vous n\'avez pas le droit de poster');
        }
        $hdrs    = Banana::$protocole->requestedHeaders();
        $headers = array();
        foreach ($hdrs as $header) {
            $headers[$header] = array('name' => BananaMessage::translateHeaderName($header));
            if (isset(Banana::$profile['headers'][$header])) {
                $headers[$header]['fixed'] = Banana::$profile['headers'][$header];
            }
        }
        if (isset($_POST['sendmessage'])) {
            $hdr_values = array();
            foreach ($hdrs as $header) {
                $hdr_values[$header] = isset($headers[$header]['fixed']) ? $headers[$header]['fixed'] : @$_POST[$header];
                if (!is_utf8($hdr_values[$header])) {
                    $hdr_values[$header] = utf8_encode($hdr_values[$header]);
                }
                if ($headers != 'Subject') {
                    $hdr_values[$header] = str_replace(', ', ',', $hdr_values[$header]);
                }
            }
            if ($artid) {
                $old =& $this->loadMessage($group, $artid);
                $hdr_values['References'] = $old->getHeaderValue('references') . $old->getHeaderValue('message-id');
            }
            $msg = null;
            if (isset($_POST['body']) && !is_utf8($_POST['body'])) {
                $_POST['body'] = utf8_encode($_POST['body']);
            }
            if (empty($hdr_values['Subject'])) {
                Banana::$page->trig(_b_('Le message doit avoir un sujet'));
            } elseif (Banana::$msgedit_canattach && isset($_FILES['attachment'])) {
                $uploaded = $_FILES['attachment'];
                if (!is_uploaded_file($uploaded['tmp_name'])) {
                    Banana::$page->trig(_b_('Une erreur est survenue lors du téléchargement du fichier'));
                } else {
                    $msg = BananaMessage::newMessage($hdr_values, $_POST['body'], $uploaded);
                }
            } else {
                $msg = BananaMessage::newMessage($hdr_values, $_POST['body']);
            }
            if (!is_null($msg)) {
                if (Banana::$protocole->send($msg)) {
                    $this->loadSpool($group);
                    $newid = Banana::$spool->updateUnread(Banana::$profile['lastnews']);
                    Banana::$page->redirect(array('group' => $group, 'artid' => $newid ? $newid : $artid));
                }
                Banana::$page->trig(_b_('Une erreur est survenue lors de l\'envoi du message :') . '<br />'
                                   . Banana::$protocole->lastError());
            }
        } else {
            if (!is_null($artid)) {
                $msg    =& $this->loadMessage($group, $artid);
                $body    = $msg->getSender() . _b_(' a écrit :') . "\n" . $msg->quote();
                $subject = $msg->getHeaderValue('subject');
                $headers['Subject']['user'] = 'Re: ' . preg_replace("/^re\s*:\s*/i", '', $subject);
                $target  = $msg->getHeaderValue($hdrs['reply']);
                if (empty($target)) {
                    $target = $group;
                }
                $headers[$hdrs['dest']]['user'] =& $target;
            } else {
                $body    = '';
                $headers[$hdrs['dest']]['user'] = $group;
            }
            if (Banana::$profile['signature']) {
                $body .=  "\n\n-- \n" . Banana::$profile['signature'];
            }
            Banana::$page->assign('body', $body);
        }

        Banana::$page->assign('maxfilesize', Banana::$msgedit_maxfilesize);
        Banana::$page->assign('can_attach', Banana::$msgedit_canattach);
        Banana::$page->assign('headers', $headers);
        return true;
    }

    protected function action_cancelMessage($group, $artid)
    {
        Banana::$page->setPage('cancel');
        $msg =& $this->loadMessage($group, $artid);
        if (!$msg->canCancel()) {
            return _b_('Vous n\'avez pas les droits suffisants pour supprimer ce message');
        }
        if (isset($_POST['cancel'])) {
            $this->loadSpool($group);
            $ndx = Banana::$spool->getNdX($id) - 1;
            if (!Banana::$protocole->cancel($msg)) {
                return _b_('Une erreur s\'est produite lors de l\'annulation du message :') . '<br />'
                       . Banana::$protocole->lastError();
            }
            if ($ndx < 50) {
                 $ndx = 0;
            }
            $this->removeMessage($group, $artid);
            Banana::$page->redirect(Array('group' => $group, 'first' => $ndx));
        }

        Banana::$page->assign_by_ref('message', $msg);
        Banana::$page->assign('body', $msg->getFormattedBody());
        Banana::$page->assign('headers', Banana::$msgshow_headers);
        return true;
    }

    /**************************************************************************/
    /* Spoolgen functions                                                     */
    /**************************************************************************/

    private function checkErrors()
    {
        if (Banana::$protocole->lastErrno()) {
            echo "\nL'erreur suivante s'est produite : "
                . Banana::$protocole->lastErrno() . " "
                . Banana::$protocole->lastError() . "\n";
            return false;
        }
        return true;
    }

    static public function createAllSpool(array $protos)
    {
        foreach ($protos as $proto) {
            $banana = new Banana(array(), $proto);

            if (!$banana->checkErrors()) {
                continue;
            }
            $groups = Banana::$protocole->getBoxList();
            if (!$banana->checkErrors()) {
                continue;
            }

            print "** $proto **\n";
            foreach (array_keys($groups) as $g) {
                print "Generating spool for $g: ";
                Banana::$group = $g;
                $spool = $banana->loadSpool($g);
                if (!$banana->checkErrors()) {
                    break;
                }
                print "done.\n";
                unset($spool);
                Banana::$spool = null;
            }
            print "\n";
        }
    }

    static public function refreshAllFeeds(array $protos)
    {
        Banana::load('feed');
        Banana::$feed_updateOnDemand = true; // In order to force update
        foreach ($protos as $proto) {
            $banana = new Banana(array(), $proto);

            if (!$banana->checkErrors()) {
                continue;
            }
            $groups = Banana::$protocole->getBoxList();
            if (!$banana->checkErrors()) {
                continue;
            }

            print "** $proto **\n";
            foreach (array_keys($groups) as $g) {
                print "Generating feed cache for $g: ";
                Banana::$group = $g;
                $spool = $banana->loadSpool($g);
                if (!$banana->checkErrors()) {
                    break;
                }
                $feed  =& BananaFeed::getFeed();
                print "done.\n";
                unset($feed);
                unset($spool);
                Banana::$spool = null;
            }
            print "\n";
        }
    }
 
    /**************************************************************************/
    /* Private functions                                                      */
    /**************************************************************************/

    protected function loadSpool($group)
    {
        Banana::load('spool');
        if (!Banana::$spool || Banana::$spool->group != $group) {
            $clean = false;
            if ($group == @$_SESSION['banana_group'] && isset($_SESSION['banana_spool'])) {
                Banana::$spool = unserialize($_SESSION['banana_spool']);
                $clean = @(Banana::$profile['lastnews'] != $_SESSION['banana_lastnews']);
            } else {
                unset($_SESSION['banana_message']);
                unset($_SESSION['banana_artid']);
                unset($_SESSION['banana_showhdr']);
            }
            BananaSpool::getSpool($group, Banana::$profile['lastnews'], Banana::$profile['autoup'] || $clean);
            $_SESSION['banana_group'] = $group;
            if (!Banana::$profile['display']) {
                $_SESSION['banana_spool'] = serialize(Banana::$spool);
                $_SESSION['banana_lastnews'] = Banana::$profile['lastnews'];
            }
            Banana::$spool->setMode(Banana::$profile['display'] ? Banana::SPOOL_UNREAD : Banana::SPOOL_ALL);
        }
        return true;
    }

    protected function &loadMessage($group, $artid)
    {
        Banana::load('message');
        if ($group == @$_SESSION['banana_group'] && $artid == @$_SESSION['banana_artid']
            && isset($_SESSION['banana_message'])) {
            $message = unserialize($_SESSION['banana_message']);
            Banana::$msgshow_headers = $_SESSION['banana_showhdr'];
        }  else {
            $message = Banana::$protocole->getMessage($artid);
            $_SESSION['banana_group'] = $group;
            $_SESSION['banana_artid'] = $artid;
            $_SESSION['banana_message'] = serialize($message);
            $_SESSION['banana_showhdr'] = Banana::$msgshow_headers;
        }
        Banana::$message =& $message;
        return $message;
    }

    protected function removeMessage($group, $artid)
    {
        Banana::$spool->delId($artid);
        if ($group == $_SESSION['banana_group']) {
            if (!Banana::$profile['display']) {
                $_SESSION['banana_spool'] = serialize(Banana::$spool);
            }
            if ($artid == $_SESSION['banana_artid']) {
                unset($_SESSION['banana_message']);
                unset($_SESSION['banana_showhdr']);
                unset($_SESSION['banana_artid']);
            }
        }
        $this->loadSpool($group);
        return true;
    }

    static private function load($file)
    {
        $file = strtolower($file) . '.inc.php';
        if (!@include_once dirname(__FILE__) . "/$file") {
            require_once $file;
        }
    }
}

// vim:set et sw=4 sts=4 ts=4 enc=utf-8:
?>
