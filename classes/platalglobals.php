<?php
/***************************************************************************
 *  Copyright (C) 2003-2008 Polytechnique.org                              *
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

class PlatalGlobals extends PlGlobals
{
    /** The x.org version */
    public $version = '';

    /** db params */
    public $dbdb               = 'frankiz2';
    public $dbhost             = 'gwz';
    public $dbuser             = 'web';
    public $dbpwd              = '42gwz:gwennoz';
    public $dbcharset          = 'utf8';

    /** default skin */
    public $skin;
    public $register_skin;

    public function __construct()
    {
        parent::__construct(array('platal.ini', 'platal.conf'));
    }

    public function init()
    {
//        $this->bootstrap(array('NbIns'), array($this, 'updateNbIns'));
//        $this->bootstrap(array('NbValid'), array($this, 'updateNbValid'));
    }

/*    public function asso($key=null)
    {
        static $aid = null;

        if (is_null($aid)) {
            $gp = Get::v('n');
            if ($p = strpos($gp, '/')) {
                $gp = substr($gp, 0, $p);
            }

            if ($gp) {
                $res = XDB::query('SELECT  a.*, d.nom AS domnom,
                                           FIND_IN_SET(\'wiki_desc\', a.flags) AS wiki_desc,
                                           FIND_IN_SET(\'notif_unsub\', a.flags) AS notif_unsub
                                     FROM  groupex.asso AS a
                                LEFT JOIN  groupex.dom  AS d ON d.id = a.dom
                                    WHERE  diminutif = {?}', $gp);
                if (!($aid = $res->fetchOneAssoc())) {
                    $aid = array();
                }
            } else {
                $aid = array();
            }
        }
        if (empty($key)) {
            return $aid;
        } elseif ( isset($aid[$key]) ) {
            return $aid[$key];
        } else {
            return null;
        }
    }
*/
/*
    public function updateNbIns()
    {
        $res = XDB::query("SELECT  COUNT(*)
                             FROM  auth_user_md5
                            WHERE  perms IN ('admin','user') AND deces=0");
        $cnt = $res->fetchOneCell();
        $this->changeDynamicConfig(array('NbIns' => $cnt));
    }

    public function updateNbValid()
    {
        $res = XDB::query("SELECT  COUNT(*)
                             FROM  requests");
        $this->changeDynamicConfig(array('NbValid' => $res->fetchOneCell()));
    }
*/
}


/******************************************************************************
 * Dynamic configuration update/edition stuff
 *****************************************************************************/

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
