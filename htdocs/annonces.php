<?php
/*
	Annonces de frankiz. Page d'acceuil pour les personnes déjà loguées.
	
	$Log$
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
demande_authentification(AUTH_MINIMUM);

function get_categorie($en_haut,$stamp,$perime) {
	if($en_haut==1) return "important";
	elseif($stamp > date("YmdHis",time()-12*3600)) return "nouveau";
	elseif($perime < date("YmdHis",time()+24*3600)) return "vieux";
	else return "reste";
}

// génération de la page
require "include/page_header.inc.php";
echo "<page id='annonces' titre='Frankiz : annonces'>\n";


$DB_web->query("SELECT annonce_id,stamp,perime,titre,contenu,en_haut,nom,prenom "
					 ."FROM annonces LEFT JOIN trombino.eleves USING(eleve_id) "
					 ."WHERE (perime>=".date("Ymd000000",time()).") ORDER BY perime DESC");
while(list($id,$stamp,$perime,$titre,$contenu,$en_haut,$nom,$prenom)=$DB_web->next_row()) { ?>
	<annonce titre="<?php echo $titre ?>" 
			categorie="<?php echo get_categorie($en_haut, $stamp, $perime) ?>"
			auteur="<?php echo "$prenom $nom" ?>"
			date="<?php echo substr($stamp,8,2)."/".substr($stamp,5,2)."/".substr($stamp,0,4) ?>">
		<?php echo $contenu ?>
	</annonce>
<?php }


echo "</page>\n";
require_once "include/page_footer.inc.php";
?>