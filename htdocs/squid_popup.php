<?php

session_start();
if(!isset($_SESSION['rand'])){
	die("Erreur dans la communication avec la Pop-up.");
}
$rand=$_SESSION['rand'];
$nom=$_SESSION['nom'];
$prenom=$_SESSION['prenom'];
$id=$_SESSION['id'];
$promo=$_SESSION['promo'];


echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Connecté en tant que <?php echo("$prenom $nom ($promo)");?></title>
</head>
<body onLoad="doCom()" onUnload="logout()">
<script language="javascript">
<!--
function logout(){
	sendmsg("logout");
//	window.close();
}

function trim (myString) 
{ 
	return myString.replace(/^\s+/g,'').replace(/\s+$/g,'') 
}

function doCom(){
	sendmsg("<?php echo $id."-".$rand;?>");
	setTimeout("doCom()", 30000);
}

function sendmsg(message)
{
    var xhr=null;
    if (window.XMLHttpRequest) { 
    	xhr = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) 
    {
    	xhr = new ActiveXObject("Microsoft.XMLHTTP");
    }
    //on définit l'appel de la fonction au retour serveur
    xhr.onreadystatechange = function() { alert_ajax(xhr, message); };

    //on appelle le fichier reponse.txt
//    alert("Envoi du message : http://frankiz/squid_keepconn.php?message=" + message);
    xhr.open("GET", "http://frankiz.eleves.polytechnique.fr/squid_keepconn.php?message=" + message, true);
    xhr.send(null);
}

function alert_ajax(xhr, message)
{
        if (xhr.readyState == 4 && xhr.status == 200){
//		alert("|" + xhr.responseText + "| " + message);
//		alert("--" + message + "--" + xhr.responseText + "--");
		if(message != "logout" && trim(xhr.responseText) != "OK"){
			alert("Erreur de communication avec le serveur. Votre connexion va être coupée.");
			logout();
		}
	}
}
-->
</script>
Bonjour,

Tu es connecté en tant que <?php echo "$prenom $nom ($promo)";?>.

Pour te déconnecter, <a href="javascript:logout()">clique ici</a>
</body>
