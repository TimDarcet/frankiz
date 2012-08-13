#!/usr/bin/php -q
<?php
/***************************************************************************
 *  Copyright (C) 2011 Binet Réseau                                       *
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

/**
 * This script checks groups and castes integrity
 * Fixes are applied by CLI with php -a and include 'bin/connect.db.inc.php';
 * For example, to fix caste userfilters, you may execute a line of PHP code like:
 * $c = new Caste(12142); $c->userfilter('{"type":"user","condition":{"type":"caste","children":[12001,12002]}}');
 * Use $c->userfilter(false) to remove a Userfilter
 */

require_once(dirname(__FILE__) . '/../connect.db.inc.php');

//$available_rights = Rights::rights();
$available_rights = array('admin', 'logic', 'member', 'friend', 'restricted', 'everybody');

// Get webmaster caste
$ufc_web = new UFC_Group(Group::from('webmasters'), Rights::member());

// Get kes admins
$ufc_kes = new UFC_Group(Group::from('kes'), Rights::admin());

// Shalom-CCX-AMEP
$shccxamep_groups = new Collection();
$shccxamep_groups->add(Group::from('shalom'));
$shccxamep_groups->add(Group::from('ccx'));
$shccxamep_groups->add(Group::from('amep'));
$ufc_shalom_ccx_amep = new UFC_Group($shccxamep_groups, Rights::admin());
unset($shccxamep_groups);

// licenses members = on_platal and X
$ufc_licenses = new PFC_And(array(
    new UFC_Group(Group::from('formation_x'), Rights::restricted()),
    new UFC_Group(Group::from('on_platal'), Rights::restricted())
));

// Get formations
$formations = array();
foreach (Formation::selectAll(FormationSelect::base()) as $form) {
    $formations[$form->abbrev()] = intval($form->id());
}

// Test wether the userfilter which is in the database is the expected one
function test_userfilters($grouptext, $rights, $db_caste, $expected_condition = null)
{
    $castetext = $rights . " userfilter (caste " . $db_caste->id() . ")";
    $db_userfilter = $db_caste->userfilter();
    if (is_null($expected_condition)) {
        if ($db_userfilter) {
            echo "Info: group " . $grouptext . " has unexpected " . $castetext . " " .
                json_encode($db_userfilter->export()) . "\n";
        }
    } else {
        $expected_userfilter = new UserFilter($expected_condition);
        $expected_json = json_encode($expected_userfilter->export());
        if (!$db_userfilter) {
            echo "Error: group " . $grouptext . " does not have a " . $castetext . ", "
                . "expected was " . $expected_json . "\n";
        } else {
            $db_json = json_encode($db_userfilter->export());
            if ($db_json != $expected_json) {
                echo "Error: group " . $grouptext . " has invalid " . $castetext . "\n"
                    . "    Expected: " . $expected_json . "\n"
                    . "    Database: " . $db_json . "\n";
            }
        }
    }
}

function check_group(Group $g)
{
    global $available_rights, $formations;
    global $ufc_web, $ufc_kes, $ufc_shalom_ccx_amep, $ufc_licenses;
    $gtext = $g->id() . " (" . $g->name() . ")";

    // For each group, get castes in an array, indexed by rights
    $gc = array();
    $gcid = array();
    $g->castes()->select(CasteSelect::base());
    foreach ($g->castes() as $c) {
        $cright = (string)($c->rights());
        if (!in_array($cright, $available_rights)) {
            echo "Error: caste " . $c->id() . " has invalid rights " . $cright . "\n";
            continue;
        }
        $gcid[$cright] = intval($c->id());
        $gc[$cright] = $c;
    }

    if (!count($gc)) {
        echo "Error: user group " . $gtext . " does not have any caste !\n";
        return;
    }

    // Check userfilters
    if ($g->ns() == 'user') {
        if (!preg_match('/^user_[1-9][0-9]*$/', $g->name())) {
            echo "Warning: user group " . $gtext . " has an invalid name\n";
        }
        if (count($gc) != 2 || !isset($gc['admin']) || !isset($gc['restricted'])) {
            echo "Error: user group " . $gtext . " has invalid castes, "
                . implode(', ', array_keys($gc)) . "\n";
            return;
        }
        if ($gc['admin']->userfilter() != null) {
            echo "Warning: user group " . $gtext . " has an admin userfilter\n";
        }
        if ($gc['restricted']->userfilter() != null) {
            echo "Warning: user group " . $gtext . " has a restricted userfilter\n";
        }
    } else {
        // Everything else has all rights
        if (count($gc) != count($available_rights)) {
            echo $gtext . " has " . count($gc) . " castes "
                . implode(', ', array_keys($gc)) . "\n";
        }

        // Check restricted userfilter
        test_userfilters($gtext, 'restricted', $gc['restricted'],
            new UFC_Caste(array($gcid['admin'], $gcid['member'], $gcid['logic'])));

        // Check everybody userfilter
        test_userfilters($gtext, 'everybody', $gc['everybody'],
            new UFC_Caste(array($gcid['admin'], $gcid['member'], $gcid['logic'], $gcid['friend'])));

        // Check special group admins
        if (in_array($g->name(), array('on_platal', 'everybody', 'public', 'tol', 'postit', 'temp'))) {
            // Webmasters are automatically admins of some groups
            test_userfilters($gtext, 'admin', $gc['admin'], $ufc_web);
        } elseif ($g->name() == 'formation_x') {
            test_userfilters($gtext, 'admin', $gc['admin'], $ufc_kes);
        } elseif ($g->name() == 'adherents-kes') {
            test_userfilters($gtext, 'admin', $gc['admin'], $ufc_kes);
        } elseif ($g->name() == 'shalom-ccx-amep') {
            test_userfilters($gtext, 'admin', $gc['admin'], $ufc_shalom_ccx_amep);
        } else {
            test_userfilters($gtext, 'admin', $gc['admin']);
        }

        // Check special group members
        if ($g->name() == 'everybody') {
            test_userfilters($gtext, 'member', $gc['member'], new PFC_Not(new UFC_Uid(-1)));
        } elseif ($g->name() == 'public') {
            test_userfilters($gtext, 'member', $gc['member'], new PFC_True());
        } elseif ($g->name() == 'shalom-ccx-amep') {
            test_userfilters($gtext, 'member', $gc['member'], $ufc_shalom_ccx_amep);
        } elseif ($g->name() == 'licenses') {
            test_userfilters($gtext, 'member', $gc['member'], $ufc_licenses);
        } elseif ($g->ns() == 'promo') {
            $matches = array();
            if ($g->name() == 'on_platal') {
                $promos = array();
                if ($gc['member']->userfilter()) {
                    $data = $gc['member']->userfilter()->export();
                    for ($i = 0; isset($data['condition']['children'][$i]['promo']); $i++) {
                        // This only checks logic connectors
                        // TODO: Check years
                        $p = $data['condition']['children'][$i]['promo'];
                        $f = $data['condition']['children'][$i]['formation_id'];
                        array_push($promos, new UFC_Promo($p, '=', $f));
                    }
                }
                test_userfilters($gtext, 'member', $gc['member'], new PFC_Or($promos));
            } elseif (!preg_match('/^promo_([a-z]*)([0-9]{4})$/', $g->name(), $matches)) {
                echo "Warning: promo group " . $gtext . " has an invalid name\n";
            } else {
                $fid = ($matches[1] ? (string)$formations[$matches[1]] : 0);
                test_userfilters($gtext, 'member', $gc['member'],
                    new UFC_Promo($matches[2], '=', $fid));
            }
        } elseif ($g->ns() == 'study' || $g->name() == 'formation_fkz') {
            $matches = array();
            if (!preg_match('/^formation_([a-z]+)$/', $g->name(), $matches)) {
                echo "Warning: study group " . $gtext . " has an invalid name\n";
            } elseif (!isset($formations[$matches[1]])) {
                echo "Error: study group " . $gtext . " has an unknown formation\n";
            } else {
                test_userfilters($gtext, 'member', $gc['member'],
                    new UFC_Study((string)$formations[$matches[1]]));
            }
        } else {
            test_userfilters($gtext, 'member', $gc['member']);
        }

        test_userfilters($gtext, 'friend', $gc['friend']);

        // Check name
        if (!$g->ns()) {
            // Empty name means it is a special group
            if (!in_array($g->name(), array(
                'everybody', 'public', 'tol', 'postit', 'temp', 'postit',
                'licenses', 'formation_fkz'))) {
                echo "Warning: group " . $gtext . " has an empty namespace\n";
            }
        } elseif ($g->ns() == 'nationality') {
            if (!preg_match('/^nation_[a-z-]+$/', $g->name())) {
                echo "Warning: nationality group " . $gtext . " has an invalid name\n";
            }
        } elseif ($g->ns() == 'course') {
            if (!preg_match('/^pa_[a-z0-9-_]+$/', $g->name())
            && !preg_match('/^modal[a-z0-9-_]+$/', $g->name())) {
                echo "Warning: course group " . $gtext . " has an invalid name\n";
            }
        } elseif ($g->ns() == 'sport') {
            if (!preg_match('/^sport_[a-z]+$/', $g->name())) {
                echo "Warning: sport group " . $gtext . " has an invalid name\n";
            }
        } elseif ($g->ns() == 'bde') {
            if ($g->name() != 'adherents-kes') {
                echo "Warning: unknown BDE " . $gtext . "\n";
            }
        } elseif (!in_array($g->ns(), array('study', 'promo', 'binet', 'free'))) {
            echo "Error: Unknown NS " . $g->ns() . " for group " . $gtext . "\n";
        }
    }
}

// Fetch groups
$groups = Group::selectAll(GroupSelect::base())->select(GroupSelect::castes());
$groups = $groups->toArray();
krsort($groups);

// Remember used castes
$used_castes = array();
echo "Checking " . count($groups) . " groups\n";
while (!empty($groups)) {
    $g = array_pop($groups);
    check_group($g);
    foreach ($g->castes() as $c) {
        array_push($used_castes, $c->id());
    }
    // Frees memory
    unset($g);
}

// Fetch castes
$cf = new CasteFilter();
$castes = $cf->get();
unset($cf);
echo "There are " . $castes->count() . " castes.\n";

// Unused castes
$unused_castes = array_diff($castes->ids(), $used_castes);
if (!empty($unused_castes)) {
    echo "Warning: " . count($unused_castes) . " unused castes\n";
}
echo "Done.\n";

?>
