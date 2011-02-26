#!/usr/bin/php -q
<?php
ini_set("memory_limit","256M");

/***************************************************************************
 *  Copyright (C) 2003-2010 Polytechnique.org                              *
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

require '../../bin/connect.db.inc.php';
$globals->debug = 0;

echo "Converting msdnaa licenses tables... \n";

XDB::execute('TRUNCATE TABLE msdnaa_keys');
 
$softwares = array('2k10onenote',
                   '2k10visiopro',
                   '2k3access',
                   '2k3onenote',
                   '2k3serv',
                   '2k3visiopro',
                   '2k8servR2',
                   'win7',
                   'winvista',
                   'winxp');

// Insert given keys
foreach($softwares as $s){
    $iter = XDB::iterator('SELECT  eleve_id, cle, attrib
                             FROM  msdnaa.cles_' . $s);

    while ($datas = $iter->next()) {
        $l = new License();
        $l->insert();
        $l->software($s);
        $l->key($datas['cle']);
        if($datas['eleve_id']){
            $l->uid($datas['eleve_id']);
        } else if($datas['attrib']) {
            $l->uid(0);
            $l->comments("clé attribuée sans uid");
            echo "Erreur : " . $l->comments() . " \n";
        }
    }
}

//Insert free keys
$iter = XDB::iterator('SELECT  logiciel, cle
                         FROM  msdnaa.cles_libres');

while ($datas = $iter->next()) {
    $l = new License();
    $l->insert();
    $l->key($datas['cle']);
    $l->software($datas['logiciel']);
}

//Insert admin keys
$iter = XDB::iterator('SELECT  log as logiciel, cle
                         FROM  msdnaa.cles_admin');

while ($datas = $iter->next()) {
    $l = new License();
    $l->insert();
    $l->key($datas['cle']);
    $l->software($datas['logiciel']);
    $l->admin(true);
}

//Insert "other" keys
$iter = XDB::iterator('SELECT  logiciel, cle, eleve_id, binet_id
                         FROM  msdnaa.cles_autres');

while ($datas = $iter->next()) {
    $l = new License();
    $l->insert();
    $l->key($datas['cle']);
    $l->software($datas['logiciel']);
    if($datas['eleve_id']){
            $l->uid($datas['eleve_id']);
    }
    if($datas['binet_id']){
            $l->gid($datas['binet_id']);
    }
}

//Insert unregistred user keys
$iter = XDB::iterator('SELECT  logiciel, cle, prenom, nom, statut, mail
                         FROM  msdnaa.cles_unregistered_users');

while ($datas = $iter->next()) {
    $l = new License();
    $l->insert();
    $l->key($datas['cle']);
    $l->software($datas['logiciel']);
    $l->uid(0);
    $l->comments("Key given to " . $datas['prenom'] . " " . $datas['nom'] . " (" . $datas['statut'] . ") " . $datas['mail']);
}

echo "Done \n";

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
