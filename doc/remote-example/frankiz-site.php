<?php
/**
 * This page is run when Frankiz goes back to your website
 */
require_once('frankiz-login.inc.php');

$response = frankiz_auth_response();
echo '<a href="frankiz-auth.php">Re-run auth</a><br />';
echo '<pre>';print_r($response);echo '</pre>';
?>
