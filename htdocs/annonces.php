<?php
/*
	Annonces de frankiz. Page d'acceuil pour les personnes déjà loguées.
	
	$Log$
	Revision 1.12  2004/10/20 19:59:42  pico
	Ajout champ id

	Revision 1.11  2004/10/16 01:50:22  schmurtz
	Affichage des annonces publiques (exterieure) pour les personnes non authentifiees
	
	Revision 1.10  2004/10/11 11:08:38  kikx
	L'affichage des annonces provoquait des erreurs ...
	
	Revision 1.9  2004/09/18 16:04:52  kikx
	Beaucoup de modifications ...
	Amélioration des pages qui gèrent les annonces pour les rendre compatible avec la nouvelle norme de formatage xml -> balise web et balise image qui permette d'afficher une image et la signature d'une personne
	
	Revision 1.8  2004/09/17 15:28:27  schmurtz
	Utilisation de la balise <eleve> pour les derniers votants aÌ€ la qdj, les anniversaires, la signature des annoncesâ€¦
	
	Revision 1.7  2004/09/17 13:12:24  schmurtz
	Suppression des <![CDATA[...]>> car les donneÌes des GET et POST (et donc de la base de donneÌes) sont maintenant eÌchappeÌes avec des &amp; &lt; &apos;...
	
	Revision 1.6  2004/09/17 10:49:40  kikx
	Petite erreur ou plutot oubli suite a la suppression du champ valid dans les annonces
	
	Revision 1.5  2004/09/16 15:33:50  schmurtz
	Orthographe
	
	Revision 1.4  2004/09/16 15:33:03  schmurtz
	Suppression de la fonction afficher_identifiant(), utilisation de <![CDATA[......]]> aÌ€ la place.
	
	Revision 1.3  2004/09/15 23:19:45  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.2  2004/09/15 21:42:15  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "include/global.inc.php";

function get_categorie($en_haut,$stamp,$perime) {
	if($en_haut==1) return "important";
	elseif($stamp > date("YmdHis",time()-12*3600)) return "nouveau";
	elseif($perime < date("YmdHis",time()+24*3600)) return "vieux";
	else return "reste";
}

// génération de la page
require "include/page_header.inc.php";
echo "<page id='annonces' titre='Frankiz : annonces'>\n";


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
		echo $contenu;

			if (file_exists(DATA_DIR_LOCAL."annonces/$id")) {
			?>
				<image source="<?echo DATA_DIR_URL."annonces/$id" ; ?>" texte=""/>
			<? 
			}
?>
		<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>" mail="<?=$mail?>"/>
	</annonce>
<?php }


echo "</page>\n";
require_once "include/page_footer.inc.php";
?>