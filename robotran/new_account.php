<?php

function mdp($length = 8)
{
    $carac = 'AZERTYUIOPMLKJHGFDSQWXCVBNazertyuiopmlkjhgfdsqwxcvbn1234567890';
    $mdp   = '';
    for($i = 0 ; $i < $length ; $i++) {
        $mdp .= $carac[mt_rand(0, strlen($carac) - 1)];
    }
    return $mdp;

}

function sendmdp($forlife,$mdp){
require_once "../htdocs/include/mail.inc.php";
        $mail = new Mail( "Codes Robotran <robotran@frankiz.polytechnique.fr>"  , $forlife."@polytechnique.edu" , "Ton nouveau mot de passe pour demander des codes pour robotran",false,"", "robotran@frankiz.polytechnique.fr");
        $mail->setBody("Bonjour,\n\nTon nouveau mot de passe pour le site de distribution des codes d'acces aux machines à laver robotran (http://frankiz.polytechnique.fr/robotran/) est ".$mdp."\n\nTrès cordialement,\n--\nLe BR (pour la distribution des codes robotran)");
        $mail->send();
}


session_start();


if(isset($_POST['forlife'])){
require_once "./robotran_mysql.php";
	$forlife=$_POST['forlife'];
	$mdp=mdp();
	$req="SELECT mdp FROM auth WHERE forlife='$forlife';";
	$res=mysql_query($req);
	if(mysql_num_rows($res)==0){
		$ins="INSERT INTO auth (forlife,mdp) VALUES (\"$forlife\",\"".md5($mdp)."\");";
		$res=mysql_query($ins);
		if($res){
			sendmdp($forlife,$mdp);
			$msg = "Compte crée avec success. Tu vas recevoir un mail avec ton mot de passe dans pas longtemps dans ton adresse $forlife@polytechnique.edu .";
		}
		else{
			$msg = "Il y a eu un problème et ta compte n'a pas pu être crée. Contacte un <a href=\"mailto:robotran@frankiz.polytechnique.fr\">administrateur du système</a> ";
		}
	}else{
		$ins="UPDATE auth SET mdp = \"".md5($mdp)."\" WHERE forlife=\"$forlife\";";
		$res=mysql_query($ins);
		if($res){
			sendmdp($forlife,$mdp);
			$msg = "Compte mise à jour avec success. Tu vas recevoir un mail avec ton mot de passe dans pas longtemps dans ton adresse $forlife@polytechnique.edu . Tu peux rétourner maintenant à la <a href=\"index.php\">page d'accueil</a>.";
		}
		else{
			$msg = "Il y a eu un problème et ta compte n'a pas pu être changée. Contacte un <a href=\"mailto:robotran@frankiz.polytechnique.fr\">administrateur du système</a>";
		}
	}
}

echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title>Creation de compte</title>
</head>
<body onLoad="document.forlife.login.focus()">
<?php
if($errmsg != ""){
	echo("<h2 class=red>$errmsg</h2>\n");
}
?>
<h1>Creation de compte</h1>
Dans cette page tu peux créer une compte pour demander des codes pour les machines traditionnelles 
de robotran à Fayolle ou au PEM.
<?php
if(isset($msg)){
	echo '<h4 class=red>'.$msg.'</h4>';
}
?>
<form name="forlife" method = "post" action="new_account.php">
<table>
	<tr><td>Forlife :</td><td><input type=text name="forlife">@polytechnique.edu</td></tr>
	<tr><td colspan=2 align=center><input type=submit value="Continuer"></td></tr>
</form>
</body>
</html>

