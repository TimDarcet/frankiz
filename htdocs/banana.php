<?php
/********************************************************************************
* index.php : main page (newsgroups list)
* -----------
*
* This file is part of the banana distribution
* Copyright: See COPYING files that comes with this distribution
********************************************************************************/
require_once("banana/banana.inc.php");
require_once "include/global.inc.php";

demande_authentification(AUTH_MINIMUM);
require "include/page_header.inc.php";

$res = Banana::run();
?>
<page id="banana" titre="Frankiz : News sur le web">
	<?php echo $res; ?>
	<br/><br/><em>Banana</em>, interface web pour les serveurs NNTP<br/>
       	Développée sous license GPL par  
	<a href="http://www.polytechnique.org">Polytechnique.org</a>
</page>
<?php
require_once "include/page_footer.inc.php";
?>

