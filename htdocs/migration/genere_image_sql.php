<?
require "../../include/global.inc.php";
$login=$_REQUEST['login'];
$promo=$_REQUEST['promo'];
$type=$_REQUEST['type'];

	connecter_mysql_tol();

	$query = "SELECT $type FROM photos WHERE login = \"$login\" and promo = \"$promo\" ORDER BY login,promo";
	$result = mysql_query($query);
	header('content-type: image/jpeg');
	echo  mysql_result($result,0);

	deconnecter_mysql_tol();
?>