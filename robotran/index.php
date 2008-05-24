<?php

session_start();


$not_authed = true;
$target = $_GET['target'];

require_once "./robotran_mysql.php";
if(isset($_POST['forlife']) && isset($_POST['mdp'])){
	$forlife=$_POST['forlife'];
	$mdp=$_POST['mdp'];
	$req="SELECT mdp FROM robotran.auth WHERE forlife=\"".$forlife."\" AND mdp='".md5($mdp)."';";
	$res=mysql_query($req);
	if(mysql_num_rows($res)!=1){
		$errmsg = "Erreur de connexion. Le couple login/mot de passe ne correspond pas.";
	}
	else{
		$not_authed = false;
	}
} else if(isset($_SESSION['forlife']) && isset($_POST['batiment'])){
	$not_authed = false;
	$bat=$_POST['batiment'];
	$forlife=$_SESSION['forlife'];
	$libre = "SELECT code FROM codes WHERE forlife IS NULL AND batiment = \"".$bat."\";";
	$verif="SELECT code FROM codes WHERE forlife=\"".$forlife."\" AND timestamp >= (NOW() - INTERVAL 1 WEEK);";
	$getcode="UPDATE codes SET forlife=\"".$forlife."\",timestamp=now() WHERE batiment=\"".$bat."\" AND forlife IS NULL LIMIT 1;";
	if($bat==''){
		$errdemande = "Il faut que tu choisisses un groupe de machines à laver.";
	}
	else{
		$res=mysql_query($libre);
		if(mysql_num_rows($res)==0){
			$errdemande="Il n'y a plus de code pour le groupe de machines demandés dans notre base.";
		}
		else{
			$res=mysql_query($verif);
			if(mysql_num_rows($res)>=4){
				$errdemande = "Tu as déjà demandé 4 codes pour les machines à laver cette semaine! Pour un avoir plus entrer en contact avec <a href=\"mailto:robotran@frankiz.polytechnique.fr\">robotran@frankiz.polytechnique.fr</a> en justifiant le besoin de plus de 4 codes.";
			}
			else{
				$res=mysql_query($getcode);
			}
		}
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
<body onLoad="document.login_robotran.login.focus()">
<?php
if($errmsg != ""){
	echo("<h2 class=red>$errmsg</h2>\n");
}
?>
<h1>Authentification requise</h1>
Pour accéder à la page demandée, tu dois t'identifier.
Pour cela, indique ton adresse mail polytechnique.edu (prenom.nom) et ton mot de passe de ce site.
Si celle là est ta première connection il faut que tu crées ta compte 
<a href="new_account.php">ici</a>.
<form name="login_robotran" method = "post" action="index.php?target=<?php echo $target?>">
<table>
	<tr><td>Login :</td><td><input type=text name="forlife">@polytechnique.edu</td></tr>
	<tr><td>Mot de passe :</td><td><input type=password name="mdp"></td></tr>
	<tr><td colspan=2 align=center><input type=submit value="Continuer"></td></tr>
</form>
</body>
</html>
<?php
}else{
	$_SESSION['forlife']=$forlife;
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Authentification réussie</title>
</head>
<body>
<h1>Tu es maintenant authentifié en tant que <?php echo "$forlife"?>.</h1>

<h2>Liste de codes dont tu disposes déjà:</h2>
<?php
require_once "./robotran_mysql.php";
$lst="SELECT code,batiment FROM codes WHERE forlife = \"".$forlife."\" ORDER BY timestamp;";
$res=mysql_query($lst);
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	$code=$row['code'];
	$bat=$row['batiment'];
	echo "<p>*".$code."# (".$bat.")</p>";
}
?>
<h2>Demande de nouveau code</h2>
<?php
if($errdemande != ""){
	echo("<h3 class=red>$errdemande</h3>\n");
}
?>
<form name="forlife" method = "post" action="index.php?target=<?php echo $target?>">
<table>
	<tr><td>Groupe de machines :</td><td>
		<select name="batiment">
			<option value="">&nbsp;</option>
			<option value="Fayolle">Fayolle</option>
			<option value="PEM">PEM</option>
		</select>
	</td></tr>
        <tr><td colspan=2 align=center><input type=submit value="Demander"></td></tr>
</form>
</body>
</html>
<?php
}

?>
