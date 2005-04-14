<?php
/********************************************************************************
* include/groups.inc.php : class for group lists
* ------------------------
*
* This file is part of the banana distribution
* Copyright: See COPYING files that comes with this distribution
********************************************************************************/

/** class for group lists
 */

define ( 'BANANA_GROUP_ALL', 0 );
define ( 'BANANA_GROUP_SUB', 1 );
define ( 'BANANA_GROUP_NEW', 2 );
 
class BananaGroups {
    /** group list */
    var $overview = Array();
    /** last update */
    var $date;

    var $type;

    /** constructor
     */

    function BananaGroups($_type = BANANA_GROUP_SUB) {
        global $banana;

        $this->type = $_type;
        $desc       = $banana->nntp->xgtitle();
        
        if ($_type == BANANA_GROUP_NEW) {
            $list = $banana->nntp->newgroups($banana->profile['lastnews']);
        } else {
            $list = $banana->nntp->liste();
            if ($_type == BANANA_GROUP_SUB) {
                $mylist = Array();
                foreach ($banana->profile['subscribe'] as $g) {
                    if (isset($list[$g])) {
                        $mylist[$g] = $list[$g];
                    }
                }
                $list = $mylist;
            }
        }

        foreach ($list as $g=>$l) {
            $this->overview[$g][0] = isset($desc[$g]) ? $desc[$g] : '-';
            $this->overview[$g][1] = $l[0];
        }
        ksort($this->overview);

        if (empty($this->overview) && $_type == BANANA_GROUP_SUB) {
            $this = new BananaGroups(BANANA_GROUP_ALL);
        }
    }

    /** updates overview 
     * @param date INTEGER date of last update
     */
    function update($_date) {
        global $banana;
        $serverdate = $banana->nntp->date();
        if (!$serverdate) $serverdate=time();
        $newlist = $banana->nntp->newgroups($_date);
        if (!$newlist) return false;
        $this->date = $serverdate;
        foreach (array_keys($newlist) as $g) {
            $groupstat = $banana->nntp->group($g);
            $groupdesc = $banana->nntp->xgtitle($g);
            $this->overview[$g][0]=($groupdesc?$groupdesc:"-");
            $this->overview[$g][1]=$groupstat[0];
        }
        return true;
    }

    function to_html($show_form = false)
    {
        global $banana;
        if (empty($this->overview)) {
            return;
        }

        $html  = "<liste id=\"bicol banana_group\" titre=\"Newsgroups\"";
	$html .= ">\n";
        $html .= "\t<entete id=\"coltotal\" titre=\"Total\"/>\n";
        if ($show_form) {
            $html .= "\t<entete id=\"colabo\" titre=\"Abo.\"/>\n";
        } elseif ($this->type == BANANA_GROUP_SUB) {
            $html .= "\t<entete id=\"colnouveau\" titre=\"Nouveau\"/>\n";
        }
        $html .= "\t<entete id=\"colnom\" titre=\"Nom\"/>\n";
	$html .= "\t<entete id=\"coldesc\" titre=\"Description\"/>\n";

        $b = true;
        foreach ($this->overview as $g => $d) {
            $b     = !$b;
            $ginfo = $banana->nntp->group($g);
            $new   = count($banana->nntp->newnews($banana->profile['lastnews'],$g));

	    $html .= "\t<element id=\"$g\">\n";
	    $html .= "\t\t<colonne id=\"coltotal\">{$ginfo[0]}</colonne>\n";
            if ($show_form) {
                $html .= "\t\t<colonne id=\"colabo\">";		
//		$html .= "<choix id=\"abo$g\" titre=\"test\"/>\n";// valeur=\"";
/*		$html .= (in_array($g, $banana->profile['subscribe']) ? "1":"0");
		$html .= "\"/></colonne>\n";*/
		$html .= "</colonne>\n";
            } elseif ($this->type == BANANA_GROUP_SUB) {
	        $html .= "\t\t<colonne id=\"colnouveau\">".($new ? $new : '-')."</colonne>\n";
            }
            $html .= "\t\t<colonne id=\"colnom\"><a href=\"banana.php?group=$g\">$g</a></colonne>\n";
	    $html .= "\t\t<colonne id=\"coldesc\">".htmlentities($d[0])."</colonne>\n";
	    $html .= "\t</element>\n";
        }

        $html .= "</liste>\n";

	if($show_form) {
		$formulaire = "<formulaire id=\"formabo\" action=\"banana.php?\">\n";
		$formulaire .= "\t<bouton id=\"submit\" titre=\"Valider\"/>\n";
		$formulaire .= "</formulaire>\n";
		$html = $formulaire.$html.$formulaire;
	}
       
        return $html;
    }
}

?>
