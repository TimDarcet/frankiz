<?php
/*
	Fichier de définition de variables et constantes utiles dans tout le projet.
	C'est de ce script que sont inclus tous les fichiers inclus pour toutes les pages
	et _qui inclus du code en dehors de fonctions_ comme erreursphp.inc.php, login.inc.php,
	skin.inc.php mais pas user.inc.php, xml.inc.php
	
	$Log$
	Revision 1.17  2004/10/04 21:48:08  schmurtz
	Modification des chemins d'acceÌ€s vers divers eÌleÌments.

	Revision 1.16  2004/09/20 20:33:47  schmurtz
	Mise en place d'un systeme de cache propre
	
	Revision 1.15  2004/09/18 16:22:26  kikx
	micro bug fix
	
	Revision 1.13  2004/09/17 14:19:58  kikx
	Page de demande d'annonce terminé
	Ajout d'une page de validations d'annonces
	
	Revision 1.12  2004/09/17 13:12:18  schmurtz
	Suppression des <![CDATA[...]>> car les donneÌes des GET et POST (et donc de la base de donneÌes) sont maintenant eÌchappeÌes avec des &amp; &lt; &apos;...
	
	Revision 1.11  2004/09/17 11:15:21  schmurtz
	Echappement des caracteres < > & dans les variables $_POST $_GET $_REQUEST
	
	Revision 1.10  2004/09/17 09:05:32  kikx
	La personne peut maintenant rajouter une annonce
	Ceci dit je ne comprend pas trop comment on protège les champs avec les <!CDATA
	-> j'ai laisser ca comme ca mais faudra modifier
	
	Revision 1.9  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.8  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

// Définition générique des différentes bases pour l'accès aux fichiers
foreach(Array('.', '..', '../..', '../../..') as $dir)
if(file_exists("$dir/frankiz.dtd"))
	$href = $dir;

define('BASE_LOCAL',realpath(dirname(__FILE__)."/.."));
define('BASE_URL','http://'.$_SERVER['HTTP_HOST'].'/'.substr((dirname($_SERVER['PHP_SELF']).'/'.$href), 1));

// Configuration du site
define('AFFICHER_LES_ERREURS',$_SERVER["SERVER_ADDR"] == "129.104.201.52");	// seulement sur gwennoz
define('BASE_PHOTOS',BASE_LOCAL."/../data/photos/");
define('BASE_CACHE',BASE_LOCAL."/../cache/");
define('MAIL_WEBMESTRE',"webmestre@frankiz.polytechnique.fr");
define('MAX_PEREMPTION',8);
define('UPLOAD_WEB_DIR',"upload_web/");

// Gestion des erreurs PHP et MySQL
// Il est important d'inclure ce fichier le plus tôt possible, mais comme il a besoin
// des paramètres du site on ne l'inclu que maintenant.
require_once "erreursphp.inc.php";

// Gestion des erreurs dans les formulaires
$i=1;
define('ERR_LOGIN',$i++);
define('ERR_MAILLOGIN',$i++);
define('ERR_LOGINPOLY',$i++);
define('ERR_MDP_DIFFERENTS',$i++);
define('ERR_MDP_TROP_PETIT',$i++);
define('ERR_SURNOM_TROP_PETIT',$i++);
define('ERR_EMAIL_NON_VALIDE',$i++);
define('ERR_TROP_COURT',$i++);
define('ERR_SELECTION_VIDE',$i++);

// Nettoyage des éléments récupérés par $_POST, $_GET et $_REQUEST
function nettoyage_balise($tableau) {
	foreach($tableau as $cle => $valeur)
		if(is_array($valeur)) $tableau[$cle] = nettoyage_balise($valeur);
		else $tableau[$cle] = str_replace(array('&','<','>','\'','"','\\'),array('&amp;','&lt;','&gt;','&apos;','&quot;',''),$valeur);
	return $tableau;
}

$_GET = nettoyage_balise($_GET);
$_POST = nettoyage_balise($_POST);
$_REQUEST = nettoyage_balise($_REQUEST);

// Connexions aux bases mysql
require_once "mysql.inc.php";
$DB_web = new DB("frankiz","frankiz2_tmp","web","kokouije?.");
$DB_admin = new DB("frankiz","admin","web","kokouije?.");
$DB_trombino = new DB("frankiz","trombino","web","kokouije?.");
$DB_valid = new DB("frankiz","a_valider","web","kokouije?.");

// divers fichiers inclus
require_once "global_func.inc.php";
require_once "mail.inc.php";
require_once "login.inc.php";
require_once "skin.inc.php";

?>