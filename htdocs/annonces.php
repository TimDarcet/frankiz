<?php
/*
	$Id$
	
	Annonces de frankiz. Page d'acceuil pour les personnes déjà loguées.
	
	$Log$
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
echo "<page id='annoncesl' titre='Frankiz : annonces'>\n";


$DB_web->query("SELECT annonce_id,stamp,perime,titre,contenu,en_haut,nom,prenom "
					 ."FROM annonces LEFT JOIN trombino.eleves USING(eleve_id) "
					 ."WHERE (perime>=".date("Ymd000000",time())." AND valide=1) ORDER BY perime DESC");
while(list($id,$stamp,$perime,$titre,$contenu,$en_haut,$nom,$prenom)=$DB_web->next_row()) { ?>
	<annonce titre="<?php echo $titre ?>" 
			categorie="<?php echo get_categorie($en_haut, $stamp, $perime) ?>"
			auteur="<?php echo "$prenom $nom" ?>"
			date="<?php echo substr($stamp,8,2)."/".substr($stamp,5,2)."/".substr($stamp,0,4) ?>">
		<?php echo afficher_identifiant($contenu) ?>
	</annonce>
<?php }


echo "</page>\n";
require_once "include/page_footer.inc.php";
?>