#!/usr/bin/php -q
<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
 *  http://br.binets.fr/                                                   *
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
 * This script creates and updates the studies
 */

require_once(dirname(__FILE__) . '/../connect.db.inc.php');

/**
 * Update a group
 * @return Group instance
 */
function update_group($name, $label, $ns, Userfilter $filter) {
    $gf = new GroupFilter(new GFC_Name($name));
    $g = $gf->get(true);
    if ($g instanceof Group) {
        echo 'Updating ' . $label . ' (' . $name . ', ' . $g->id() . ') ';
        $g->select(GroupSelect::castes());
    } else {
        echo 'Creating ' . $label . ' (' . $name . ') ';
        $g = new Group();
        $g->insert();
        $g->ns($ns);
        $g->name($name);
        $g->label($label);
    }
    $c = $g->caste(Rights::member());
    $c->select(CasteSelect::base());
    $c->userfilter($filter);
    $c->compute();
    echo '... ' . $c->users()->count() . ' member(s)' . "\n";
    return $g;
}

/**
 * Create an image for a promotion, for a group, if needed
 */
function create_promo_image(Group $g, $promo) {
    $g->select(GroupSelect::base());
    if ($g->image())
        return;

    if ($promo % 2 == 0) {
        $upload = FrankizUpload::fromFile(dirname(__FILE__) . '/../images/rouje.png');
        $label = 'Chic à la rouje';
    } else {
        $upload = FrankizUpload::fromFile(dirname(__FILE__) . '/../images/jone.png');
        $label = 'Chic à la jone';
    }

    $i = new FrankizImage();
    $i->insert();
    $i->caste($g->caste(Rights::everybody()));
    $i->label($label);
    $i->image($upload, false);

    $g->image($i);
}

// Update formations
$formations = Formation::selectAll(FormationSelect::base());
foreach ($formations as $form) {
    // Update group
    $f = new UserFilter(new UFC_Study(new Formation($form->id())));
    $g = update_group('formation_' . $form->abbrev(), $form->label(), Group::NS_STUDY, $f);
    $g->description($form->description());

    // Admin caste
    if ($form->abbrev() == 'x') {
        $c = $g->caste(Rights::admin());
        $c->select(CasteSelect::base());
        if (!$c->userfilter()) {
           $uf_kes = new UserFilter(new UFC_Group(Group::from('kes'), Rights::admin()));
           $c->userfilter($uf_kes);
        }
    }

}

// Update promotions
$iter = XDB::iterRow('SELECT promo FROM studies GROUP BY promo ORDER BY promo');
while (list($promo) = $iter->next()) {
    $f = new UserFilter(new UFC_Promo($promo, '='));
    $g = update_group('promo_' . $promo, $promo, Group::NS_PROMO, $f);
    create_promo_image($g, $promo);
}

// Update promotions by formation
$iter = XDB::iterRow('SELECT  s.promo, s.formation_id, f.abbrev, f.label
                        FROM  studies AS s
                   LEFT JOIN  formations AS f ON (f.formation_id = s.formation_id)
                       WHERE  s.formation_id > 0
                    GROUP BY  s.promo, s.formation_id
                    ORDER BY  s.promo, s.formation_id ASC');
while (list($promo, $formation_id, $abbrev, $label) = $iter->next()) {
    $f = new UserFilter(new UFC_Promo($promo, '=', $formation_id));
    $g = update_group('promo_' . $abbrev . $promo, $promo . ' ' . $label, Group::NS_PROMO, $f);
    create_promo_image($g, $promo);
}

// Update on_platal, specifying the number of years a school remains on the platal
$onplatal_numyears = array(
    'x' => 2,
    'poly' => 0,
    'master' => 0,
    'doc' => 0,
    'pei' => 0,
    'iogs' => 0,
    'fkz' => 0,
    'stcyr' => 0,
    'ensta' => 2
);
$formations->select(FormationSelect::on_platal());
$formations->select(FormationSelect::promos());
$filters = array();
foreach ($formations as $f) {
    if (!isset($onplatal_numyears[$f->abbrev()])) {
        echo 'Warning: no numyears for ' . $f->label() . "\n";
        continue;
    }
    $numyears = $onplatal_numyears[$f->abbrev()];
    $promos = $f->promos();
    $onplatal_promos = new PlFlagSet();
    sort($promos);
    while ($numyears > 0) {
        $promo = array_pop($promos);
        if (is_null($promo))
            break;
        $onplatal_promos->addFlag($promo);
        $filters[] = new UFC_Promo($promo, '=', $f->id());
        $numyears --;
    }

    // Update on_platal logic classes
    $f->platalyears($onplatal_promos);
}
$f = new UserFilter(new PFC_Or($filters));
update_group('on_platal', 'Sur le platal', Group::NS_PROMO, $f);

echo "Done\n";

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
