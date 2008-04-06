<?php

session_start();

$not_authed = true;
$target = $_GET['target'];

if(isset($_POST['login']) && isset($_POST['passwd'])){
include("squid_mysql.php");
	$pre_login=$_POST['login'];
	$passwd=$_POST['passwd'];
	$pre_login = explode(".",$pre_login) ;
	if (count($pre_login)!=2) {
		$login ="" ;
		$promo = "" ;
	} else {
		$login = $pre_login[0] ;
		$promo = $pre_login[1] ;
	}
	$req="SELECT eleves.eleve_id as id,login,nom,prenom,passwd FROM trombino.eleves LEFT JOIN compte_frankiz USING(eleve_id) WHERE login='".mysql_real_escape_string($login)."' AND promo='".mysql_real_escape_string($promo)."' ORDER BY promo DESC LIMIT 1";
	$res=mysql_query($req);
	if(mysql_num_rows($res)==1){
		$row=mysql_fetch_array($res, MYSQL_ASSOC);
		$nom=$row['nom'];
		$prenom=$row['prenom'];
		$hash_passwd=$row['passwd'];
		$id=$row['id'];
		if(crypt($passwd,$hash_passwd) == $hash_passwd){
			$not_authed = false;
		}else{
			$errmsg = "Erreur de connexion. Le couple login/mot de passe ne correspond pas.";
		}
	}else{
		$errmsg = "Erreur de connexion. Le couple login/mot de passe ne correspond pas.";
	}
}

if($not_authed){
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title>Authentification requise</title>
</head>
<body onLoad="document.squid_login.login.focus()">
<?php
if($errmsg != ""){
	echo("<h2 class=red>$errmsg</h2>\n");
}
?>
<h1>Authentification requise</h1>
Pour accéder à la page demandée, tu dois t'identifier.
Pour cela, indique ton login frankiz (loginpoly.promo) et ton mot de passe frankiz.
Une fois connecté, une pop-up apparaîtra, rappelant que tu es loggué. Si tu la fermes, tu devras à nouveau t'identifier.
<form name="squid_login" method = "post" action="squid_login.php?target=<?php echo $target?>">
<table>
	<tr><td>Login.promo :</td><td><input type=text name="login"></td></tr>
	<tr><td>Mot de passe :</td><td><input type=password name="passwd"></td></tr>
	<tr><td colspan=2 align=center><input type=submit value="Continuer"></td></tr>
</form>
</body>
</html>
<?php
}else{
	$_SESSION['nom']=$nom;
	$_SESSION['prenom']=$prenom;
	$_SESSION['id']=$id;
	$_SESSION['promo']=$promo;
	$_SESSION['rand']=rand(0,1024);
	if(isset($_SERVER['HTTP_CLIENT_IP'])){
	  $ip = $_SERVER['HTTP_CLIENT_IP'];
	}else{
	  $ip = $_SERVER['REMOTE_ADDR'];
	}
	$req="UPDATE squid_auth SET eleve_id='".mysql_real_escape_string($id)."', date_auth=NOW() WHERE ip='".mysql_real_escape_string($ip)."'";
	mysql_query($req) or die("Authentification correcte, mais communication impossible avec la base de données");
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Authentification réussie</title>
</head>
<body onLoad="auto_popup()">
<script language="javascript">
<!--
function popup_open(){
window.open("squid_popup.php", "squid_popup", config="height=50, width=200, toolbar=no, menubar=no, scrollbars=no, resizable=no, directories=no, statut=no");
}

function popup(){
popup_open();
window.location = "<?php echo($target);?>";
}

function auto_popup(){
setTimeout("popup_open()", 3000);
}
-->
</script>
<h1>Tu es maintenant authentifié en tant que <?php echo "$prenom $nom ($promo)"?>.</h1>
Pour continuer ta navigation, tu dois ouvrir une pop-up (si elle ne s'est pas encore ouverte). Ne la ferme pas, c'est elle qui va maintenir tes informations de connexion.
Pour l'ouvrir, <a href="javascript:popup()">clique ici</a>.
Quand tu auras fini ta navigation, clique sur le bouton "déconnecter" de la pop-up.

Nous te rappelons que des logs de ta session sont conservées, au même titre que la DSI conserve les logs de connexions internet des élèves.
<?php
}

?>
