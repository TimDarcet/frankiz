<?php
/*
	Page qui permet aux utilisateurs de demander le rajout d'une annonce
	
	$Log$
	Revision 1.19  2004/10/20 23:21:39  schmurtz
	Creation d'un element <html> qui permet d'afficher du html brute sans verification
	C'est ce qui est maintenant utilise dans les annonces/cadres

	Revision 1.18  2004/10/19 14:58:43  schmurtz
	Creation d'un champ de formulaire specifique pour les fichiers (sans passer
	l'element champ, qui actuellement est un peu acrobatique).
	
	Revision 1.17  2004/10/13 19:37:13  kikx
	oubli
	
	Revision 1.16  2004/10/11 11:01:38  kikx
	Correction des pages de proposition et de validation des annonces pour permettre
	- de stocker les image au bon endroit
	- de mettre les annonces su l'esterieur
	
	Revision 1.15  2004/10/04 21:48:54  kikx
	Modification du champs fichier pour uploader des fichiers
	
	Revision 1.14  2004/09/20 22:19:28  kikx
	test
	
	Revision 1.13  2004/09/20 07:14:41  kikx
	Permet de supprimer l'image qd on va valider l'annonce !!!
	C'est chaint si on peut pas la suppimer
	
	Revision 1.12  2004/09/18 16:22:26  kikx
	micro bug fix
	
	Revision 1.11  2004/09/18 16:04:52  kikx
	Beaucoup de modifications ...
	Am�lioration des pages qui g�rent les annonces pour les rendre compatible avec la nouvelle norme de formatage xml -> balise web et balise image qui permette d'afficher une image et la signature d'une personne
	
	Revision 1.8  2004/09/17 16:14:43  kikx
	Pffffff ...
	Je sais plus trop ce que j'ai fait donc allez voir le code parce que la ca me fait chi� de refl�chir
	
	Revision 1.7  2004/09/17 14:19:58  kikx
	Page de demande d'annonce termin�
	Ajout d'une page de validations d'annonces
	
*/
	
require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_MINIMUM);

$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();


//---------------------------------------------------------------------------------
// On traite l'image qui vient d'etre uploader si elle existe
//---------------------------------------------------------------------------------

//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//
//--//
//--// Norme de nommage des images que nous uploadons
//--// dans le rep pr�vu a cette effet : 
//--// # annonce_{$id_eleves} qd l'annonce n'a pas �t� soumise � validation
//--// # {$id_annonce_a_valider}_annonce qd l'annonce est soumise � validation
//--//
//--// Ceci permet de faire la diff�rence entre les fichiers tempo et les fichiers a valider
//--//
//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//

$erreur_upload = 0 ;
if ((isset($_FILES['file']))&&($_FILES['file']['size']!=0))  {
	$original_size = getimagesize($_FILES['file']['tmp_name']);
	$filetype = $_FILES['file']['type'] ;
	
	$larg = $original_size[0];
	$haut = $original_size[1];
	if (($larg>=400)||($haut>=300)) {
		$erreur_upload =1 ;
	} else if (($filetype=="image/jpg")||($filetype=="image/jpeg")||($filetype=="image/pjpg")||($filetype=="image/gif")||($filetype=="image/png")) {
		$filename = "temp_$eleve_id" ;
		move_uploaded_file($_FILES['file']['tmp_name'], DATA_DIR_LOCAL ."annonces/". $filename) ;
	} else {
		$erreur_upload = 1 ;
	}

}
//================================================
// On Supprime l'image qui a �t� upload� (si elle existe bien sur :))
//================================================

if (isset($_POST['suppr_img'])) {

	if (file_exists(DATA_DIR_LOCAL."annonces/temp_$eleve_id")) {
		unlink(DATA_DIR_LOCAL."annonces/temp_$eleve_id") ; 
	}
}
//================================================
// On valide l'annonce et en envoie un mail aux webmestres pour les pr�venir 
//================================================

if (isset($_POST['valid'])) {

	$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;

	if (isset($_REQUEST['ext']))
		$temp_ext = '1'  ;
	else 
		$temp_ext = '0' ;

	$DB_valid->query("INSERT INTO valid_annonces SET perime=FROM_UNIXTIME({$_POST['date']}), eleve_id='".$_SESSION['user']->uid."', titre='".$_POST['titre']."',contenu='".$_POST['text']."', exterieur=$temp_ext");
	
	// on modifie le nom du fichier qui a �t� t�l�charg� si celui ci existe
	// selon la norme de nommage ci-dessus
	//----------------------------------------------------------------------------------------------
	
	if (file_exists(DATA_DIR_LOCAL."annonces/temp_$eleve_id")) {
		$index = mysql_insert_id() ;
		rename(DATA_DIR_LOCAL."annonces/temp_$eleve_id",DATA_DIR_LOCAL."annonces/a_valider_{$index}") ; 
	}
	$contenu = "$prenom $nom a demand� la validation d'une annonce : \n".
				$_POST['titre']."\n\n".
				"Pour valider ou non cette demande va sur la page suivante : \n".
				"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_annonces.php\n\n" .
				"Tr�s BR-ement\n" .
				"L'automate :)\n"  ;
				
	mail(MAIL_WEBMESTRE,"[Frankiz] Validation d'une annonce",$contenu);

}
//=================
//===============
// G�n�ration de la page
//===============
//=================

require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="propoz_annonce" titre="Frankiz : Propose une annonce">
<h1>Proposition d'annonce</h1>

 <?
if ($erreur_upload==1) {
?>
	<warning><p>Ton fichier n'a pas �t� t�l�charg� car il ne respecte pas une des conditions sp�cifi�es ci dessous</p>
		<p>Dimension : <? echo $larg."x".$haut ;?></p>
		<p>Taille : <? echo $_FILES['file']['size'] ;?> octets</p>
		<p>Type : <? echo $filetype ;?></p>
	</warning>
<?

}
//=========================================
// PREVISUALISATION :
// On teste l'affichage de l'annonce pour voir � quoi �a ressemble
//=========================================

if (!isset($_POST['text'])) $_POST['text']="c'est &lt;b&gt;en gras&lt;/b&gt;, "
									."&lt;u&gt;en soulign�&lt;/u&gt;, "
									."&lt;i&gt;en italique&lt;/i&gt;, "
									."&lt;a href='http://frankiz'&gt;un lien&lt;/a&gt;,"
									."&lt;a href='mailto:toto@poly'&gt;un lien email&lt;/a&gt;" ;
if (!isset($_POST['titre']))  $_POST['titre']="Titre" ;

?>
	<annonce titre="<?php echo $_POST['titre'] ; ?>" 
			categorie=""
			date="<? echo date("d/m/y") ?>">
			<? 
			echo "<html>{$_POST['text']}</html>" ;
			if ((!isset($_POST['valid']))&&(file_exists(DATA_DIR_LOCAL."annonces/temp_$eleve_id"))) {
			?>
				<image source="<?echo DATA_DIR_URL."annonces/temp_".$eleve_id ; ?>" texte=""/>
			<? 
			} else if ((isset($index))&&(file_exists(DATA_DIR_LOCAL."annonces/a_valider_{$index}"))){
			?>
				<image source="<?echo DATA_DIR_URL."annonces/a_valider_".$index ; ?>" texte=""/>
			<?
			}
			?>
			<eleve nom="<?=$nom?>" prenom="<?=$prenom?>" promo="<?=$promo?>" surnom="<?=$surnom?>" mail="<?=$mail?>"/>
	</annonce>
<?

//=========================
// On met le commentaire qui va bien 
//=========================

if (isset($_POST['valid'])) {
?>
	<commentaire>
		<p>Tu as demand� � un webmestre de valider ton annonce</p>
		<p>Il faut compter 24h pour que ton annonce soit prise en compte par notre syst�me</p>		
		<p>&nbsp;</p>		
		<p>Nous te remercions d'avoir soumis une annonce et nous essayerons d'y r�pondre le plus rapidement possible</p>		
	</commentaire>
<?	
} else {
//====================
// Zone de saisie de l'annonce
//====================
?>

	<formulaire id="propoz_annonce" titre="Ton annonce" action="proposition/annonce.php">
		<champ id="titre" titre="Le titre" valeur="<? if (isset($_POST['titre'])) echo $_POST['titre'] ;?>"/>
		<zonetext id="text" titre="Le texte" valeur="<? if (isset($_POST['text'])) echo $_POST['text'] ;?>"/>
		<textsimple valeur="Ton image doit �tre un fichier gif, png ou jpg, ne doit pas d�passer 400x300 pixels et 250ko car sinon elle ne sera pas t�l�charg�e"/>
		<fichier id="file" titre="Ton image" taille="250000"/>
		<bouton id='suppr_img' titre="Supprimer l'image"/>

		<textsimple valeur="Ta signature sera automatiquement g�n�r�"/>
		<choix titre="Date de p�remption" id="date" type="combo" valeur="<? if (isset($_REQUEST['date'])) echo $_REQUEST['date'] ;?>">
<?		for ($i=0 ; $i<MAX_PEREMPTION ; $i++) {
			$date_id = mktime(0, 0, 0, date("m") , date("d") + $i, date("Y")) ;
			$date_value = date("d/m/y" , $date_id);
?>
			<option titre="<? echo $date_value?>" id="<? echo $date_id?>" />
<?
		}
?>
		</choix>
		
		<textsimple valeur="Si tu veux que ton annonce soit visible de l'exterieur, clique ici"/>
		<choix titre="Exterieur" id="exterieur" type="checkbox" valeur="<? if (isset($_REQUEST['ext'])) echo 'ext' ;?>">
			<option id="ext" titre=""/>
		</choix>

		<bouton id='test' titre="Tester"/>
		<bouton id='valid' titre='Valider' onClick="return window.confirm('Voulez vous vraiment valider votre annonce ?')"/>
	</formulaire>
<?
}
?>

</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
