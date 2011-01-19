#!/usr/bin/php -q
<?php
ini_set("memory_limit","512M");

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

/*
 * Convert fkz2 to fkz3
 */

require '../../bin/connect.db.inc.php';

function conv($str) {
    $str = html_entity_decode($str, ENT_QUOTES);
    $str = str_replace("&apos;", "'", $str);
    return $str;
}

function conv_name($str)
{
    $str = str_replace(array('É'), 'e', $str);
    $str = strtolower(conv($str));
    $str = str_replace(array('é', 'è', 'ë', 'ê'), 'e', $str);
    $str = str_replace(array('à', 'ä', 'â'), 'a', $str);
    $str = str_replace(array('î', 'ï'), 'i', $str);
    $str = str_replace(array('ç'), 'c', $str);
    return preg_replace("/[^a-z0-9_-]/", "", $str);
}

XDB::execute('DROP DATABASE ' . $globals->dbdb);
echo 'DB droped ' . "\n";
XDB::execute('CREATE DATABASE ' . $globals->dbdb . ' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
XDB::execute('USE ' . $globals->dbdb);
echo 'DB created ' . "\n";

echo "-----------------------------------------------\n";

// Import "static" tables
$tables = glob("sql/*.sql");
foreach ($tables as $table) {
    echo exec('mysql -h ' . $globals->dbhost . ' -u ' . $globals->dbuser .
              ' -p' . $globals->dbpwd . ' ' . $globals->dbdb . ' < ' . $table);
    echo 'Imported ' . $table . "\n";
}

echo "-----------------------------------------------\n";

// Populating Groups
$iter = XDB::iterator("SELECT  b.binet_id, b.nom, b.description, b.http, b.mail, COUNT(m.binet_id) AS score
                         FROM  trombino.binets AS b
                    LEFT JOIN  trombino.membres AS m ON m.binet_id = b.binet_id
                     GROUP BY  b.binet_id");

$groups = $iter->total();
$k = 0;
while ($datas = $iter->next()) {
    $g = new Group();
    $g->insert($datas['binet_id']);
    $g->label(conv($datas['nom']));

    $name = conv_name($datas['nom']);
    $gf = new GroupFilter(new GFC_Name($name));
    if (strlen($name) >= 2 && $gf->getTotalCount() == 0) {
        $g->name($name);
    } else {
        $g->name('g_' . $g->id());
    }

    if ($datas['score'] > 99) {
        $g->ns(Group::NS_BINET);
    } else {
        $g->ns(Group::NS_FREE);
    }

    $g->description(conv($datas['description']));
    $g->external(0);
    $g->priv(0);
    $g->leavable(1);
    $g->visible(1);
    $g->web($datas['http']);
    $g->mail($datas['mail']);

    $k++;
    echo 'Group ' . $k . '/' . $groups . ' : ' . $g->id() . " - " . $g->label() . "\n";
}

echo "-----------------------------------------------\n";

// Populating Nationalities
$iter = XDB::iterator("SELECT  nation
                         FROM  trombino.eleves
                        WHERE  nation IS NOT NULL
                     GROUP BY  nation");

$nations = $iter->total();
$k = 0;
while ($datas = $iter->next()) {
    $g = new Group();
    $g->insert();
    $g->label($datas['nation']);
    $g->name('nation_' . conv_name($datas['nation']));
    $g->ns(Group::NS_NATIONALITY);
    $g->external(0);
    $g->priv(1);
    $g->leavable(0);
    $g->visible(0);

    $k++;
    echo 'Nation ' . $k . '/' . $nations . ' : ' . $g->id() . " - " . $g->label() . "\n";
}

echo "-----------------------------------------------\n";

// Populating Sports
$iter = XDB::iterator("SELECT  nom
                         FROM  trombino.sections");

$sports = $iter->total();
$k = 0;
while ($datas = $iter->next()) {
    $g = new Group();
    $g->insert();
    $g->label($datas['nom']);
    $g->name('sport_' . conv_name($datas['nom']));
    $g->ns(Group::NS_SPORT);
    $g->external(0);
    $g->priv(1);
    $g->leavable(0);
    $g->visible(0);

    $k++;
    echo 'Sport ' . $k . '/' . $sports . ' : ' . $g->id() . " - " . $g->label() . "\n";
}

echo "-----------------------------------------------\n";

// Populating accounts
$iter = XDB::iterator('SELECT  c.eleve_id, c.passwd,
                               e.nom, e.prenom, e.surnom, e.instrument,
                               e.date_nais, e.sexe, e.piece_id, s.nom AS sport,
                               e.promo, e.login, e.mail, e.nation, e.programme, e.portable,
                               SUBSTR(p.mail, 1, LENGTH(p.mail) - 18) AS hruid
                         FROM  frankiz2.compte_frankiz AS c
                   INNER JOIN  trombino.eleves AS e       ON c.eleve_id = e.eleve_id
                    LEFT JOIN  frankiz2.poly_mailedu AS p ON (p.poly = e.login AND p.promo = e.promo)
                    LEFT JOIN  trombino.sections AS s     ON s.section_id = e.section_id
                        WHERE  e.promo != 0000');

$users = $iter->total();
$k = 0;
while ($datas = $iter->next()) {
    // Creating the User
    $u = new User();
    $u->insert($datas['eleve_id']);
    $u->password($datas['passwd'], false);
    $u->firstname(conv($datas['prenom']));
    $u->lastname(conv($datas['nom']));
    $u->nickname(conv($datas['surnom']));
    $u->birthdate(new FrankizDateTime($datas['date_nais']));
    $u->gender(($datas['sexe'] == 1) ? User::GENDER_FEMALE : User::GENDER_MALE);
    $u->cellphone($datas['portable']);
    $u->poly($datas['login']);

    // Linking with the room
    $room = $datas['piece_id'];
    if (!empty($room)) {
        if (preg_match('/^[0-9]+[a-z]?$/', $room)) {
            $room = 'X' . $room;
        }
        try {
            $room = new Room($room);
            $u->addRoom($room);
        } catch (NotAnIdException $e) {
            echo 'Error for room ' . $datas['piece_id'];
        }
    }

    if (!empty($datas['hruid'])) {
        $login = $datas['hruid'];
        switch ($datas['programme']) {
            case 1: // X
            $formation_id = 1;
            break;

            case 4: // Doctorant
            $formation_id = 4;
            break;

            case 5: // PEI
            $formation_id = 5;
            break;

            default: // Master
            $formation_id = 3;
        }
    } else {
        $login = $datas['login'] . '.' . $datas['promo'];
        $formation_id = 2;
    }
    $u->login($login);
    $u->addStudy($formation_id, $datas['promo'], (int) $datas['promo'] + 4, $datas['promo'], $login);

    // Linking with the nationality
    if (!empty($datas['nation'])) {
        $nf = new GroupFilter(new GFC_Name('nation_' . conv_name($datas['nation'])));
        $n = $nf->get(true);
        $n->select(Group::SELECT_CASTES);
        $n->caste(Rights::member())->addUser($u);
    }

    // Linking with the sport
    if (!empty($datas['sport'])) {
        $nf = new GroupFilter(new GFC_Name('sport_' . conv_name($datas['sport'])));
        $n = $nf->get(true);
        $n->select(Group::SELECT_CASTES);
        $n->caste(Rights::member())->addUser($u);
    }

    // Linking the User with his groups
    $g_iter = XDB::iterator("SELECT  m.binet_id, m.remarque
                               FROM  trombino.membres AS m
                              WHERE  m.eleve_id = {?}", $u->id());
    $l = 0;
    while ($g_datas = $g_iter->next()) {
        $g = new Group($g_datas['binet_id']);
        $g->select(Group::SELECT_CASTES);
        $g->caste(Rights::member())->addUser($u);
        $u->comment($g, conv($g_datas['remarque']));
        $l++;

        if ($g->id() == 1 && strlen(conv_name($u->nickname())) > 1) {
            $u->addStudy(0, $datas['promo'], (int) $datas['promo'] + 4, $datas['promo'], conv_name($u->nickname()));
        }
    }

    $k++;
    echo 'User ' . $k . '/' . $users . ' : ' . $u->id() . ' - ' . $datas['promo'] . ' - '
                                             . $l . " groups - " . $u->login() . "\n";
}

echo "-----------------------------------------------\n";

XDB::execute('UPDATE account SET skin = "default"');
echo "default skin set \n";

XDB::execute('INSERT INTO users_minimodules (SELECT uid, "days",     "COL_LEFT",   0 FROM account)');
XDB::execute('INSERT INTO users_minimodules (SELECT uid, "ik",       "COL_LEFT",   1 FROM account)');
XDB::execute('INSERT INTO users_minimodules (SELECT uid, "meteo",    "COL_MIDDLE", 0 FROM account)');
XDB::execute('INSERT INTO users_minimodules (SELECT uid, "jtx",      "COL_MIDDLE", 1 FROM account)');
//XDB::execute('INSERT INTO users_minimodules (SELECT uid, "qdj",      "COL_RIGHT",  0 FROM account)');
XDB::execute('INSERT INTO users_minimodules (SELECT uid, "groups",   "COL_RIGHT",  1 FROM account)');
XDB::execute('INSERT INTO users_minimodules (SELECT uid, "quicksearch", "COL_FLOAT",  0 FROM account)');
XDB::execute('INSERT INTO users_minimodules (SELECT uid, "timeleft", "COL_FLOAT",  1 FROM account)');
XDB::execute('INSERT INTO users_minimodules (SELECT uid, "todo",     "COL_FLOAT",  2 FROM account)');
echo "default minimodules set \n";

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>