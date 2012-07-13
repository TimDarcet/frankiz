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

require dirname(__FILE__) . '../../connect.db.inc.php';

$gf = new GroupFilter();
$groups = $gf->get()->select(GroupSelect::base());
$groups->select(GroupSelect::castes());
$cf = new CasteFilter();
$castes = $cf->get()->select(CasteSelect::base());
echo "Checking " . $groups->count() . " groups and " . $castes->count() . " castes.\n";
//$available_rights = Rights::rights();
$available_rights = array('admin', 'logic', 'member', 'friend', 'restricted', 'everybody');
unset($gf);
unset($cf);

// Get webmaster caste
$webs = Group::from('webmasters')->select(GroupSelect::castes());
$ufc_web = new UFC_Caste(intval($webs->caste(Rights::member())->id()));
unset($webs);

// Get kes admins
$kes = Group::from('kes')->select(GroupSelect::castes());
$ufc_kes = new UFC_Caste(intval($kes->caste(Rights::admin())->id()));
unset($kes);

// Shalom-CCX-AMEP
$shccxamep_groups = new Collection();
$shccxamep_groups->add(Group::from('shalom'));
$shccxamep_groups->add(Group::from('ccx'));
$shccxamep_groups->add(Group::from('amep'));
$shccxamep_cf = new CasteFilter(new PFC_And(new CFC_Group($shccxamep_groups, Rights::admin())));
$shccxamep_castes = $shccxamep_cf->get();
$ufc_shalom_ccx_amep = new UFC_Caste(array_map('intval', $shccxamep_castes->ids()));
unset($shccxamep_groups);
unset($shccxamep_cf);
unset($shccxamep_castes);

// licenses members = on_platal and X
$on_platal = Group::from('on_platal')->select(GroupSelect::castes());
$formation_x = Group::from('formation_x')->select(GroupSelect::castes());
$ufc_licenses = new PFC_And(array(
    new UFC_Caste(intval($formation_x->caste(Rights::restricted())->id())),
    new UFC_Caste(intval($on_platal->caste(Rights::restricted())->id()))
));
unset($on_platal);
unset($formation_x);

// Get formations
$iter = XDB::iterRow("SELECT  formation_id, abbrev FROM  formations");
$formations = array();
while (list($fid, $fabbr) = $iter->next()) {
    $formations[$fabbr] = intval($fid);
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

$used_castes = array();
foreach ($groups as $g) {
    $gtext = $g->id() . " (" . $g->name() . ")";

    // For each group, get castes in an array, indexed by rights
    $gc = array();
    $gcid = array();
    foreach ($g->castes() as $c) {
        $cright = (string)($c->rights());
        if (!in_array($cright, $available_rights)) {
            echo "Error: caste " . $c->id() . " has invalid rights " . $cright . "\n";
            continue;
        }
        $gcid[$cright] = intval($c->id());
        $gc[$cright] = $castes->get($c->id());
        array_push($used_castes, $c->id());
    }

    if (!count($gc)) {
        echo "Error: user group " . $gtext . " does not have any caste !\n";
        continue;
    }

    // Check userfilters
    if ($g->ns() == 'user') {
        if (!preg_match('/^user_[1-9][0-9]*$/', $g->name())) {
            echo "Warning: user group " . $gtext . " has an invalid name\n";
        }
        if (count($gc) != 2 || !isset($gc['admin']) || !isset($gc['restricted'])) {
            echo "Error: user group " . $gtext . " has invalid castes, "
                . implode(', ', array_keys($gc)) . "\n";
            continue;
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
                        array_push($promos, new UFC_Promo($data['condition']['children'][$i]['promo']));
                    }
                }
                test_userfilters($gtext, 'member', $gc['member'], new PFC_Or($promos));
            } elseif (!preg_match('/^promo_([0-9]{4})$/', $g->name(), $matches)) {
                echo "Warning: promo group " . $gtext . " has an invalid name\n";
            } else {
                test_userfilters($gtext, 'member', $gc['member'],
                    new UFC_Promo($matches[1]));
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
            if (!preg_match('/^pa_[a-z0-9]+$/', $g->name())
            && !preg_match('/^modal[a-z0-9-]+$/', $g->name())) {
                echo "Warning: course group " . $gtext . " has an invalid name\n";
            }
        } elseif ($g->ns() == 'sport') {
            if (!preg_match('/^sport_[a-z]+$/', $g->name())) {
                echo "Warning: sport group " . $gtext . " has an invalid name\n";
            }
        } elseif (!in_array($g->ns(), array('study', 'promo', 'binet', 'free'))) {
            echo "Error: Unknown NS " . $g->ns() . " for group " . $gtext . "\n";
        }
    }
}

// Unused castes
$unused_castes = array_diff($castes->ids(), $used_castes);
if (!empty($unused_castes)) {
    echo "Warning: " . count($unused_castes) . " unused castes\n";
}
echo "Done.\n";

?>
