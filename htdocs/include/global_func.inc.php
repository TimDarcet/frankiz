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

	$Log$
	Revision 1.45  2005/01/25 14:50:56  kikx
	Suppression fonction xorg

	Revision 1.44  2005/01/24 17:27:53  kikx
	Permet de gerer l'auth Xorg
	NE PAS COMMITER EN PROD ... car pas encore terminer
	il faut maintenant reflechir a comment on gere les compte xorg ...
	
	J'attend vos avis eclairés :)
	
	Revision 1.43  2005/01/21 17:01:31  pico
	Fonction pour savoir si interne
	
	Revision 1.42  2005/01/17 20:15:39  pico
	Mail promo pour les kessiers
	
	Revision 1.41  2005/01/12 22:40:39  pico
	Ajout des fêtes à souhaiter
	
	Revision 1.40  2004/12/17 17:25:08  schmurtz
	Ajout d'une belle page d'erreur.
	
	Revision 1.39  2004/12/14 01:02:43  paxal
	Ajout d'un random pour portage de la skin aubade et celio
	
	Si je me suis planté n'hésitez pas à me blatter
	
	Revision 1.38  2004/12/14 00:30:22  kikx
	Pour preparer le terrain a la modification de la FAQ
	
	Revision 1.37  2004/12/07 12:13:47  kikx
	Une fonction de diff pour autoriser au gens de modifier les faqs en live ... ca demande la validation mais le webmestre voit de suite ce qui a été modifié
	
	Revision 1.36  2004/11/29 20:48:45  kikx
	Simplification des rajouts des droits des personnes ... ce fait grace a des cases a cocher ... (pour les autistes ca devrait etre bon ...) Comme ca pas d'erreur de syntaxe possibles...
	
	La liste des droits possibles est dans global_func.inc.php
	
	Revision 1.35  2004/11/24 22:12:57  schmurtz
	Regroupement des fonctions zip unzip deldir et download dans le meme fichier
	
	Revision 1.34  2004/11/24 21:09:04  pico
	Sauvegarde avant mise à jour skins
	
	Revision 1.33  2004/11/24 20:07:12  pico
	Ajout des liens persos
	
	Revision 1.32  2004/11/24 16:37:09  pico
	Ajout des news externes en tant que module
	
	Revision 1.31  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.30  2004/11/19 23:04:27  alban
	
	Rajout du module lien_tol
	
	Revision 1.29  2004/11/19 22:28:36  schmurtz
	C'est bien de conserver une certaine unite dans la maniere de mettre les
	commentaires
	Et dans le meme genre, si tout le monde utilisait des tabulations de la taille
	de 4 espaces (ou autre, mais il faut se mettre d'accord) ca serait mieux.
	
	Revision 1.28  2004/11/19 17:14:31  kikx
	Gestion complete et enfin FINIIIIIIIIIIIIIIII des sondages !!! bon ok c'est assez moche l'affichage des resultats mais .... j'en ai marrrrrrrrrrre
	
	Revision 1.27  2004/11/17 22:19:15  kikx
	Pour avoir un module sondage
	
	Revision 1.26  2004/11/17 13:27:06  kikx
	Mise ne place d'un titre dan sles sondages
	
	Revision 1.25  2004/11/16 14:02:37  pico
	- Nouvelle fonction qui permet de dl le contenu d'un répertoire
	- Mise en place sur la page de la FAQ
	
	Revision 1.24  2004/11/10 21:39:44  pico
	Corrections skin + fonction deldir + faq
	
	Revision 1.23  2004/11/08 11:57:58  pico
	Déplacement de la fonction deldir (pas une fonction de compression)
	
	Revision 1.22  2004/11/06 20:52:08  kikx
	Reordonnancement des liens
	
	Revision 1.21  2004/10/28 14:49:47  kikx
	Mise en place de la météo en module : TODO eviter de repliquer 2 fois le code de la météo
	
	Revision 1.20  2004/10/28 11:29:07  kikx
	Mise en place d'un cache pour 30 min pour la météo
	
	Revision 1.19  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.18  2004/10/14 19:04:20  schmurtz
	Bug dans la gestion du cache
	
	Revision 1.17  2004/09/20 20:33:47  schmurtz
	Mise en place d'un systeme de cache propre
	
	Revision 1.16  2004/09/18 16:04:52  kikx
	Beaucoup de modifications ...
	Amélioration des pages qui gèrent les annonces pour les rendre compatible avec la nouvelle norme de formatage xml -> balise web et balise image qui permette d'afficher une image et la signature d'une personne
	
	Revision 1.15  2004/09/17 17:41:23  kikx
	Bon ct plein de bugs partout et ca ressemblait  a rien mais bon c'est certainement la faute de Schmurtz :))))))
	
	Revision 1.12  2004/09/17 15:27:08  schmurtz
	Suppression de la fonction suppression qui ne sert pas.
	
	Revision 1.11  2004/09/16 15:32:56  schmurtz
	Suppression de la fonction afficher_identifiant(), utilisation de <![CDATA[......]]> aÌ€ la place.
	
	Revision 1.10  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.9  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
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

function est_interne() {
	return (substr($_SERVER['REMOTE_ADDR'],0,8) == "129.104." &&  $_SERVER['REMOTE_ADDR'] != "129.104.30.4" );
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
        "random"            => "",
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
		"liens_perso"		=> "Liens Perso"
		);
}/*
	Renvoi la liste des droits disponibles sous la forme d'une liste :
		"nom" => "droits"
	
	Si le nom affichage est vide, cela signifie que le module est toujours visible.
*/
function liste_droits() {
	return array(
		"admin"            =>"Administrateur Total",
		"web"              =>"Webmestre de Frankiz",
		"qdjmaster"        =>"QdjMaster",
		"xshare"           =>"Xshare",
		"faq"              =>"Faqmestre",
		"trombino"         =>"TrombinoMen",
		"kes"         =>"(EXT) Kessiers (mail promo)",
		"bob"              =>"(EXT) BobarMen",
		"affiches"         =>"(EXT) Affiches (ex BRC...)"
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
			echo "<champ id=\"$i\" titre=\"$temp[1]\" valeur=\"\"/>\n" ;
		}
		if ($temp[0]=="text") {
			echo "<zonetext id=\"$i\" titre=\"$temp[1]\"></zonetext>\n" ;
		}
		if ($temp[0]=="radio") {
			echo "<choix titre=\"$temp[1]\" id=\"$i\" type=\"radio\" valeur=\"\">\n" ;
			for ($j=2 ; $j<count($temp) ; $j++) {
				echo "\t<option titre=\"".$temp[$j]."\" id=\"".($j-1)."\"/>\n";
			}	
			echo "</choix>\n" ;
		}
		if ($temp[0]=="combo") {
			echo "<choix titre=\"$temp[1]\" id=\"$i\" type=\"combo\" valeur=\"\">\n" ;
			for ($j=2 ; $j<count($temp) ; $j++) {
				echo "\t<option titre=\"".$temp[$j]."\" id=\"".($j-1)."\"/>\n";
			}	
			echo "</choix>\n" ;
		}
		if ($temp[0]=="check") {
			echo "<choix titre=\"$temp[1]\" id=\"$i\" type=\"checkbox\" valeur=\"\">\n" ;
			for ($j=2 ; $j<count($temp) ; $j++) {
				echo "\t<option titre=\"".$temp[$j]."\" id=\"{$i}_".($j-1)."\"/>\n";
			}	
			echo "</choix>\n" ;
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

?>
