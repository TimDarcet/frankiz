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
	Page qui permet aux faqmestres de valider une modification FAQ
	

	$Log$
	Revision 1.1  2004/12/14 22:49:53  kikx
	oups désolé


*/
	
require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin') && !verifie_permission('faq'))
	rediriger_vers("/gestion/");

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_qdj" titre="Frankiz : Valide les modif FAQ">

<h1>Validation de Modif FAQ</h1>

<?
// On traite les différents cas de figure d'enrigistrement et validation de qdj :)

// Enregistrer ...
$DB_valid->query("LOCK TABLE valid_modiffaq WRITE");
$DB_valid->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;


	if (($temp[0]=='modif')||($temp[0]=='valid')) {
		$DB_valid->query("SELECT 0 FROM valid_modiffaq WHERE faq_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {

			$DB_valid->query("UPDATE valid_modiffaq SET faq_modif='{$_POST['faq_modif']}'  WHERE faq_id='{$temp[1]}'");
		?>
			<commentaire>Modif effectuée</commentaire>
		<?
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?
		}
	}
	
	if ($temp[0]=='valid') {
		$DB_valid->query("SELECT eleve_id FROM valid_modiffaq WHERE faq_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {

			list($eleve_id) = $DB_valid->next_row() ;
				
			// On remplace le fichier par la faq modifié
			$DB_faq->query("SELECT question,reponse FROM faq WHERE faq_id='{$temp[1]}'") ;
			if (list($question,$reponse) = $DB_faq->next_row()) {
				$filename = BASE_DATA."/faq/".$reponse;
				
				$somecontent = $_POST['faq_modif'] ;
				$somecontent = str_replace(array('&amp;','&lt;','&gt;','&apos;','&quot;',''), array('&','<','>','\'','"','\\'),$somecontent);
				
				$erreur =0 ;
				// Assurons nous que le fichier est accessible en écriture
				if (is_writable($filename)) {

					// Mode remplacement
					if (!$handle = fopen($filename, 'w+'))
						$erreur=1 ;
	
					// Ecrivons quelque chose dans notre fichier.
					if (fwrite($handle, $somecontent) === FALSE)
						$erreur =1 ;
					
					fclose($handle);
				} else {
		?>
					<warning>Erreur Ecriture : Remplacement de la FAQ impossible</warning>
		<?
				}
				
				if ($erreur ==0){
					$DB_valid->query("DELETE FROM valid_modiffaq WHERE faq_id='{$temp[1]}'") ;
		?>
					<commentaire>Validation effectuée</commentaire>
					
		<?
					$contenu = "<strong>Bonjour,</strong><br><br>".
						"Ta modification de la FAQ vient d'être pris en compte par le BR<br>".
						"Nous te remercions sincèrement de ta modification<br>".
						"<br>Sincèrement<br>" .
						"Le BR<br>"  ;
				
					couriel($eleve_id,"[Frankiz] Ta modification de la FAQ vient d'être pris en compte",$contenu);

				} else {
		?>
					<warning>Erreur Ecriture : Remplacement de la FAQ impossible</warning>
		<?
				}
			}
			
		}
	}
	if ($temp[0]=='suppr') {
		$DB_valid->query("SELECT eleve_id FROM valid_modiffaq WHERE faq_id='{$temp[1]}'");
		if ($DB_valid->num_rows()!=0) {
	
			list($eleve_id) = $DB_valid->next_row() ;
			$DB_valid->query("DELETE FROM valid_modiffaq WHERE faq_id='{$temp[1]}'") ;
		?>
			<warning>Suppression d'une modif de FAQ</warning>
		<?
		} else {
	?>
			<warning>Requête deja traitée par un autre administrateur</warning>
	<?
		}

	}
}
$DB_valid->query("COMMIT");
$DB_valid->query("UNLOCK TABLES");
//===============================

	$DB_valid->query("SELECT v.faq_id,v.faq_modif, e.nom, e.prenom, e.surnom, e.promo, e.mail, e.login FROM valid_modiffaq as v INNER JOIN trombino.eleves as e USING(eleve_id)");
	
	while(list($id,$faq_modif,$nom, $prenom, $surnom, $promo,$mail,$login) = $DB_valid->next_row()) {

		$DB_faq->query("SELECT question,reponse FROM faq WHERE faq_id='{$id}'") ;
		if (list($question,$reponse) = $DB_faq->next_row()) {
			$repfaq = BASE_DATA."/faq/".$reponse;
			echo "<cadre titre=\"Q: ".$question."\" id=\"reponse\">\n";
			if(file_exists($repfaq)){
				if($texte = fopen($repfaq,"r")){
					$wiki = '';
					while(!feof($texte)) {
						$ligne = fgets($texte,2000);
						// Remplace les liens locaux pour les images et les liens, car sinon conflit avec le BASE_HREF
						$patterns[0] ='(\[(?!http://)(?!ftp://)(?!#))';
						$patterns[1] ='(\[#)';
						$replacements[1] = '['.dirname(URL_DATA."faq/$reponse")."/";
						$replacements[0] = '['.getenv('SCRIPT_NAME')."?".getenv('QUERY_STRING')."#";
						$ligne = str_replace(array('&','<','>','\'','"','\\'),array('&amp;','&lt;','&gt;','&apos;','&quot;',''),$ligne);
						$ligne = preg_replace($patterns,$replacements, $ligne);
						$wiki.= $ligne;
					}
					
			
					print(wikiVersXML(diff_to_xml($wiki,$faq_modif)));
						
					fclose($texte);
				}
			} else {
			?>
				<warning>Erreur : Impossible de trouver cette question </warning>
			<?
			}
			?>
			</cadre>
			
			<formulaire id='modif_faq' titre='Modification' action= 'admin/valid_faqmodif.php'>
				<note>Modification apportée par <? echo "$prenom $nom ($promo)"?></note>
				<zonetext titre="FAQ" id='faq_modif'><?=$faq_modif?></zonetext>
				<bouton id='modif_<?=$id?>' titre="Modifier"/>
				<bouton id='valid_<?=$id?>' titre='Valider' onClick="return window.confirm('Voulez vous vraiment validé cette modification ?')"/>
				<bouton id='suppr_<?=$id?>' titre='Supprimer' onClick="return window.confirm('Voulez vous vraiment supprimer cette modification de FAQ ?')"/>
	
			</formulaire>
			<?
		} else {
		?>
			<warning>Erreur : Impossible de trouver cette question </warning>
		<?
		}
		

	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
