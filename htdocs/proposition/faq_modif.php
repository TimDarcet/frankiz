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
		$Log$
		Revision 1.6  2004/12/14 23:15:38  kikx
		Pour avoir la source + affichage

		Revision 1.5  2004/12/14 23:11:11  kikx
		Oups /.me boulet
		
		Revision 1.4  2004/12/14 23:09:52  kikx
		Pour avoir qd meme la page modifié
		
		Revision 1.3  2004/12/14 23:06:06  schmurtz
		Ajout du support zonetext grand pour les faqs
		
		Revision 1.2  2004/12/14 23:00:50  kikx
		Car c'est trop la merde sinon
		
		Revision 1.1  2004/12/14 22:17:32  kikx
		Permet now au utilisateur de modifier les Faqqqqqqqqqqqqqqqq :)
		

		
*/
require_once "../include/global.inc.php";
require_once "../include/wiki.inc.php";
// Vérification des droits
demande_authentification(AUTH_MINIMUM);


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="modif_faq" titre="Frankiz : Modification de la FAQ">
<h1>Modification FAQ</h1>
<?
$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;


if (isset($_REQUEST['valid'])) {
	$DB_valid->query("SELECT 0 FROM valid_modiffaq WHERE faq_id='{$_REQUEST['id']}'") ;
	if ($DB_valid->num_rows()==0) {

		$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
		list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();
	
		$DB_valid->query("INSERT INTO valid_modiffaq SET faq_modif='{$_REQUEST['faq_modif']}', faq_id='{$_REQUEST['id']}', eleve_id='{$_SESSION['user']->uid}'") ;
		
		$contenu = "<strong>Bonjour,</strong><br><br>".
			"$prenom $nom a demandé la modification d'une FAQ<br>".
			"Pour valider ou non cette demande va sur la page suivante<br>".
			"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_faqmodif.php'>".
			"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_faqmodif.php</a></div><br><br>" .
			"Très BR-ement<br>" .
			"L'automate :)<br>"  ;
	
		couriel(FAQMESTRE_ID,"[Frankiz] Validation d'une modification d'une FAQ",$contenu,$eleve_id);
		
		?>
		<commentaire>Tu as soumis au FAQmestre une modification d'une des FAQ. Nous t'en remercions... Elle sera traité le plus rapidement possible</commentaire>
		<?
	} else {
	?>
		<warning>Il y a déjà une demande de modification pour cette FAQ et les FAQmestres ne peuvent pas les gérer en même temps... Attend 1 jour et retente...</warning>
	<?
	}

} else {
//
// Corps du Documents pour les réponses
//---------------------------------------------------
	$DB_valid->query("SELECT 0 FROM valid_modiffaq WHERE faq_id='{$_REQUEST['id']}'") ;
	if ($DB_valid->num_rows()==0) {

		if(isset($_REQUEST['id'])) 
			$id = $_REQUEST['id'] ; 
		else 
			$id = "";
		
		if ($id != "") {
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
						
						if (isset($_REQUEST['faq_modif'])) {
							print(wikiVersXML($_POST['faq_modif'])) ;
							echo "<note>Source : </note>" ;
							print(diff_to_xml($wiki,$_POST['faq_modif']));
						}else{ 
							print(wikiVersXML($wiki)) ;
							echo "<note>Source : </note>" ;
							print($wiki);
						}
							
						fclose($texte);
					}
				} else {
				?>
					<warning>Erreur : Impossible de trouver cette question </warning>
				<?
				}
				?>
				</cadre>
				
				<formulaire id='modif_faq' titre='Modification' action='proposition/faq_modif.php?id=<?=$id?>'>
					<zonetext titre="FAQ" id='faq_modif' type="grand"><?
					if (isset($_REQUEST['faq_modif']))
						print($_POST['faq_modif']);
					else 
						print($wiki);
					
					
					?></zonetext>
					<bouton id='test' titre="Tester"/>
					<bouton id='valid' titre='Valider' onClick="return window.confirm('Voulez vous vraiment soumettre cette FAQ modifiée aux webmestres ?')"/>
		
				</formulaire>
				<?
			} else {
			?>
				<warning>Erreur : Impossible de trouver cette question </warning>
			<?
			}
			
	
		}
	} else {
		?>
			<warning>Il y a déjà une demande de modification pour cette FAQ et les FAQmestres ne peuvent pas les gérer en même temps... Attend 1 jour et retente...</warning>
		<?
	}
}
//
// Pied de page ...
//---------------------------------------------------
?>

</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>