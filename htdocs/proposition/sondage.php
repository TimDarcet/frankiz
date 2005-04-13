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
	Page pour demander les sondages !
	
	$Log$
	Revision 1.15  2005/04/13 17:10:00  pico
	Passage de tous les fichiers en utf8.

	Revision 1.14  2005/03/04 23:11:33  pico
	Restriction des sondages par promo/section/binet
	
	Revision 1.13  2005/02/10 21:37:53  pico
	- Pour les ids de news, fait en fonction de la date de péremption, c'est mieux que seulement par id, mais y'a tjs un pb avec les nouvelles fraiches
	- Correction pour éviter que des gens postent des annonces qui sont déjà périmées
	
	Revision 1.12  2005/01/20 20:09:03  pico
	Changement de "Très BRment, l'automate"
	
	Revision 1.11  2005/01/18 10:35:02  pico
	Interface avancée d'édition de sondages (on édite le code, utile pour faire plusieurs sondages de même type)
	
	Revision 1.10  2005/01/14 09:19:32  pico
	Corrections bug mail
	+
	Sondages maintenant public ou privé (ne s'affichant pas dans le cadre)
	Ceci sert pour les sondages section par exemple
	
	Revision 1.9  2004/12/14 00:27:40  kikx
	Pour que le FROM des mails de validation soit au nom du mec qui demande la validation... (qu'est ce que je ferai pas pour les TOS :))
	
	Revision 1.8  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.7  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.6  2004/11/17 23:46:21  kikx
	Prepa pour le votes des sondages
	
	Revision 1.5  2004/11/17 13:32:18  kikx
	Mise en place du lien pour l'admin
	
	Revision 1.4  2004/11/17 13:27:06  kikx
	Mise ne place d'un titre dan sles sondages
	
	Revision 1.3  2004/11/17 12:13:45  kikx
	Preparation de la validation d'un sondage
	
	Revision 1.2  2004/11/16 18:17:57  kikx
	Mise ne place quasi definitive des proposition de sondages
	
	Revision 1.1  2004/11/16 15:35:27  kikx
	Pour les sondages

	
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);

$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();

$msg="" ;

 if(!isset($_REQUEST["avance"])) $_REQUEST["avance"]=1;
 if(isset($_REQUEST["btn_avance"])){
 	if($_REQUEST["btn_avance"]=="Interface simplifiée") $_REQUEST["avance"]=0;
 	if($_REQUEST["btn_avance"]=="Interface avancée") $_REQUEST["avance"]=1;
}

//---------------------------------------------------------------------------------
// Differents traitement
//---------------------------------------------------------------------------------
if (isset($_REQUEST['contenu_form']))
	$contenu_form=$_REQUEST['contenu_form'] ;
else
	$contenu_form="" ;
	

$titre_sondage="" ;
	
$erreur = 0 ;
	
// Rajout un champ

if (isset($_POST['ok_expli'])) {
	$contenu_form .= "###expli///".$_POST['explication'] ;
}
if (isset($_POST['ok_champ'])) {
	$contenu_form .= "###champ///".$_POST['question'] ;
}
if (isset($_POST['ok_text'])) {
	$contenu_form .= "###text///".$_POST['question'] ;
}
if (isset($_POST['ok_radio'])) {
	$contenu_form .= "###radio///".$_POST['question'] ;
	if ($_POST['reponse1']!="") $contenu_form .= "///".$_POST['reponse1'] ;
	if ($_POST['reponse2']!="") $contenu_form .= "///".$_POST['reponse2'] ;
	if ($_POST['reponse3']!="") $contenu_form .= "///".$_POST['reponse3'] ;
	if ($_POST['reponse4']!="") $contenu_form .= "///".$_POST['reponse4'] ;
	if ($_POST['reponse5']!="") $contenu_form .= "///".$_POST['reponse5'] ;
	if ($_POST['reponse6']!="") $contenu_form .= "///".$_POST['reponse6'] ;
}
if (isset($_POST['ok_combo'])) {
	$contenu_form .= "###combo///".$_POST['question'] ;
	if ($_POST['reponse1']!="") $contenu_form .= "///".$_POST['reponse1'] ;
	if ($_POST['reponse2']!="") $contenu_form .= "///".$_POST['reponse2'] ;
	if ($_POST['reponse3']!="") $contenu_form .= "///".$_POST['reponse3'] ;
	if ($_POST['reponse4']!="") $contenu_form .= "///".$_POST['reponse4'] ;
	if ($_POST['reponse5']!="") $contenu_form .= "///".$_POST['reponse5'] ;
	if ($_POST['reponse6']!="") $contenu_form .= "///".$_POST['reponse6'] ;
}
if (isset($_POST['ok_check'])) {
	$contenu_form .= "###check///".$_POST['question'] ;
	if ($_POST['reponse1']!="") $contenu_form .= "///".$_POST['reponse1'] ;
	if ($_POST['reponse2']!="") $contenu_form .= "///".$_POST['reponse2'] ;
	if ($_POST['reponse3']!="") $contenu_form .= "///".$_POST['reponse3'] ;
	if ($_POST['reponse4']!="") $contenu_form .= "///".$_POST['reponse4'] ;
	if ($_POST['reponse5']!="") $contenu_form .= "///".$_POST['reponse5'] ;
	if ($_POST['reponse6']!="") $contenu_form .= "///".$_POST['reponse6'] ;
}
if (isset($_POST['titre_sondage']))
	$titre_sondage=$_POST['titre_sondage'] ;

if (isset($_POST['valid'])) {
	if ($titre_sondage!="") {
		$restriction = '';
		if(($_REQUEST['restriction']!='aucune')&&($_REQUEST[$_REQUEST['restriction']])!='')
			$restriction = ",restriction='{$_REQUEST['restriction']}_{$_REQUEST[$_REQUEST['restriction']]}'";
		$DB_valid->query("INSERT INTO valid_sondages SET eleve_id =".$_SESSION['user']->uid.", questions='$contenu_form', titre='$titre_sondage', perime=FROM_UNIXTIME({$_POST['date']}) $restriction") ;
		
		$tempo = explode("proposition",$_SERVER['REQUEST_URI']) ;
	
		$contenu = "<strong>Bonjour,</strong><br><br>".
			"$prenom $nom a demandé la validation d'un sondage : <br>".
			"<br>".
			"Pour valider ou non cette demande va sur la page suivante<br>".
			"<div align='center'><a href='http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_sondages.php'>".
			"http://".$_SERVER['SERVER_NAME'].$tempo[0]."admin/valid_sondages.php</a></div><br><br>" .
			"Cordialement,<br>" .
			"Le Webmestre de Frankiz<br>"  ;
					
		couriel(WEBMESTRE_ID,"[Frankiz] Validation d'un sondage",$contenu,$_SESSION['user']->uid);
	} else {
		$erreur = 1 ;
	}
}


//=================
//===============
// Génération de la page
//===============
//=================

require_once BASE_LOCAL."/include/page_header.inc.php";

?>



<page id="propoz_sondage" titre="Frankiz : Propose un sondage">
<h1>Proposition de sondage</h1>
<?

if ((isset($_POST['valid']))&&($erreur==0)) {
?>
	<commentaire>
		Tu as demandé à un webmestre de valider ton sondage<br/>
		Il faut compter 24h pour que ton sondage soit prise en compte par notre système<br/>
		<br/>
		Nous te remercions d'avoir soumis un sondage et nous essayerons d'y répondre le plus rapidement possible<br/>
	</commentaire>
	
	
	<formulaire id="form" titre="<?=$titre_sondage?>">	
<?
	decode_sondage($contenu_form) ;
?>
	</formulaire>
<?	

} else {
	if ($erreur==1) {
 		?>
		<warning>
		Remplis le titre du sondage, merci.
		</warning>
		<?
	}
?>
<formulaire id="form" titre="Aperçu de ton sondage">	
	<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
	<hidden id="titre_sondage" valeur="<?=$titre_sondage?>"/>
	<h2><?=$titre_sondage?></h2>
		
<?
	decode_sondage($contenu_form) ;
?>
	
	<choix titre="Sondage jusqu'à " id="date" type="combo" valeur="<? if (isset($_REQUEST['date'])) echo $_REQUEST['date'] ;?>">
<?	for ($i=1 ; $i<=MAX_PEREMPTION ; $i++) {
		$date_id = mktime(0, 0, 0, date("m") , date("d") + $i, date("Y")) ;
		$date_value = date("d/m/y" , $date_id);
?>
		<option titre="<? echo $date_value?>" id="<? echo $date_id?>" />
<?
	}
?>
	</choix>
	<note>Si tu souhaite que ce sondage soit réservé à certaines personnes, définis le ici</note>
	<choix titre="Restreindre" id="restriction" type="radio" valeur="<? echo (isset($_REQUEST['restriction'])) ? $_REQUEST['restriction']:"aucune" ;?>">
		<option id="aucune" titre="Aucune"/>
		<option id="promo" titre="A une promo"/>
		<option id="section" titre="A une section"/>
		<option id="binet" titre="A un binet"/>
	</choix>
	<choix titre="Promo" id="promo" type="combo" valeur="">
		<option titre="Toutes" id="" />
<?php
		$DB_trombino->query("SELECT DISTINCT promo FROM eleves ORDER BY promo DESC");
		while( list($promo) = $DB_trombino->next_row() )
			echo "\t\t\t<option titre=\"$promo\" id=\"$promo\"/>\n";
?>
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
	<br/>
	<bouton titre="Valider le sondage" id="valid" onClick="return window.confirm('Voulez vous vraiment valider votre sondage ?')" />
	<bouton titre="Interface <?= ($_REQUEST["avance"]==1)?"simplifiée":"avancée";?>" id="btn_avance"/>
</formulaire>

<formulaire id="ajout_titre" titre="OBLIGATOIRE: le titre du sondage" action="proposition/sondage.php">
	<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
	<champ id="titre_sondage" titre="Titre" valeur="<?=$titre_sondage?>"/>
	<bouton titre="Mettre à jour le titre" id="ok_titre" />
</formulaire>
	
<? if(isset($_REQUEST["avance"])&&$_REQUEST["avance"]==1){ ?>
	<formulaire id="edit" titre="Edite ton sondage" action="proposition/sondage.php">
		<note>
			La syntaxe est la suivante:<br/>
			Pour une explication: ###expli///Mon texte<br/>
			Pour un champ: ###champ///Le nom du champ<br/>
			Pour un texte: ###text///Ma question<br/>
			Pour un radio: ###radio///ma question///option1///option2///option3<br/>
			Pour une boite déroulante: ###combo///ma question///option1///option2///option3<br/>
			Pour une checkbox: ###check///ma question///option1///option2///option3<br/>
		</note>
		<zonetext id="contenu_form" titre="Zone d'édition avancée" type="grand"><?=$contenu_form?></zonetext>
		<hidden id="titre_sondage" titre="Titre" valeur="<?=$titre_sondage?>"/>
		<bouton titre="Mettre à jour le sondage" id="ok_sondage" />
		<hidden id="avance" valeur="1"/>
	</formulaire>
<? }else{ ?>
	<formulaire id="ajout_simple" titre="Rajoute une explication" action="proposition/sondage.php">
		<hidden id="titre_sondage" valeur="<?=$titre_sondage?>"/>
		<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
		<zonetext id="explication" titre="Explication"></zonetext>
		<hidden id="avance" valeur="0"/>
		<bouton titre="Ajouter" id="ok_expli" />
	</formulaire>
	<formulaire id="ajout_champ" titre="Rajoute une question de type 'champ'" action="proposition/sondage.php">
		<hidden id="titre_sondage" valeur="<?=$titre_sondage?>"/>
		<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
		<champ id="question" titre="Question" valeur=""/>
		<hidden id="avance" valeur="0"/>
		<bouton titre="Ajouter" id="ok_champ" />
	</formulaire>
	<formulaire id="ajout_champ" titre="Rajoute une question de type 'textarea'" action="proposition/sondage.php">	
		<hidden id="titre_sondage" valeur="<?=$titre_sondage?>"/>
		<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
		<champ id="question" titre="Question" valeur=""/>
		<hidden id="avance" valeur="0"/>
		<bouton titre="Ajouter" id="ok_text" />
	</formulaire>
	<formulaire id="ajout_champ" titre="Rajoute une question de type 'radio'" action="proposition/sondage.php">	
		<hidden id="titre_sondage" valeur="<?=$titre_sondage?>"/>
		<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
		<champ id="question" titre="Question" valeur=""/>
		<textsimple titre="Maintenant rajouter les réponses possibles"/>
		<champ id="reponse1" titre="Reponse 1" valeur=""/>
		<champ id="reponse2" titre="Reponse 2" valeur=""/>
		<champ id="reponse3" titre="Reponse 3" valeur=""/>
		<champ id="reponse4" titre="Reponse 4" valeur=""/>
		<champ id="reponse5" titre="Reponse 5" valeur=""/>
		<champ id="reponse6" titre="Reponse 6" valeur=""/>
		<hidden id="avance" valeur="0"/>
		<bouton titre="Ajouter" id="ok_radio" />
	</formulaire>
	<formulaire id="ajout_champ" titre="Rajoute une question de type 'checkbox'" action="proposition/sondage.php">	
		<hidden id="titre_sondage" valeur="<?=$titre_sondage?>"/>
		<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
		<champ id="question" titre="Question" valeur=""/>
		<textsimple titre="Maintenant rajouter les réponses possibles"/>
		<champ id="reponse1" titre="Reponse 1" valeur=""/>
		<champ id="reponse2" titre="Reponse 2" valeur=""/>
		<champ id="reponse3" titre="Reponse 3" valeur=""/>
		<champ id="reponse4" titre="Reponse 4" valeur=""/>
		<champ id="reponse5" titre="Reponse 5" valeur=""/>
		<champ id="reponse6" titre="Reponse 6" valeur=""/>
		<hidden id="avance" valeur="0"/>
		<bouton titre="Ajouter" id="ok_check" />
	</formulaire>
	<formulaire id="ajout_champ" titre="Rajoute une question de type 'liste déroulante'" action="proposition/sondage.php">	
		<hidden id="titre_sondage" valeur="<?=$titre_sondage?>"/>
		<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
		<champ id="question" titre="Question" valeur=""/>
		<textsimple titre="Maintenant rajouter les réponses possibles"/>
		<champ id="reponse1" titre="Reponse 1" valeur=""/>
		<champ id="reponse2" titre="Reponse 2" valeur=""/>
		<champ id="reponse3" titre="Reponse 3" valeur=""/>
		<champ id="reponse4" titre="Reponse 4" valeur=""/>
		<champ id="reponse5" titre="Reponse 5" valeur=""/>
		<champ id="reponse6" titre="Reponse 6" valeur=""/>
		<hidden id="avance" valeur="0"/>
		<bouton titre="Ajouter" id="ok_combo" />
	</formulaire>
<?
	}
}
?>

</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
