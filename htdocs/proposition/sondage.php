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
	
	$Id$

*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);

$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();

$msg="" ;

// Choix de l'interface par défaut
 if(!isset($_REQUEST["avance"])) $_REQUEST["avance"]=0;
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

if (isset($_REQUEST['restriction']))
	$restriction=$_REQUEST['restriction'];
else
	$restriction = "";

if (isset($_REQUEST['perimdate']))
	$perimdate = $_REQUEST['perimdate'];
else
	$perimdate = mktime(0, 0, 0, date("m") , date("d") + 1, date("Y"));

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

if (isset($_POST['ok_infosgen'])) {
	if(($_REQUEST['restriction']!='aucune')&&($_REQUEST[$_REQUEST['restriction']])!='')
		$restriction = $_REQUEST['restriction']."_".$_REQUEST[$_REQUEST['restriction']];
}

if (isset($_POST['valid'])) {
	if ($titre_sondage!="") {
		if ($restriction != "") $restriction = ", restriction='".$restriction."'";
		$DB_valid->query("INSERT INTO valid_sondages SET eleve_id =".$_SESSION['user']->uid.", questions='$contenu_form', titre='$titre_sondage', perime=FROM_UNIXTIME($perimdate) $restriction") ;
		
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
<?php

if ((isset($_POST['valid']))&&($erreur==0)) {
?>
	<commentaire>
		Tu as demandé à un webmestre de valider ton sondage<br/>
		Il faut compter 24h pour que ton sondage soit prise en compte par notre système<br/>
		<br/>
		Nous te remercions d'avoir soumis un sondage et nous essayerons d'y répondre le plus rapidement possible<br/>
	</commentaire>
	
	
	<formulaire id="form" titre="<?php echo $titre_sondage; ?>">	
<?php
	decode_sondage($contenu_form) ;
?>
	</formulaire>
<?php	

} else {
	if ($erreur==1) {
 		?>
		<warning>
		Remplis le titre du sondage, merci.
		</warning>
		<?php
	}
?>
<formulaire id="form" titre="Aperçu de ton sondage"  action="proposition/sondage.php">	
	<hidden id="contenu_form" valeur="<?php echo $contenu_form; ?>"/> 	
	<hidden id="titre_sondage" valeur="<?php echo $titre_sondage; ?>"/>
	<hidden id="perimdate" valeur="<?php echo $perimdate; ?>"/>
	<hidden id="restriction" valeur="<?php echo $restriction; ?>"/>
	<h2><?php echo $titre_sondage; ?></h2>
		
<?php
	decode_sondage($contenu_form) ;
?>
	
	<bouton titre="Valider le sondage" id="valid" onClick="return window.confirm('Voulez vous vraiment valider votre sondage ?')" />
	<bouton titre="Interface <?php echo $_REQUEST['avance'] == 1 ? 'simplifiée' : 'avancée'; ?>" id="btn_avance"/>
</formulaire>

<warning>Bien mettre à jour chaque portion ci-dessous avant de valider le sondage (les informations non mises à jour ne sont pas enregistrées)</warning>

<formulaire id="infos_generales" titre="Informations générales"  action="proposition/sondage.php">	
	<hidden id="contenu_form" valeur="<?php echo $contenu_form; ?>"/> 	
	<hidden id="titre_sondage" valeur="<?php echo $titre_sondage; ?>"/>
	<hidden id="avance" valeur="<?php echo $_REQUEST['avance']; ?>"/>  
	<choix titre="Sondage jusqu'à " id="perimdate" type="combo" valeur="<?php echo $perimdate; ?>">
<?php	for ($i=1 ; $i<=MAX_PEREMPTION ; $i++) {
		$date_id = mktime(0, 0, 0, date("m") , date("d") + $i, date("Y")) ;
		$date_value = date("d/m/y" , $date_id);
?>
		<option titre="<?php echo $date_value?>" id="<?php echo $date_id?>" />
<?php
	}
?>
	</choix>
	<note>Si tu souhaite que ce sondage soit réservé à certaines personnes, définis le ici</note>
<?php
if ($restriction != "") $temp = explode("_",$restriction);
else $temp = array("aucune","");
?>
	<choix titre="Restreindre" id="restriction" type="radio" valeur="<?php echo $temp[0]; ?>">
		<option id="aucune" titre="Aucune"/>
		<option id="promo" titre="A une promo"/>
		<option id="section" titre="A une section"/>
		<option id="binet" titre="A un binet"/>
	</choix>
	<choix titre="Promo" id="promo" type="combo" valeur="<?php if ($temp[0]=="promo") echo $temp[1];?>">
		<option titre="Toutes" id="" />
<?php
		$DB_trombino->query("SELECT DISTINCT promo FROM eleves ORDER BY promo DESC");
		while( list($promo) = $DB_trombino->next_row() )
			echo "\t\t\t<option titre=\"$promo\" id=\"$promo\"/>\n";
?>
	</choix>
	<choix titre="Section" id="section" type="combo" valeur="<?php if ($temp[0]=="section") echo $temp[1];?>">
		<option titre="Toutes" id=""/>
<?php
		$DB_trombino->query("SELECT section_id,nom FROM sections ORDER BY nom ASC");
		while( list($section_id,$section_nom) = $DB_trombino->next_row() )
			echo "\t\t\t<option titre=\"$section_nom\" id=\"$section_id\"/>\n";
?>
	</choix>
	<choix titre="Binet" id="binet" type="combo" valeur="<?php if ($temp[0]=="binet") echo $temp[1];?>">
		<option titre="Tous" id=""/>
<?php
		$DB_trombino->query("SELECT binet_id,nom FROM binets ORDER BY nom ASC");
		while( list($binet_id,$binet_nom) = $DB_trombino->next_row() )
			echo "\t\t\t<option titre=\"$binet_nom\" id=\"$binet_id\"/>\n";
?>
	</choix>
	<br/>
	<bouton titre="Mettre à jour les informations générales" id="ok_infosgen" />
</formulaire>

<formulaire id="ajout_titre" titre="OBLIGATOIRE: le titre du sondage" action="proposition/sondage.php">
	<hidden id="contenu_form" valeur="<?php echo $contenu_form; ?>"/> 	
	<hidden id="perimdate" valeur="<?php echo $perimdate; ?>"/>
	<hidden id="restriction" valeur="<?php echo $restriction; ?>"/>
	<hidden id="avance" valeur="<?php echo $_REQUEST['avance']; ?>"/>  
	<champ id="titre_sondage" titre="Titre" valeur="<?php echo $titre_sondage; ?>"/>
	<bouton titre="Mettre à jour le titre" id="ok_titre" />
</formulaire>
	
<?php if(isset($_REQUEST["avance"])&&$_REQUEST["avance"]==1){ ?>
	<formulaire id="edit" titre="Edite ton sondage" action="proposition/sondage.php">
		<hidden id="titre_sondage" valeur="<?php echo $titre_sondage; ?>"/>
		<hidden id="perimdate" valeur="<?php echo $perimdate; ?>"/>
		<hidden id="restriction" valeur="<?php echo $restriction; ?>"/>
		<hidden id="avance" valeur="1"/>
		<note>
			La syntaxe est la suivante:<br/>
			Pour une explication: ###expli///Mon texte<br/>
			Pour un champ: ###champ///Le nom du champ<br/>
			Pour un texte: ###text///Ma question<br/>
			Pour un radio: ###radio///ma question///option1///option2///option3<br/>
			Pour une boite déroulante: ###combo///ma question///option1///option2///option3<br/>
			Pour une checkbox: ###check///ma question///option1///option2///option3<br/>
		</note>
		<zonetext id="contenu_form" titre="Zone d'édition avancée" type="grand"><?php echo $contenu_form; ?></zonetext>
		<bouton titre="Mettre à jour le sondage" id="ok_sondage" />
	</formulaire>
<?php }else{ ?>
	<formulaire id="ajout_simple" titre="Rajoute une explication" action="proposition/sondage.php">
		<hidden id="contenu_form" valeur="<?php echo $contenu_form; ?>"/> 	
		<hidden id="titre_sondage" valeur="<?php echo $titre_sondage; ?>"/>
		<hidden id="perimdate" valeur="<?php echo $perimdate; ?>"/>
		<hidden id="restriction" valeur="<?php echo $restriction; ?>"/>
		<hidden id="avance" valeur="0"/>
		<zonetext id="explication" titre="Explication"></zonetext>
		<bouton titre="Ajouter" id="ok_expli" />
	</formulaire>
	<formulaire id="ajout_champ" titre="Rajoute une question de type 'champ'" action="proposition/sondage.php">
		<hidden id="contenu_form" valeur="<?php echo $contenu_form; ?>"/> 	
		<hidden id="titre_sondage" valeur="<?php echo $titre_sondage; ?>"/>
		<hidden id="perimdate" valeur="<?php echo $perimdate; ?>"/>
		<hidden id="restriction" valeur="<?php echo $restriction; ?>"/>
		<hidden id="avance" valeur="0"/>
		<champ id="question" titre="Question" valeur=""/>
		<bouton titre="Ajouter" id="ok_champ" />
	</formulaire>
	<formulaire id="ajout_champ" titre="Rajoute une question de type 'textarea'" action="proposition/sondage.php">	
		<hidden id="contenu_form" valeur="<?php echo $contenu_form; ?>"/> 	
		<hidden id="titre_sondage" valeur="<?php echo $titre_sondage; ?>"/>
		<hidden id="perimdate" valeur="<?php echo $perimdate; ?>"/>
		<hidden id="restriction" valeur="<?php echo $restriction; ?>"/>
		<hidden id="avance" valeur="0"/>
		<champ id="question" titre="Question" valeur=""/>
		<bouton titre="Ajouter" id="ok_text" />
	</formulaire>
	<formulaire id="ajout_champ" titre="Rajoute une question de type 'radio'" action="proposition/sondage.php">	
		<hidden id="contenu_form" valeur="<?php echo $contenu_form; ?>"/> 	
		<hidden id="titre_sondage" valeur="<?php echo $titre_sondage; ?>"/>
		<hidden id="perimdate" valeur="<?php echo $perimdate; ?>"/>
		<hidden id="restriction" valeur="<?php echo $restriction; ?>"/>
		<hidden id="avance" valeur="0"/>
		<champ id="question" titre="Question" valeur=""/>
		<textsimple titre="Maintenant rajouter les réponses possibles"/>
		<champ id="reponse1" titre="Reponse 1" valeur=""/>
		<champ id="reponse2" titre="Reponse 2" valeur=""/>
		<champ id="reponse3" titre="Reponse 3" valeur=""/>
		<champ id="reponse4" titre="Reponse 4" valeur=""/>
		<champ id="reponse5" titre="Reponse 5" valeur=""/>
		<champ id="reponse6" titre="Reponse 6" valeur=""/>
		<bouton titre="Ajouter" id="ok_radio" />
	</formulaire>
	<formulaire id="ajout_champ" titre="Rajoute une question de type 'checkbox'" action="proposition/sondage.php">	
		<hidden id="contenu_form" valeur="<?php echo $contenu_form; ?>"/> 	
		<hidden id="titre_sondage" valeur="<?php echo $titre_sondage; ?>"/>
		<hidden id="perimdate" valeur="<?php echo $perimdate; ?>"/>
		<hidden id="restriction" valeur="<?php echo $restriction; ?>"/>
		<hidden id="avance" valeur="0"/>
		<champ id="question" titre="Question" valeur=""/>
		<textsimple titre="Maintenant rajouter les réponses possibles"/>
		<champ id="reponse1" titre="Reponse 1" valeur=""/>
		<champ id="reponse2" titre="Reponse 2" valeur=""/>
		<champ id="reponse3" titre="Reponse 3" valeur=""/>
		<champ id="reponse4" titre="Reponse 4" valeur=""/>
		<champ id="reponse5" titre="Reponse 5" valeur=""/>
		<champ id="reponse6" titre="Reponse 6" valeur=""/>
		<bouton titre="Ajouter" id="ok_check" />
	</formulaire>
	<formulaire id="ajout_champ" titre="Rajoute une question de type 'liste déroulante'" action="proposition/sondage.php">	
		<hidden id="contenu_form" valeur="<?php echo $contenu_form; ?>"/> 	
		<hidden id="titre_sondage" valeur="<?php echo $titre_sondage; ?>"/>
		<hidden id="perimdate" valeur="<?php echo $perimdate; ?>"/>
		<hidden id="restriction" valeur="<?php echo $restriction; ?>"/>
		<hidden id="avance" valeur="0"/>
		<champ id="question" titre="Question" valeur=""/>
		<textsimple titre="Maintenant rajouter les réponses possibles"/>
		<champ id="reponse1" titre="Reponse 1" valeur=""/>
		<champ id="reponse2" titre="Reponse 2" valeur=""/>
		<champ id="reponse3" titre="Reponse 3" valeur=""/>
		<champ id="reponse4" titre="Reponse 4" valeur=""/>
		<champ id="reponse5" titre="Reponse 5" valeur=""/>
		<champ id="reponse6" titre="Reponse 6" valeur=""/>
		<bouton titre="Ajouter" id="ok_combo" />
	</formulaire>
<?php
	}
}
?>

</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
