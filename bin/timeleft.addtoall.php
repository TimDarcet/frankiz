<?php
/**
 * Activate timeleft inimodue for every user
 */

require_once(dirname(__FILE__) . '/connect.db.inc.php');

// Get all users
$userfilter = new UserFilter(new UFC_Group(Group::from('on_platal')));
$users = $userfilter->get();
$users->select(UserSelect::minimodules());
$users->select(UserSelect::base());

// Get timeleft minimodules
$timeleft = FrankizMinimodule::get('timeleft');

foreach ($users as $u) {
    $minimodules = $u->minimodules();
    if (!in_array('timeleft', $minimodules)) {
        echo 'Adding minimdule to ' . $u->login() . PHP_EOL;
        $u->addMinimodule($timeleft);
    }
}
?>
