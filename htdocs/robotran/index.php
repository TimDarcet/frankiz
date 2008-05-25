<?php

require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

demande_authentification(AUTH_INTERNE);

$not_authed = true;

require_once "./robotran_mysql.php";
// Login (sans session frankiz)
if(isset($_POST['login_edu']) && isset($_POST['passwd_robotran'])){
	$login_edu=$_POST['login_edu'];
	$mdp=$_POST['passwd_robotran'];
	$req="SELECT mdp FROM robotran.auth WHERE login_edu=\"".$login_edu."\" AND mdp='".md5($mdp)."';";
	$res=mysql_query($req);
	if(mysql_num_rows($res)!=1){
		$errmsg = "Erreur de connexion. Le couple identifiant/mot de passe ne correspond pas.";
	}
	else{
		$not_authed = false;
	}
}
// Demande de nouveau code
else if(isset($_SESSION['login_edu']) && isset($_POST['batiment'])){
	$not_authed = false;
	$bat=$_POST['batiment'];
	$login_edu=$_SESSION['login_edu'];
	$libre = "SELECT code FROM robotran.codes WHERE login_edu IS NULL AND batiment = \"".$bat."\" LIMIT 1;";// Savoir s'il reste au moins un code non attribué
	$verif="SELECT code FROM robotran.codes WHERE login_edu=\"".$login_edu."\" AND timestamp >= (NOW() - INTERVAL 1 WEEK);";
	$getcode="UPDATE robotran.codes SET login_edu=\"".$login_edu."\",timestamp=now() WHERE batiment=\"".$bat."\" AND login_edu IS NULL LIMIT 1;";
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
				$errdemande = "Tu as déjà demandé 4 codes pour les machines à laver cette semaine ! Pour en avoir plus, entre en contact avec <a href=\"mailto:robotran@frankiz.polytechnique.fr\">robotran@frankiz.polytechnique.fr</a> en justifiant le besoin de plus de 4 codes.";
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
  $req_polyedu = "SELECT polyedu FROM trombino.eleves WHERE eleve_id = $eleve_id";
  $res = mysql_query($req_polyedu);
  if (mysql_num_rows($res)==1) {
    $row = mysql_fetch_array($res, MYSQL_ASSOC);
    $login_edu = $row['polyedu'];
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

<h2>Authentification requise</h2>
Pour accéder à la page demandée, tu dois t'identifier.
<h3>Si tu as un compte frankiz :</h3>
Il suffit que tu <a href="login.php">te connectes</a> pour être identifié.

<h3>Dans le cas contraire :</h3>
<p>S'il s'agit de ta première connexion, tu peux créer ton compte <a href="robotran/new_account.php">ici</a>.</p>

Une fois cela effectué, utilises le formulaire ci-dessous pour t'identifier.
<note>Ton identifiant est la partie de ton adresse mail qui vient avant @polytechnique.edu, en général <em>prenom.nom</em>.</note>
<formulaire id="login_robotran" titre="Connexion Robotran" action="robotran/">
	<champ id="login_edu" titre="Identifiant" valeur="<?php if(isset($_POST['login_edu'])) echo $_POST['login_edu']?>"/>
	<champ id="passwd_robotran" titre="Mot de passe" valeur=""/>
	<bouton id="connect" titre="Continuer"/>
</formulaire>

<?php
}else{
	$_SESSION['login_edu']=$login_edu;
?>
<h2>Tu es maintenant authentifié en tant que <?php echo "$login_edu"?>.</h2>

<h3>Voici la liste des codes dont tu disposes :</h3>
<?php
require_once "./robotran_mysql.php";
$lst="SELECT code,batiment FROM robotran.codes WHERE login_edu = \"".$login_edu."\" ORDER BY timestamp DESC;";
$res=mysql_query($lst);
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	$code=$row['code'];
	$bat=$row['batiment'];
	echo "<p>*".$code."# (".$bat.")</p>";
}
?>
<formulaire id="ask_code" titre="Demande de nouveau code" action="robotran/">
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
