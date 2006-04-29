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
	
	$Id$

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
if (!est_interne() && !est_authentifie(AUTH_MINIMUM))  {
?>
<annonce id="0"  titre="Bienvenue sur le site web des élèves de l'École Polytechnique." visible="oui" categorie="important" date="<?php echo date("d/m/Y") ?>">
	Pour un élève de l'École des promos 1998 à 2004, il est possible de <a href="login.php">se connecter</a> pour accéder à l'ensemble des services proposés.
	<br/>
	Sinon, une partie du site reste accesible. Il est en effet possible de consulter :
	<ul>
		<li> Certaines <a href="activites.php">activités</a> de la semaine qui se dérouleront sur le campus.</li>
		<li> Une base de <a href="xshare.php">téléchargement</a> de logiciels libres.</li>
		<li> Une <a href="faq.php">Foire Aux Questions</a> sur des problèmes informatiques ou sur le campus</li>
		<li> Des descriptions et des sites de <a href="binets.php">clubs de l'école</a> (les binets).</li>
		<li> Des <a href="siteseleves.php">sites d'élèves</a> de l'école.</li>
		<li> Une liste du <a href="vocabulaire.php">vocabulaire</a> propre aux X.</li>
	</ul>
	<br/>
	Bonne navigation,<br/>
	Les webmestres de Frankiz
</annonce>

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
$DB_web->query("SELECT annonces.annonce_id,DATE_FORMAT(stamp,'%d/%m/%Y'),stamp,perime,titre,contenu,en_haut,exterieur,nom,prenom,surnom,promo,"
					 ."IFNULL(mail,CONCAT(login,'@poly.polytechnique.fr')) as mail, $annonces_lues2 "
					 ."FROM annonces LEFT JOIN trombino.eleves USING(eleve_id) $annonces_lues1"
					 ."WHERE (perime>NOW()) ORDER BY perime DESC");
/*if (est_authentifie(AUTH_MINIMUM))  {
	$idprec=0;
	while(list($id,$stamp,$perime,$titre,$contenu,$en_haut,$exterieur,$nom,$prenom,$surnom,$promo,$mail,$visible)=$DB_web->next_row()) {
		if($visible){
			if($idprec!=0)
				$array_id[$idprec]=$id;
			$idprec=$id;
		}
	}
	mysql_data_seek($DB_web->result, 0);
}*/
while(list($id,$date,$stamp,$perime,$titre,$contenu,$en_haut,$exterieur,$nom,$prenom,$surnom,$promo,$mail,$visible)=$DB_web->next_row()) {
	if(!$exterieur && !est_authentifie(AUTH_INTERNE)) continue;
?>
	<annonce id="<?php echo $id ?>" 
		titre="<?php echo $titre ?>" visible="<?=$visible?"oui":"non" ?>"
		categorie="<?php echo get_categorie($en_haut, $stamp, $perime) ?>"
		date="<?php echo $date ?>">
<?php
		if (file_exists(DATA_DIR_LOCAL."annonces/$id"))
			echo "<image source=\"".DATA_DIR_URL."annonces/$id\" texte=\"logo\"/>\n";
		echo wikiVersXML($contenu);
		if($nom=="Lemarchand") echo "<eleve nom=\"\" prenom=\"\" promo=\"\" surnom=\"GeneK\" mail=\"\"/>\n";
		else echo "<eleve nom=\"$nom\" prenom=\"$prenom\" promo=\"$promo\" surnom=\"$surnom\" mail=\"$mail\"/>\n";
		if(est_authentifie(AUTH_MINIMUM))
			echo "<lien url=\"?lu=$id#annonce_$id\" titre=\"Faire disparaître\" id=\"annonces_lues\"/><br/>\n";
		echo "</annonce>";
}
echo "</page>\n";
require_once "include/page_footer.inc.php";
?>
