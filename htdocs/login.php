<?php
require "include/global.inc.php";

demande_authentification(AUTH_MDP);

header("Location: ".BASE_URL."/");
?>
