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
	echo "<p>Si ton navigateur n'est pas automatiquement redirigé, <a href=\"".BASE_URL.$page."\">cliques ici</a>.</p>";
	exit;
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
		"liens_utiles"		=> "Liens école",
		"qdj"				=> "Question du jour",
		"qdj_hier"			=> "Question de la veille",
		"meteo"			=> "Météo",
		"activites"			=> "Activités",
		"tour_kawa"		=> "Tours kawa",
		"anniversaires"		=> "Anniversaires",
		"stats"			=> "Statistiques",
		"sondages"		=> "Sondages",
		"lien_tol"			=> "Lien rapide vers le tol",
		"rss"				=> "News Extérieures",
		"liens_perso"		=> "Liens Perso"
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

?>
