<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Page d'accueil de frankiz pour les personnes non logu�es.
	
	$Log$
	Revision 1.14  2004/12/09 19:29:13  pico
	Rajoute le tel dans le trombino, �a pourrait �tre utile...

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

require_once "include/wiki.inc.php";

function get_categorie($en_haut,$stamp,$perime) {
	if($en_haut==1) return "important";
	elseif($stamp > date("Y-m-d H:i:s",time()-12*3600)) return "nouveau";
	elseif($perime < date("Y-m-d H:i:s",time()+24*3600)) return "vieux";
	else return "reste";
}

// g�n�ration de la page
require "include/page_header.inc.php";
echo "<page id='annonces' titre='Frankiz : annonces'>\n";
?>

<h2>Bienvenue sur Frankiz</h2>
<?
if (!est_authentifie(AUTH_COOKIE))  {
?>
	<p>&nbsp;</p>
	<p>Voici la nouvelle page �l�ve qui est en construction...</p>
	<p>Si tu veux te connecter et acc�der � la partie r�serv�e aux �l�ves alors clique sur ce <a href="login.php">lien</a></p>
	<p>Sinon navigue sur cette page en utilisant les liens qui se situe un peu partout sur cette page :)</p>
<?
}
$DB_web->query("SELECT annonce_id,stamp,perime,titre,contenu,en_haut,exterieur,nom,prenom,surnom,promo,"
					 ."IFNULL(mail,CONCAT(login,'@poly.polytechnique.fr')) as mail "
					 ."FROM annonces LEFT JOIN trombino.eleves USING(eleve_id) "
					 ."WHERE (perime>=".date("Ymd000000",time()).") ORDER BY perime DESC");
while(list($id,$stamp,$perime,$titre,$contenu,$en_haut,$exterieur,$nom,$prenom,$surnom,$promo,$mail)=$DB_web->next_row()) {
	if(!$exterieur && !est_authentifie(AUTH_MINIMUM)) continue;
?>
	<annonce id="<?php echo $id ?>" 
			titre="<?php echo $titre ?>"
			categorie="<?php echo get_categorie($en_haut, $stamp, $perime) ?>"
			date="<?php echo substr($stamp,8,2)."/".substr($stamp,5,2)."/".substr($stamp,0,4) ?>">
<?php
		echo wikiVersXML($contenu);

			if (file_exists(DATA_DIR_LOCAL."annonces/$id")) {
			?>
				<image source="<?echo DATA_DIR_URL."annonces/$id" ; ?>" texte="logo"/>
			<? 
			}
?>
		<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>" mail="<?=$mail?>"/>
	</annonce>
<?php }

echo "</page>\n";
require_once "include/page_footer.inc.php";
?>