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
	Page d'accueil de frankiz pour les personnes non loguées.
	
	$Log$
	Revision 1.39  2005/02/10 22:25:42  pico
	On revient à une version précédente

	Revision 1.31  2005/02/10 21:37:53  pico
	- Pour les ids de news, fait en fonction de la date de péremption, c'est mieux que seulement par id, mais y'a tjs un pb avec les nouvelles fraiches
	- Correction pour éviter que des gens postent des annonces qui sont déjà périmées
	
	Revision 1.30  2005/02/08 21:57:56  pico
	Correction bug #62
	
	Revision 1.29  2005/02/02 12:40:38  kikx
	Resolution bug 57
	Pb restant, je ne suis jamais sur que l'annonce d'en dessous c'est id-1 car c'est la skin qui reordonne
	
	Revision 1.28  2005/01/24 17:27:53  kikx
	Permet de gerer l'auth Xorg
	NE PAS COMMITER EN PROD ... car pas encore terminer
	il faut maintenant reflechir a comment on gere les compte xorg ...
	
	J'attend vos avis eclairés :)
	
	Revision 1.27  2005/01/18 19:30:34  pico
	Place la boite du sudo dans la boite avec les infos de connexion.
	Pbs d'encodage des variables passées à sablotron réglés
	Pb du su quand on est pas loggué par mot de passe réglé
	
	Revision 1.26  2005/01/12 17:17:19  pico
	hum
	
	Revision 1.25  2005/01/12 17:15:58  pico
	Correction bug #30
	
	Revision 1.24  2005/01/02 10:50:25  pico
	Passage de certaines pages en visibles de l'intérieur (non loggué)
	
	Revision 1.23  2004/12/17 10:55:44  kikx
	Ca avait déja ete fait ?
	pige pas ...
	
	Revision 1.22  2004/12/17 09:41:53  pico
	Solution simple pour éviter de mettre 2 fois la même entrée dans la base des annonces lues
	
	Revision 1.21  2004/12/14 18:42:28  schmurtz
	Bugs sur les annonces
	
	Revision 1.20  2004/12/14 17:14:52  schmurtz
	modification de la gestion des annonces lues :
	- toutes les annonces sont envoyees dans le XML
	- annonces lues avec l'attribut visible="non"
	- suppression de la page affichant toutes les annonces
	
	Revision 1.19  2004/12/13 21:28:25  pico
	Le lien est mieux à la fin de l'annonce
	
	Revision 1.18  2004/12/13 20:03:25  pico
	Les liens ne forment pas de blocs, il faut donc le spécifier
	
	Revision 1.17  2004/12/12 09:29:26  kikx
	Disparition du lien faire disparaitrre si l'utilisateur n'est pas logué ou et si l'utilisateur a deja lu cette annonce et qu'il se situe sur la page pour toute les voir
	
	Desolé Psycow ... j'ai pourri ta skin :(
	
	Revision 1.16  2004/12/10 20:06:18  kikx
	Pour faire plaisir a fruneau
	
	Revision 1.15  2004/12/10 19:49:28  kikx
	YESSSSSS
	Pour permettre au utilisateurs de supprimer les annonces qu'ils ont déjà lues
	
	Revision 1.14  2004/12/09 19:29:13  pico
	Rajoute le tel dans le trombino, ça pourrait être utile...
	
	Revision 1.13  2004/11/24 22:56:18  schmurtz
	Inclusion de wiki.inc.php par les fichiers concerne uniquement et non de facon
	globale pour tous les fichiers.
	
	Revision 1.12  2004/11/24 13:38:34  kikx
	Changment de l'id de la page d'annonce en annonces pour les skinneurs
	
	Revision 1.11  2004/11/24 13:32:23  kikx
	Passage des annonces en wiki !
	
	Revision 1.10  2004/10/25 19:41:58  kikx
	Rend clair la page d'accueil et les annonces
	
	Revision 1.9  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.8  2004/09/15 23:19:45  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.7  2004/09/15 21:42:15  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
require_once "include/global.inc.php";
require_once "include/wiki.inc.php";

function get_categorie($en_haut,$stamp,$perime) {
	if($en_haut==1) return "important";
	elseif($stamp > date("Y-m-d H:i:s",time()-12*3600)) return "nouveau";
	elseif($perime < date("Y-m-d H:i:s",time()+24*3600)) return "vieux";
	else return "reste";
}

// génération de la page
require "include/page_header.inc.php";
echo "<page id='annonces' titre='Frankiz : annonces'>\n";
?>

<h2>Bienvenue sur Frankiz</h2>
<?
$annonces_lues1="";
$annonces_lues2=" 1 ";
if (!est_authentifie(AUTH_MINIMUM))  {
?>
	<p>Bienvenu sur le site web des élèves de l'École Polytechnique.</p>
	<p>Si tu souhaites te connecter et accéder à la partie réservée aux élèves, clique
		<a href="login.php">ici</a> pour te loguer.</p>
<?
} else {
// Pour marquer les annonces comme lues ou non

	if (isset($_REQUEST['lu'])) {
		$DB_web->query("SELECT 0 FROM annonces_lues WHERE annonce_id='{$_REQUEST['lu']}' AND eleve_id='{$_SESSION['user']->uid}'");
		if ($DB_web->num_rows()==0) {
			$DB_web->query("INSERT INTO annonces_lues SET annonce_id='{$_REQUEST['lu']}',eleve_id='{$_SESSION['user']->uid}'");
		}
	}

	
	if (isset($_REQUEST['nonlu']))
		$DB_web->query("DELETE FROM annonces_lues WHERE annonce_id='{$_REQUEST['nonlu']}' AND eleve_id='{$_SESSION['user']->uid}'");
	
	$annonces_lues1=" LEFT JOIN annonces_lues ON annonces_lues.annonce_id=annonces.annonce_id AND annonces_lues.eleve_id='{$_SESSION['user']->uid}'" ;
	$annonces_lues2=" ISNULL(annonces_lues.annonce_id) ";
}
		
		
// Affichage des annonces
$DB_web->query("SELECT annonces.annonce_id,stamp,perime,titre,contenu,en_haut,exterieur,nom,prenom,surnom,promo,"
					 ."IFNULL(mail,CONCAT(login,'@poly.polytechnique.fr')) as mail, $annonces_lues2 "
					 ."FROM annonces LEFT JOIN trombino.eleves USING(eleve_id) $annonces_lues1"
					 ."WHERE (perime>'".date("Y-m-d H:i:s",time())."') ORDER BY perime DESC");
$nb =$DB_web->num_rows();
$cpt =0;
while(list($id,$stamp,$perime,$titre,$contenu,$en_haut,$exterieur,$nom,$prenom,$surnom,$promo,$mail,$visible)=$DB_web->next_row()) {
	if(!$exterieur && !est_authentifie(AUTH_INTERNE)) continue;
	if($cpt > 0){
			if(est_authentifie(AUTH_MINIMUM))
			echo "<lien url=\"?lu=$idprec#annonce_$id\" titre=\"Faire disparaître\" id=\"annonces_lues\"/><br/>\n";
			echo "</annonce>";
	}
	$idprec=$id;
	$cpt++;
?>
	<annonce id="<?php echo $id ?>" 
		titre="<?php echo $titre ?>" visible="<?=$visible?"oui":"non" ?>"
		categorie="<?php echo get_categorie($en_haut, $stamp, $perime) ?>"
		date="<?php echo substr($stamp,8,2)."/".substr($stamp,5,2)."/".substr($stamp,0,4) ?>">
<?php
		if (file_exists(DATA_DIR_LOCAL."annonces/$id"))
			echo "<image source=\"".DATA_DIR_URL."annonces/$id\" texte=\"logo\"/>\n";
		echo wikiVersXML($contenu);
		echo "<eleve nom=\"$nom\" prenom=\"$prenom\" promo=\"$promo\" surnom=\"$surnom\" mail=\"$mail\"/>\n";
	if($nb==$cpt){
		if(est_authentifie(AUTH_MINIMUM))
		echo "<lien url=\"?lu=$idprec#annonce_$id\" titre=\"Faire disparaître\" id=\"annonces_lues\"/><br/>\n";
		echo "</annonce>";
	}
}
echo "</page>\n";
require_once "include/page_footer.inc.php";
?>
