<?php
/*
	Copyright (C) 2004 Binet Réseau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
/*
	Divers fonctions pouvant être utile dans n'importe quelles pages.
	Pas de fonctionnalités spécifiques à quelques pages.

	$Id$
*/
/*
	Gestion des erreurs dans les formulaires
*/

$_ERREURS = array();

function a_erreur($err) {
	global $_ERREURS;
	return isset($_ERREURS[$err]);
}

function ajoute_erreur($err) {
	global $_ERREURS;
	$_ERREURS[$err] = $err;
}

function aucune_erreur() {
	global $_ERREURS;
	return count($_ERREURS) == 0;
}

/*
	Crée un hash aléatoire de 16 caractères.
*/
function nouveau_hash() {
    $fp = fopen('/dev/urandom', 'r');
    $hash = md5(fread($fp, 16));
    fclose($fp);
    return $hash;
}

/*
 *  Crée un Hash de type shadow
 */
function hash_shadow($String) {
	$hash = '';
	for($i=0;$i<8;$i++){
		$j = mt_rand(0,53);
		if($j<26)
			$hash .= chr(rand(65,90));
		else if($j<52)
			$hash .= chr(rand(97,122));
		else if($j<53)
			$hash .= '.';
		else $hash .= '/';
	}
	return crypt($String,'$1$'.$hash.'$');
}

/*
	Envoi les données nécessaire pour faire une redirection vers la page donnée.
	Arrète l'exécution du code PHP.
*/
function rediriger_vers($page) {
	header("Location: ".BASE_URL.$page);
	echo "<p>Si ton navigateur n'est pas automatiquement redirigé, <a href=\"".BASE_URL.$page."\">clique ici</a>.</p>";
	exit;
}

/*
	Simule une erreur 403
*/
function acces_interdit() {
	header("HTTP/1.1 403 Forbidden");
	$_GET['erreur'] = 403;
	require BASE_LOCAL."/erreur.php";
	exit;
}

function est_interne()
{
	$ip = ip_get();
	return $ip == '127.0.0.1' || (substr($ip, 0, 8) == '129.104.' && $ip != '129.104.30.4');
}

/*
	Renvoi la liste des modules disponibles sous la forme d'une liste :
		"nom du fichier moins le .php" => "Nom affichable du module"
	
	Si le nom affichage est vide, cela signifie que le module est toujours visible.
*/
function liste_modules() {
	return array(
		"css"				=> "",
		"liens_navigation"	=> "",
		"liens_propositions"	=> "",
	"random"	    => "",
		"liens_utiles"		=> "Liens école",
		"qdj"				=> "Question du jour",
		"qdj_hier"			=> "Question de la veille",
		"meteo"			=> "Météo",
		"activites"			=> "Activités",
		"tour_kawa"		=> "Tours kawa",
		"anniversaires"		=> "Anniversaires",
		"fetes"		=> "Fête du jour",
		"stats"			=> "Statistiques",
		"sondages"		=> "Sondages",
		"lien_tol"			=> "Lien rapide vers le tol",
		"rss"				=> "News Extérieures",
		"liens_perso"		=> "Liens Perso",
		"annonce_virus"	=> "Attention Virus !"
		);
}/*
	Renvoi la liste des droits disponibles sous la forme d'une liste :
		"nom" => "droits"
	
	Si le nom affichage est vide, cela signifie que le module est toujours visible.
*/
function liste_droits() {
	return array(
		"admin"		=>	"Administrateur Total",
		"web"		=>	"Webmestre de Frankiz",
		"news"		=>	"Newsmestre de Frankiz",
		"qdjmaster"	=>	"QdjMaster",
		"xshare"	=>	"Xshare",
		"faq"		=>	"Faqmestre",
		"trombino"	=>	"TrombinoMen",
		"windows"	=>	"admin@windows",
		"support"	=>	"support@windows",
		"kes"		=>	"(EXT) Kessiers (mail promo)",
		"bob"		=>	"(EXT) BobarMen",
		"affiches"	=>	"(EXT) Affiches (ex BRC...)",
		"postit"	=>	"(EXT) Postit (droit temporaire, pour le .gamma par expl)"
		);
}

/*
	Gestion des caches :
	 - cache_supprimer() supprime un fichier de cache
	 - cache_recuperer() récupère et affiche le fichier de cache s'il est à jour
		sinon renvoie faux et ouvre un buffer pour récupérer la sortie à mettre en cache.
	 - cache_sauver() récupère le contenu du buffer ouvert par cache_recuperer(), l'écrit
		dans le fichier de cache et sur la sortie.
*/
function cache_supprimer($cache_id) {
	unlink(BASE_CACHE.$cache_id);
}

global $_CACHE_SAVED_BUFFER;	// TODO corriger ce hack tout moche qui se résoud avec PHP 4.2.0
								// qui autorise d'avoir des buffers imbriqués
								// Il suffira alors de supprimer les lignes finisant par "// hack"

function cache_recuperer($cache_id,$date_valide_max) {
	if(file_exists(BASE_CACHE.$cache_id) && filemtime(BASE_CACHE.$cache_id) > $date_valide_max) {
		readfile(BASE_CACHE.$cache_id);
		return true;
	} else {
		global $_CACHE_SAVED_BUFFER;				// hack
		$_CACHE_SAVED_BUFFER = ob_get_contents();	// hack
		ob_end_clean();								// hack
		ob_start();
		return false;
	}
}

function cache_sauver($cache_id) {
	$contenu = ob_get_contents();
	ob_end_clean();

	$file = fopen(BASE_CACHE.$cache_id, 'w');
	fwrite($file, $contenu);
	fclose($file);		 

	global $_CACHE_SAVED_BUFFER;					// hack
	ob_start();										// hack
	echo $_CACHE_SAVED_BUFFER;						// hack
	echo $contenu;
}

/*
	Fonction de décodage du sondage
*/
function decode_sondage($string) {
	$string = explode("###",$string) ;
	for ($i=1 ; $i<count($string) ; $i++) {
		$temp = explode("///",$string[$i]) ;
		if ($temp[0]=="expli") {
			echo "<note>$temp[1]</note>\n" ;
		}
		if ($temp[0]=="champ") {
			echo "<champ id=\"$i\" titre=\"$temp[1]\" valeur=\"\"/><br/>\n" ;
		}
		if ($temp[0]=="text") {
			echo "<zonetext id=\"$i\" titre=\"$temp[1]\"></zonetext><br/>\n" ;
		}
		if ($temp[0]=="radio") {
			echo "<choix titre=\"$temp[1]\" id=\"$i\" type=\"radio\" valeur=\"\">\n" ;
			for ($j=2 ; $j<count($temp) ; $j++) {
				echo "\t<option titre=\"".$temp[$j]."\" id=\"".($j-1)."\"/>\n";
			}	
			echo "</choix><br/>\n" ;
		}
		if ($temp[0]=="combo") {
			echo "<choix titre=\"$temp[1]\" id=\"$i\" type=\"combo\" valeur=\"\">\n" ;
			for ($j=2 ; $j<count($temp) ; $j++) {
				echo "\t<option titre=\"".$temp[$j]."\" id=\"".($j-1)."\"/>\n";
			}	
			echo "</choix><br/>\n" ;
		}
		if ($temp[0]=="check") {
			echo "<choix titre=\"$temp[1]\" id=\"$i\" type=\"checkbox\" valeur=\"\">\n" ;
			for ($j=2 ; $j<count($temp) ; $j++) {
				echo "\t<option titre=\"".$temp[$j]."\" id=\"{$i}_".($j-1)."\"/>\n";
			}	
			echo "</choix><br/>\n" ;
		}
	}
}
// Returns a nicely formatted html string
// POUR FAIRE LE DIFF ENTRE 2 STRINGS
// PAS ENCORE TESTER ... (KIKX)
function diff_rek(&$a1,&$a2,$D,$k,&$vbck)
{
 $x=$vbck[$D][$k]; $y=$x-$k;
 if ($D==0)
 {
  if ($x==0) return array(array(),array());
  else
  return array(array_slice($a1,0,$x),array_fill(0,$x,"b"));
 }
 if (isset($vbck[$D-1][$k+1])) 
 	$x2=$vbck[$D-1][$k+1];
 else 
 	$x2=0 ;
 $y2=$vbck[$D-1][$k-1]-($k-1);
 $xdif=$x-$x2; $ydif=$y-$y2;
 $l=min($x-$x2,$y-$y2);
 $x=$x-$l;
 $y=$y-$l;
 if ($x==$x2)
 {
   $res=diff_rek($a1,$a2,$D-1,$k+1,$vbck);
   array_push($res[0],$a2[$y-1]);
   array_push($res[1],"2");
   if ($l>0)
   {
   $res[0]=array_merge($res[0],array_slice($a2,$y,$l));
   $res[1]=array_merge($res[1],array_fill(0,$l,"b"));
   }
 }
 else
 {
   $res=diff_rek($a1,$a2,$D-1,$k-1,$vbck);
   array_push($res[0],$a1[$x-1]);
   array_push($res[1],"1");
   if ($l>0)
   {
   $res[0]=array_merge($res[0],array_slice($a1,$x,$l));
   $res[1]=array_merge($res[1],array_fill(0,$l,"b"));
   }
 }
 return $res;
}
//Example:
//$a1=array("hello","world");
//$a2=array("good","bye","world");
//=> arr_diff($a1,$a2) = array(array("hello","good","bye","world"), array("1","2","2","b"));

function arr_diff(&$a1,&$a2)
{
	$max=1700;
	$c1=count($a1);	// taille de a1
	$c2=count($a2);	// taille de a2
	
	$v[1]=0;
	for ($D=0; $D<=$max; $D++) {
		for ($k=-$D; $k<=$D; $k=$k+2) {
			if (($k==-$D) || ($k!=$D && $v[$k-1]<$v[$k+1]))
				$x=$v[$k+1];
			else
				$x=$v[$k-1]+1;
			$y=$x-$k;
			while (($x<$c1)&&($y<$c2)&&($a1[$x]==$a2[$y])){
				$x++;
				$y++;
			}
			$v[$k]=$x;
			if (($x>=$c1)&&($y>=$c2)) {
				$vbck[$D]=$v;
				return diff_rek($a1,$a2,$D,$c1-$c2,$vbck);
			}
		}
		$vbck[$D]=$v;
	}
	return -1;
}
function diff_to_xml($oldString, $newString) {
  $a1 = explode(" ", $oldString);
  $a2 = explode(" ", $newString);
  $result = arr_diff($a1, $a2);
  $return = "" ;

  foreach ($result[0] as $num => $foo)
  {
   $source = $result[1][$num];
   $element = $result[0][$num];
   switch ($source)
   {
     case "1":
       $pre = "<old_string>";
       $post = "</old_string>";
       break;
     case "2":
	$pre = "<new_string>";
       $post = "</new_string>";
       break;
     case "b":
       $pre = "";
       $post = "";
       break;
   }
   $return .= $pre . $element . $post . " ";
  }
  return $return;
}

function log_admin($id,$log) {
	global $DB_admin ;
	$log2 = str_replace(array('&','<','>','\'','\"','\\'),array('&amp;','&lt;','&gt;','&apos;','&quot;','&#92;'),$log);
	$DB_admin->query("INSERT INTO log_admin SET log='$log2', id_admin='$id'") ;
}


// pour gérer les échappements de chaines de caractères comme il faut en fonction de magic_quotes_gpc et magic_quotes_runtime
function gpc_stripslashes($var) {
	if (is_array($var)) {
		reset($var);
		while (list($key,$value) = each($var)) {
			$var[$key] = gpc_stripslashes($value);
		}
	} elseif (ini_get('magic_quotes_sybase')) $var = str_replace('\'\'','\'',$var);
	elseif (get_magic_quotes_gpc()) $var = stripslashes($var);
	return $var;
}

function mysql_addslashes($var,$gpc=true) {
	if (is_array($var)) {
		reset($var);
		while (list($key,$value) = each($var)) {
			$var[$key] = mysql_addslashes($value,$gpc);
		}
	} elseif ($gpc) $var = mysql_real_escape_string(gpc_stripslashes($var));
	else $var = mysql_real_escape_string($var);
	return $var;
}

function extdata_stripslashes($var) {
	if (is_array($var)) {
		reset($var);
		while (list($key,$value) = each($var)) {
			$var[$key] = extdata_stripslashes($value);
		}
	} elseif (get_magic_quotes_runtime()) $var = stripslashes($var);
	return $var;
}

// une fonction pour transformer les &amp; &apos; &quot; &lt; &gt; en &#38; &#39; &#34; &#139; &#155;
function vrais_caract_spec($string) {
	$fauxCaractSpec = array('&amp;','&apos;','&quot;','&lt;','&gt;');
	$vraisCaractSpec = array('&#38;','&#39;','&#34;','&#139;','&#155;');
	return str_replace($fauxCaractSpec,$vraisCaractSpec,$string);
}

/**
 * Déterminer l'adresse ip réelle du client, s'il est derrière le portail web de
 * l'école en particulier
 * 
 * @return string l'ip
 * @author alakazam
 **/
function ip_get() {
	if (isset($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	} else {
		// CLI
		$ip = '127.0.0.1';
	}

	if ($ip === '129.104.30.4') {
		// C'est l'adresse du portail w3x
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$listeIPs = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			
			// Le dernier de cette liste est celui ajoute par w3x, qui est un
			// proxy fiable. Toute cette verification a pour objectif de ne pas
			// permettre l'ip spoofing
			// (trim : le séparateur entre les ips dans $headers['X-Forwarded-For'] est ', ')
			$ipForwardee = trim(end($listeIPs));
			
			if (preg_match("/([0-9]{1,3}\.){3}[0-9]{1,3}/", $ipForwardee)) {
				$ip = $ipForwardee;
			}
		}
	}

	return $ip;
}

// fonction qui recupere l'etat du bob, verifie sa "coherence" et modifie eventuellement en fonction
function getEtatBob() {
	global $DB_web;
	$DB_web->query("SELECT valeur FROM parametres WHERE nom='bob'");
	list($val) = extdata_stripslashes($DB_web->next_row());
	$valeur  = intval($val);
	$month	 = date("n");
	$day     = date("j");
	$year    = date("Y");
	$huitH   = mktime(8,0,0,$month,$day,$year);
	$quinzeH = mktime(15,0,0,$month,$day,$year);
	$now     = time();
	if ($valeur && ( ($now > $huitH && $valeur < $huitH) || ($now > $quinzeH && $valeur < $quinzeH) )) {
		fermerBob();
		$valeur = 0;
	}
	return ($valeur)? 1 : 0;
}

function ouvrirBob() {
	global $DB_web;
	$DB_web->query("UPDATE parametres SET valeur='".time()."' WHERE nom='bob';");
	return true;
}

function fermerBob() {
	global $DB_web;
	$DB_web->query("UPDATE parametres SET valeur='0' WHERE nom='bob';");
	return true;
}

?>
