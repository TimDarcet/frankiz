<?php
/*
	Copyright (C) 2004 Binet RÈseau
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
	Recherche dans le trombino.

	$Log$
	Revision 1.34  2004/12/15 04:32:44  falco
	Bugfix TOL #2 : " " converts into "-"

	Revision 1.33  2004/12/15 04:27:46  falco
	c du html
	
	Revision 1.32  2004/12/15 04:25:22  falco
	mieux ainsi
	
	Revision 1.31  2004/12/15 04:24:41  falco
	Bugfix lien xorg TOL apostrophes
	
	Revision 1.30  2004/12/15 03:48:23  kikx
	Sinon ca merde
	
	Revision 1.29  2004/12/12 15:18:59  psycow
	Rechangement blah
	
	Revision 1.28  2004/12/12 13:17:01  psycow
	Modification du Trombino
	
	Revision 1.27  2004/12/09 19:58:59  pico
	Suppression du code en double
	
	Revision 1.26  2004/12/09 19:56:48  pico
	La boite de recherche tol fait aussi les requetes de tel.
	
	Pourquoi ce bout de code est en double ?
	
	Sinon, j'ai importÈ la base des numÈros de tel dans frankiz2.
	
	Revision 1.25  2004/12/09 19:29:13  pico
	Rajoute le tel dans le trombino, Áa pourrait Ítre utile...
	
	Revision 1.24  2004/11/27 20:49:10  pico
	Affichage des liens du trombino
	
	Revision 1.23  2004/11/27 15:39:54  pico
	Ajout des droits trombino
	
	Revision 1.22  2004/11/25 02:22:02  schmurtz
	esthetisme (trop long)
	
	Revision 1.21  2004/11/25 01:46:10  schmurtz
	Bug sur l'affichage des liens admin dans le trombino. C'est bien de tester quand
	on fait des modifs.
	
	Revision 1.20  2004/11/22 18:44:08  pico
	Recherche par promo promo prÈsente
	
	Revision 1.19  2004/11/22 10:15:03  pico
	Ajout d'un lien vers le trombi d'X.org
	
	Revision 1.18  2004/11/19 23:04:27  alban
	
	Rajout du module lien_tol
	
	Revision 1.17  2004/11/13 00:25:26  schmurtz
	Rajout du lien d'ace au su
	
	Revision 1.16  2004/11/12 23:32:14  schmurtz
	oublie dans le deplacement du trombino
	
	Revision 1.15  2004/10/24 20:13:22  kikx
	Pour afficher la photo original ...
	
	Revision 1.14  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.13  2004/09/17 13:12:07  schmurtz
	Suppression des <![CDATA[...]>> car les donneÃÅes des GET et POST (et donc de la base de donneÃÅes) sont maintenant eÃÅchappeÃÅes avec des &amp; &lt; &apos;...
	
	Revision 1.12  2004/09/16 15:32:49  schmurtz
	Suppression de la fonction afficher_identifiant(), utilisation de <![CDATA[......]]> aÃÄ la place.
	
	Revision 1.11  2004/09/15 23:19:17  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.10  2004/09/15 21:42:01  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once "include/global.inc.php";
demande_authentification(AUTH_MINIMUM);

// RÈcupÈration d'une image
if((isset($_REQUEST['image']))&&($_REQUEST['image'] == "true") && ($_REQUEST['image'] != "")){
	require_once "include/global.inc.php";
	header('content-type: image/jpeg');
	if (!isset($_REQUEST['original'])) {
		readfile(BASE_PHOTOS.$_REQUEST['promo']."/".$_REQUEST['login'].".jpg");	
	} else {
		readfile(BASE_PHOTOS.$_REQUEST['promo']."/".$_REQUEST['login']."_original.jpg");		
	}
	exit;
}

require "include/page_header.inc.php";
echo "<page id='trombino' titre='Frankiz : Trombino'>\n";

// Affichage des rÈponses
if(isset($_REQUEST['chercher'])||(isset($_REQUEST['cherchertol'])&&(!(empty($_REQUEST['q_search']))))) {
		
	$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
	list($promo_temp) = $DB_web->next_row() ;

	$where = "";
		$join = "LEFT JOIN sections ON eleves.section_id=sections.section_id LEFT JOIN pieces ON eleves.piece_id = pieces.piece_id ";
		$champs = "eleves.eleve_id,eleves.nom,prenom,surnom,eleves.piece_id,sections.nom,eleves.section_id,cie,promo,login,mail,pieces.tel";
	
	// CrÈation de la requÍte si lien_tol appelle
	if(isset($_REQUEST['cherchertol'])) {
		$where_like = array(
			'nom' => 'eleves.nom',	'prenom' => 'prenom',	'surnom' => 'surnom');
		foreach($where_like as $post_arg => $db_field)
			$where .= (empty($where) ? "(" : " OR") . " $db_field LIKE '%".$_REQUEST['q_search']."%'";
		$where .= ") AND (promo=$promo_temp OR promo=".($promo_temp -1).")";
	}
	
	// CrÈation de la requËte si tol s'appelle
	if(isset($_REQUEST['chercher'])) {
		$where_exact = array(
				'section' => 'eleves.section_id',	'cie' => 'cie');
		foreach($where_exact as $post_arg => $db_field)
			if(!empty($_REQUEST[$post_arg]))
				$where .= (empty($where) ? "" : " AND") . " $db_field='".$_REQUEST[$post_arg]."'";
			if($_REQUEST['promo'] == "")
				$where .=  (empty($where) ? "" : " AND") ." (promo=$promo_temp OR promo=".($promo_temp -1).")";
			else if($_REQUEST['promo'] != "toutes")
				$where .= (empty($where) ? "" : " AND") ." promo='".$_REQUEST['promo']."'";

		$where_like = array(
				'nom' => 'eleves.nom',	'prenom' => 'prenom',   'casert' => 'piece_id',
				/*'phone' => '',*/		'surnom' => 'surnom',   'mail' => 'mail',
				'loginpoly' => 'login');
		foreach($where_like as $post_arg => $db_field)
			if(!empty($_REQUEST[$post_arg]))
				$where .= (empty($where) ? "" : " AND") . " $db_field LIKE '%".$_REQUEST[$post_arg]."%'";
			
		if(!empty($_REQUEST['binet'])) {
			$join = "INNER JOIN membres USING(eleve_id) " . $join;
			$where .= (empty($where) ? "" : " AND") . " binet_id='".$_REQUEST['binet']."'";
		}
	}
	
	// GÈnÈration de la page si il y a au moins un critËre.
	if(!empty($where)) {	
		
		$DB_trombino->query("SELECT $champs FROM eleves $join WHERE $where");
		
		// GÈnÈration d'un message d'erreur si aucun ÈlËve ne correspond
		if($DB_trombino->num_rows()==0)
		echo "<warning> DÈsolÈ, aucun ÈlËve ne correspond ‡ ta recherche </warning>";
		
		// GÈnÈration des fiches des ÈlËves
		while(list($eleve_id,$nom,$prenom,$surnom,$piece_id,$section,$section_id,$cie,$promo,$login,$mail,$tel) = $DB_trombino->next_row()) {
			echo "<eleve nom='$nom' prenom='$prenom' promo='$promo' login='$login' surnom='$surnom' "
				."tel='$tel' mail='".(empty($mail)?"$login@poly.polytechnique.fr":$mail)."' casert='$piece_id' "
				."section='$section' cie='$cie'>\n";
			
			// GÈnÈration de la liste des binets
			$DB_trombino->push_result();
			$DB_trombino->query("SELECT remarque,nom,membres.binet_id FROM membres "
							   ."LEFT JOIN binets USING(binet_id) WHERE eleve_id='$eleve_id'");
			while(list($remarque,$binet_nom,$binet_id) = $DB_trombino->next_row())
				echo "<binet nom='$binet_nom' id='$binet_id'>$remarque</binet>\n";
			$DB_trombino->pop_result();
			echo "</eleve>\n";
			
			// Echappe les '
			$nompolyorg = str_replace( "&apos;" , "" , $nom );
			$prenompolyorg = str_replace( "&apos;" , "" , $prenom );
			
			// Echappe les espaces
			$nompolyorg = str_replace( " " , "-" , $nompolyorg );
			$prenompolyorg = str_replace( " " , "-" , $prenompolyorg );
			
			echo "<lien url='https://www.polytechnique.org/fiche.php?user=$prenompolyorg.$nompolyorg.$promo' titre='Fiche sur polytechnique.org'/>\n";
			
			// Liens d'administration
			if(verifie_permission('admin')||verifie_permission('trombino')) {
				echo "<lien url='".BASE_URL."/admin/user.php?id=$eleve_id' titre='Administrer $prenom $nom'/>\n" ;
			}
			if(verifie_permission('admin')) {
				echo "<lien url='".BASE_URL."/?su=$eleve_id' titre='Prendre l&apos;identitÈ de $prenom $nom'/>\n" ;
			}
			
			echo "<br/>";
		}
	}
}

// Affichage du formulaire de recherche
?>
	<formulaire id="trombino" action="trombino.php">
		<champ titre="Nom" id="nom" valeur="" />
		<champ titre="PrÈnom" id="prenom" valeur="" />
		<champ titre="Surnom" id="surnom" valeur="" />
		
		<choix titre="Promo" id="promo" type="combo" valeur="">
			<option titre="Sur le campus" id=""/>
			<option titre="Toutes" id="toutes" />
			<option titre="2003" id="2003" />
			<option titre="2002" id="2002" />
			<option titre="2001" id="2001" />
			<option titre="2000" id="2000" />
			<option titre="1999" id="1999" />
			<option titre="1998" id="1998" />
		</choix>
		
		<choix titre="Section" id="section" type="combo" valeur="">
			<option titre="Toutes" id=""/>
<?php
			$DB_trombino->query("SELECT section_id,nom FROM sections ORDER BY nom ASC");
			while( list($section_id,$section_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$section_nom\" id=\"$section_id\"/>\n";
?>
		</choix>
			
		<choix titre="Binet" id="binet" type="combo" valeur="">
			<option titre="Tous" id=""/>
<?php
			$DB_trombino->query("SELECT binet_id,nom FROM binets ORDER BY nom ASC");
			while( list($binet_id,$binet_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$binet_nom\" id=\"$binet_id\"/>\n";
?>
		</choix>
		
		<champ titre="Login poly" id="loginpoly" valeur="" />
		<champ titre="TÈlÈphone" id="phone" valeur="" />
		<champ titre="Casert" id="casert" valeur="" />
		
		<bouton titre="Effacer" id="reset" />
		<bouton titre="Chercher" id="chercher" />
	</formulaire>
</page>
<?php require "include/page_footer.inc.php" ?>
