<?php

session_start();
include("squid_mysql.php");
if(!isset($_SESSION['rand'])){
        die("ERR");
}
$rand=$_SESSION['rand'];
$nom=$_SESSION['nom'];
$prenom=$_SESSION['prenom'];
$id=$_SESSION['id'];
$promo=$_SESSION['promo'];

if(isset($_SERVER['HTTP_CLIENT_IP'])){
  $ip = $_SERVER['HTTP_CLIENT_IP'];
}else{
  $ip = $_SERVER['REMOTE_ADDR'];
}

$msg=$_GET['message'];
if($msg == "logout"){
	$req="UPDATE squid_auth SET eleve_id=NULL where ip='".mysql_real_escape_string($ip)."'";//On déconnecte de force
	mysql_query($req);
	die();
}else{
	list($dist_id, $dist_rand) = split ("-", $msg);
	if(!is_numeric($dist_id) || is_null($id) || $id==0 || $dist_rand != $rand || $dist_id != $id){
		die("ERR");
	}
	$req="UPDATE squid_auth SET eleve_id='$id', date_auth=NOW() WHERE ip='".mysql_real_escape_string($ip)."'";//On maintient la connexion
	mysql_query($req) or die("ERR");
	echo("OK");
}
?>
