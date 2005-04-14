<?php
/********************************************************************************
* include/spool.inc.php : spool subroutines
* -----------------------
*
* This file is part of the banana distribution
* Copyright: See COPYING files that comes with this distribution
********************************************************************************/

if(!function_exists('_file_put_contents')) {
    function file_put_contents($filename, $data)
    {
        $fp = fopen($filename, 'w');
        if(!$fp) {
            trigger_error('file_put_contents cannot write in file '.$filename, E_USER_ERROR);
            return;
        }
        fputs($fp, $data);
        fclose($fp);
    }
}

function spoolCompare($a,$b) { return ($b->date>=$a->date); }

/** Class spoolhead
 *  class used in thread overviews
 */
class BananaSpoolHead
{
    /** date (timestamp) */
    var $date;
    /** subject */
    var $subject;
    /** author */
    var $from;
    /** reference of parent */
    var $parent;
    /** paren is direct */
    var $parent_direct;
    /** array of children */
    var $children = Array();
    /** true if post is read */
    var $isread;
    /** number of posts deeper in this branch of tree */
    var $desc;
    /**  same as desc, but counts only unread posts */
    var $descunread;

    /** constructor
     * @param $_date INTEGER timestamp of post
     * @param $_subject STRING subject of post
     * @param $_from STRING author of post
     * @param $_desc INTEGER desc value (1 for a new post)
     * @param $_read BOOLEAN true if read
     * @param $_descunread INTEGER descunread value (0 for a new post)
     */

    function BananaSpoolHead($_date, $_subject, $_from, $_desc=1, $_read=true, $_descunread=0)
    {
        $this->date       = $_date;
        $this->subject    = $_subject;
        $this->from       = $_from;
        $this->desc       = $_desc;
        $this->isread     = $_read;
        $this->descunread = $_descunread;
    }
}

/** Class spool
 * builds and updates spool 
 */

define("BANANA_SPOOL_VERSION", '0.2');

class BananaSpool
{
    var $version;
    /**  spool */
    var $overview;
    /**  group name */
    var $group;
    /**  array msgid => msgnum */
    var $ids;
    /** thread starts */
    var $roots;

    /** constructor
     * @param $_group STRING group name
     * @param $_display INTEGER 1 => all posts, 2 => only threads with new posts
     * @param $_since INTEGER time stamp (used for read/unread)
     */
    function BananaSpool($_group, $_display=0, $_since="")
    {
        global $banana;
        $this->group = $_group;
        $groupinfo   = $banana->nntp->group($_group);
        if (!$groupinfo) { return ($this = null); }

        $this->_readFromFile();

        $do_save = false;
        $first   = $banana->maxspool ? max($groupinfo[2]-$banana->maxspool, $groupinfo[1]) : $groupinfo[1];
        $last    = $groupinfo[2];
        if ($this->version == BANANA_SPOOL_VERSION) {
            for ($id = min(array_keys($this->overview)); $id<$first; $id++) { 
                $this->delid($id, false);
                $do_save = true;
            }
            $first = max(array_keys($this->overview))+1;
        } else {
            unset($this->overview, $this->ids);
            $this->version = BANANA_SPOOL_VERSION;
        }

        if ($first<=$last && $groupinfo[0]) {
            $do_save = true;
            $this->_updateSpool("$first-$last");
        }

        if ($do_save) { $this->_saveToFile(); }

        $this->_updateUnread($_since, $_display);
    }

    function _readFromFile()
    {
        $file = $this->_spoolfile();
        if (file_exists($file)) {
            $this = unserialize(file_get_contents($file));
        }
    }

    function _saveToFile()
    {
        $file = $this->_spoolfile();
        uasort($this->overview, "spoolcompare");

        $this->roots = Array();
        foreach($this->overview as $id=>$msg) {
            if (is_null($msg->parent)) {
                $this->roots[] = $id;
            }
        }
        
        file_put_contents($file, serialize($this));
    }

    function _spoolfile()
    {
        global $banana;
        $url = parse_url($banana->host);
        $file = $url['host'].'_'.$url['port'].'_'.$this->group;
        return dirname(dirname(__FILE__)).'/../cache/banana/'.$file;
    }

    function _updateSpool($arg)
    {
        global $banana;
        $dates    = array_map('strtotime',    $banana->nntp->xhdr('Date',    $arg));
        $subjects = array_map('headerdecode', $banana->nntp->xhdr('Subject', $arg));
        $froms    = array_map('headerdecode', $banana->nntp->xhdr('From',    $arg));
        $msgids   = $banana->nntp->xhdr('Message-ID', $arg);
        $refs     = $banana->nntp->xhdr('References', $arg);

        if (is_array($this->ids)) {
            $this->ids = array_merge($this->ids, array_flip($msgids));
        } else {
            $this->ids = array_flip($msgids);
        }

        foreach ($msgids as $id=>$msgid) {
            $msg                = new BananaSpoolHead($dates[$id], $subjects[$id], $froms[$id]);
            $refs[$id]          = str_replace('><', '> <', $refs[$id]);
            $msgrefs            = preg_split("/[ \t]/", strtr($refs[$id], $this->ids));
            $parents            = preg_grep('/^\d+$/', $msgrefs);
            $msg->parent        = array_pop($parents);
            $msg->parent_direct = preg_match('/^\d+$/', array_pop($msgrefs));

            if (isset($this->overview[$id])) {
                $msg->desc     = $this->overview[$id]->desc;
                $msg->children = $this->overview[$id]->children;
            }
            $this->overview[$id] = $msg;

            if ($p = $msg->parent) {
                if (empty($this->overview[$p])) {
                    $this->overview[$p] = new BananaSpoolHead($dates[$p], $subjects[$p], $froms[$p], 1);
                }
                $this->overview[$p]->children[] = $id;

                while ($p) {
                    $this->overview[$p]->desc += $msg->desc;
                    $p = $this->overview[$p]->parent;
                }
            }
        }
    }

    function _updateUnread($since, $mode)
    {
        global $banana;
        if (empty($since)) { return; }

        if (is_array($newpostsids = $banana->nntp->newnews($since, $this->group))) {
            $newpostsids = array_intersect($newpostsids, array_keys($this->ids));
            foreach ($newpostsids as $mid) {
                $this->overview[$this->ids[$mid]]->isread     = false;
                $this->overview[$this->ids[$mid]]->descunread = 1;
                $parentmid = $this->ids[$mid];
                while (isset($parentmid)) {
                    $this->overview[$parentmid]->descunread ++;
                    $parentmid = $this->overview[$parentmid]->parent;
                }
            }

            if (count($newpostsids)) {
                switch ($mode) {
                    case 1:
                        foreach ($this->roots as $k=>$i) {
                            if ($this->overview[$i]->descunread==0) {
                                $this->killdesc($i);
                                unset($this->roots[$k]);
                            }
                        }
                        break;
                }
            }
        }
    }

    /** kill post and childrens
     * @param $_id MSGNUM of post
     */

    function killdesc($_id)
    {
        if (sizeof($this->overview[$_id]->children)) {
            foreach ($this->overview[$_id]->children as $c) {
                $this->killdesc($c);
            }
        }
        unset($this->overview[$_id]);
        if (($msgid = array_search($_id, $this->ids)) !== false) {
            unset($this->ids[$msgid]);
        }
    }

    /** delete a post from overview
     * @param $_id MSGNUM of post
     */

    function delid($_id, $write=true)
    {
        if (isset($this->overview[$_id])) {
            if (sizeof($this->overview[$_id]->parent)) {
                $this->overview[$this->overview[$_id]->parent]->children = 
                    array_diff($this->overview[$this->overview[$_id]->parent]->children, array($_id));
                if (sizeof($this->overview[$_id]->children)) {
                    $this->overview[$this->overview[$_id]->parent]->children = 
                        array_merge($this->overview[$this->overview[$_id]->parent]->children, $this->overview[$_id]->children);
                    foreach ($this->overview[$_id]->children as $c) {
                        $this->overview[$c]->parent        = $this->overview[$_id]->parent;
                        $this->overview[$c]->parent_direct = false;
                    }
                }
                $p = $this->overview[$_id]->parent;
                while ($p) {
                    $this->overview[$p]->desc--;
                    $p = $this->overview[$p]->parent;
                }
            } elseif (sizeof($this->overview[$_id]->children)) {
                foreach ($this->overview[$_id]->children as $c) {
                    $this->overview[$c]->parent = null;
                }
            }
            unset($this->overview[$_id]);
            $msgid = array_search($_id, $this->ids);
            if ($msgid) {
                unset($this->ids[$msgid]);
            }
            
            if ($write) { $this->_saveToFile(); }
        }
    }

    /** displays children tree of a post
     * @param $_id INTEGER MSGNUM of post
     * @param $_index INTEGER linear number of post in the tree
     * @param $_first INTEGER linear number of first post displayed
     * @param $_last INTEGER linear number of last post displayed
     * @param $_ref STRING MSGNUM of current post 
     * @param $_pfx_node STRING prefix used for current node
     * @param $_pfx_end STRING prefix used for children of current node
     * @param $_head BOOLEAN true if first post in thread
     */

    function _to_html($_id, $_index, $_first=0, $_last=0, $_ref="", $_pfx_node="", $_pfx_end="", $_head=true)
    {
        $spfx_f   = '<image source="banana/img/k1.gif" texte="o" />'; 
        $spfx_n   = '<image source="banana/img/k2.gif" texte="*" />'; 
        $spfx_Tnd = '<image source="banana/img/T-direct.gif" texte="+" />';
        $spfx_Lnd = '<image source="banana/img/L-direct.gif" texte="`" />';
        $spfx_snd = '<image source="banana/img/s-direct.gif" texte="-" />';
        $spfx_T   = '<image source="banana/img/T.gif" texte="+" />';
        $spfx_L   = '<image source="banana/img/L.gif" texte="`" />';
        $spfx_s   = '<image source="banana/img/s.gif" texte="-" />';
        $spfx_e   = '<image source="banana/img/e.gif" texte="&nbsp;" />';
        $spfx_I   = '<image source="banana/img/I.gif" texte="|" />';

        if ($_index + $this->overview[$_id]->desc < $_first || $_index > $_last) {
            return;
        }

        $res = '';

        if ($_index>=$_first) {
            $hc = empty($this->overview[$_id]->children);

	    $res .= "\t<element>\n";
	    $res .= "\t\t<colonne id=\"coldate\">".fancyDate($this->overview[$_id]->date)."</colonne>\n";
	    $res .= "\t\t<colonne id=\"colsujet\">";
	    if($_index == $_ref) {
	    	$res .= htmlentities($this->overview[$_id]->subject);
	    } else {
	    	$res .= "<a href='banana.php?group={$this->group}&amp;artid=$_id'>"
			.htmlentities($this->overview[$_id]->subject).'</a>';
	    }
	    $res .= "</colonne>\n";
	    $res .= "\t\t<colonne id=\"colaut\">".formatFrom($this->overview[$_id]->from)."</colonne>\n";
	    $res .= "\t</element>\n";

//            $res .= '<tr class="'.($_index%2?'pair':'impair').($this->overview[$_id]->isread?'':' new')."\">\n";
/*            $res .= "<td class='subj'>"
                ."<div class='tree'>$_pfx_node".($hc?($_head?$spfx_f:($this->overview[$_id]->parent_direct?$spfx_s:$spfx_snd)):$spfx_n)
                ."</div>";*/
//	return $res;
            if ($hc) { return $res; }
        } 

        $_index ++;

        $children = $this->overview[$_id]->children;
        while ($child = array_shift($children)) {
            if ($_index > $_last) { return $res; }
            if ($_index+$this->overview[$child]->desc >= $_first) {
                if (sizeof($children)) {
                    $res .= $this->_to_html($child, $_index, $_first, $_last, $_ref,
                            $_pfx_end.($this->overview[$child]->parent_direct?$spfx_T:$spfx_Tnd),
                            $_pfx_end.$spfx_I, false);
                } else {
                    $res .= $this->_to_html($child, $_index, $_first, $_last, $_ref,
                            $_pfx_end.($this->overview[$child]->parent_direct?$spfx_L:$spfx_Lnd),
                            $_pfx_end.$spfx_e, false);
                }
            }
            $_index += $this->overview[$child]->desc;
        }

        return $res;
    }

    /** Displays overview
     * @param $_first INTEGER MSGNUM of first post
     * @param $_last INTEGER MSGNUM of last post
     * @param $_ref STRING MSGNUM of current/selectionned post
     */

    function to_html($_first=0, $_last=0, $_ref = null)
    {
        $res  = "<liste id=\"bicol banana_thread\" titre=\"Messages\">\n";
	
        if (is_null($_ref)) {
		$res .= "\t<entete id=\"coldate\" titre=\"Date\"/>\n";
		$res .= "\t<entete id=\"colsujet\" titre=\"Sujet\"/>\n";
		$res .= "\t<entete id=\"colaut\" titre=\"Auteur\"/>\n";
        }

        $index = 1;
        if (sizeof($this->overview)) {
            foreach ($this->roots as $id) {
                $res   .= $this->_to_html($id, $index, $_first, $_last, $_ref);
                $index += $this->overview[$id]->desc ;
                if ($index > $_last) { break; }
            }
        } else {
            $res .= "\t<element>\n";
	    $res .= "\t\t<colonne id=\"coldate\">-</colonne>\n";
	    $res .= "\t\t<colonne id=\"colsujet\">-</colonne>\n";
	    $res .= "\t\t<colonne id=\"colaut\">Aucun message sur ce forum</colonne>\n";
	    $res .= "\t</element>\n";
        }

        return $res."</liste>\n";
    }

    /** computes linear post index
     * @param $_id INTEGER MSGNUM of post
     * @return INTEGER linear index of post
     */

    function getndx($_id)
    {
        $ndx    = 1;
        $id_cur = $_id;
        while (true) {
            $id_parent = $this->overview[$id_cur]->parent;
            if (is_null($id_parent)) break;
            $pos       = array_search($id_cur, $this->overview[$id_parent]->children);
        
            for ($i = 0; $i < $pos ; $i++) {
                $ndx += $this->overview[$this->overview[$id_parent]->children[$i]]->desc;
            }
            $ndx++; //noeud p�e

            $id_cur = $id_parent;
        }

        foreach ($this->roots as $i) {
            if ($i==$id_cur) {
                break;
            }
            $ndx += $this->overview[$i]->desc;
        }
        return $ndx;
    }
}

?>
