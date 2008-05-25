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


require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

demande_authentification(AUTH_INTERNE);


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
			$msg = "Compte créé avec succès. Un mail a été envoyé à ton adresse $forlife@polytechnique.edu avec ton mot de passe.";
		}
		else{
			$msg = "Un problème est survenu et ton compte n'a pas pu être créé. Contacte un <a href=\"mailto:robotran@frankiz.polytechnique.fr\">administrateur du système</a> ";
		}
	}else{
		$ins="UPDATE auth SET mdp = \"".md5($mdp)."\" WHERE forlife=\"$forlife\";";
		$res=mysql_query($ins);
		if($res){
			sendmdp($forlife,$mdp);
			$msg = "Compte mise à jour avec succès. Un mail a été envoyé à ton adresse $forlife@polytechnique.edu avec ton mot de passe. Tu peux retourner maintenant à la <a href=\"index.php\">page d'accueil</a>.";
		}
		else{
			$msg = "Un problème est survenu et ton compte n'a pas pu être changé. Contacte un <a href=\"mailto:robotran@frankiz.polytechnique.fr\">administrateur du système</a>";
		}
	}
}

require BASE_LOCAL."/include/page_header.inc.php";
?><page id='robotran' titre='Frankiz : Comptes Robotran'>
<?php

if(isset($errmsg) && $errmsg != ""){
	echo("<warning>$errmsg</warning>\n");
}
?>

<h1>Comptes Robotran</h1>
Dans cette page tu peux créer un compte pour demander des codes pour les machines traditionnelles 
de Robotran à Fayolle ou au PEM.
<?php
if(isset($msg)){
	echo '<note>'.$msg.'</note>';
}
?>

<note>Ton identifiant est la partie de ton adresse mail qui vient avant @polytechnique.edu. (En général, prenom.nom)</note>
<formulaire id="createaccount" action="robotran/new_account.php" titre="Création de compte">
  <champ id="forlife" titre="Identifiant"/>
  <bouton id="envoyer" value="Continuer" titre="Créer"/>
</formulaire>

</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>

