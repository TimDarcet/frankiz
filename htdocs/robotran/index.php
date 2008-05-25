<?php

require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

demande_authentification(AUTH_INTERNE);

$not_authed = true;

require_once "./robotran_mysql.php";
// Login (sans session frankiz)
if(isset($_POST['forlife']) && isset($_POST['passwd_robotran'])){
	$forlife=$_POST['forlife'];
	$mdp=$_POST['passwd_robotran'];
	$req="SELECT mdp FROM robotran.auth WHERE forlife=\"".$forlife."\" AND mdp='".md5($mdp)."';";
	$res=mysql_query($req);
	if(mysql_num_rows($res)!=1){
		$errmsg = "Erreur de connexion. Le couple identifiant/mot de passe ne correspond pas.";
	}
	else{
		$not_authed = false;
	}
}
// Demande de nouveau code
else if(isset($_SESSION['forlife']) && isset($_POST['batiment'])){
	$not_authed = false;
	$bat=$_POST['batiment'];
	$forlife=$_SESSION['forlife'];
	$libre = "SELECT code FROM robotran.codes WHERE forlife IS NULL AND batiment = \"".$bat."\";";
	$verif="SELECT code FROM robotran.codes WHERE forlife=\"".$forlife."\" AND timestamp >= (NOW() - INTERVAL 1 WEEK);";
	$getcode="UPDATE robotran.codes SET forlife=\"".$forlife."\",timestamp=now() WHERE batiment=\"".$bat."\" AND forlife IS NULL LIMIT 1;";
	if($bat==''){
		$errdemande = "Il faut que tu choisisses un groupe de machines à laver.";
	}
	else{
		$res=mysql_query($libre);
		if(mysql_num_rows($res)==0){
			$errdemande="Il n'y a plus de code dans notre base pour le groupe de machines demandé.";
		}
		else{
			$res=mysql_query($verif);
			if(mysql_num_rows($res)>=4){
				$errdemande = "Tu as déjà demandé 4 codes pour les machines à laver cette semaine ! Pour en avoir plus, entrer en contact avec <a href=\"mailto:robotran@frankiz.polytechnique.fr\">robotran@frankiz.polytechnique.fr</a> en justifiant le besoin de plus de 4 codes.";
			}
			else{
				$res=mysql_query($getcode);
			}
		}
	}
}
// Login (session frankiz)
if(isset($_SESSION['user'])) {
  $eleve_id = $_SESSION['user']->uid;
  $polyedu = "SELECT polyedu FROM trombino.eleves WHERE eleve_id = $eleve_id";
  $res = mysql_query($polyedu);
  if (mysql_num_rows($res)==1) {
    $row = mysql_fetch_array($res, MYSQL_ASSOC);
    $forlife = $row['polyedu'];
    $not_authed = false;
  }
}


require BASE_LOCAL."/include/page_header.inc.php";
?><page id='robotran' titre='Frankiz : Codes Robotran'>
<?php

if($not_authed){
?>
<?php
if( isset($errmsg) && $errmsg != ""){
	echo("<warning>$errmsg</warning>\n");
}
?>

<h1>Authentification requise</h1>
Pour accéder à la page demandée, tu dois t'identifier.
Pour cela, indique ton adresse mail polytechnique.edu (de la forme prenom.nom) et ton mot de passe pour ce site.

<p>Si celle-là est ta première connexion, tu peux créer ton compte <a href="robotran/new_account.php">ici</a>.</p>

<note>Ton identifiant est la partie de ton adresse mail qui vient avant @polytechnique.edu. (En général, prenom.nom)</note>
<formulaire id="login_robotran" titre="Connexion" action="robotran/">
	<champ id="forlife" titre="Identifiant" valeur="<?php if(isset($_POST['forlife'])) echo $_POST['forlife']?>"/>
	<champ id="passwd_robotran" titre="Mot de passe" valeur=""/>
	<bouton id="connect" titre="Continuer"/>
</formulaire>

<?php
}else{
	$_SESSION['forlife']=$forlife;
?>
<h1>Tu es maintenant authentifié en tant que <?php echo "$forlife"?>.</h1>

<h2>Liste de codes dont tu disposes déjà:</h2>
<?php
require_once "./robotran_mysql.php";
$lst="SELECT code,batiment FROM robotran.codes WHERE forlife = \"".$forlife."\" ORDER BY timestamp;";
$res=mysql_query($lst);
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	$code=$row['code'];
	$bat=$row['batiment'];
	echo "<p>*".$code."# (".$bat.")</p>";
}
?>
<formulaire id="forlife" titre="Demande de nouveau code" action="">
<?php
if(isset($errdemande) && $errdemande != ""){
	echo("<warning>$errdemande</warning>\n");
}
?>
  <choix titre="Batiment" id="batiment" type="combo" valeur="<?php echo empty($_REQUEST['batiment']) ? '' : $_REQUEST['batiment']; ?>">
    <option titre="   " id=""/>
    <option titre="Fayolle" id="Fayolle"/>
    <option titre="PEM" id="PEM"/>
  </choix>
  <bouton id='envoyer' titre='Demander'/>
</formulaire>
<?php
}
?>
</page>
<?php
require BASE_LOCAL."/include/page_footer.inc.php";

?>
