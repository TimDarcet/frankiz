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
	Page pur demander les sondages !
	
	$Log$
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
//---------------------------------------------------------------------------------
// Differents traitement
//---------------------------------------------------------------------------------
if (isset($_REQUEST['contenu_form']))
	$contenu_form=$_REQUEST['contenu_form'] ;
else
	$contenu_form="" ;
	
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
//---------------------------------------------------------------------------------
// Fonction de décodage du sondage
//---------------------------------------------------------------------------------
function decode_sondage($string) {
	$string = explode("###",$string) ;
	for ($i=1 ; $i<count($string) ; $i++) {
		$temp = explode("///",$string[$i]) ;
		if ($temp[0]=="expli") {
			echo "<note>$temp[1]</note>" ;
		}
		if ($temp[0]=="champ") {
			echo "<champ id=\"$i\" titre=\"$temp[1]\" valeur=\"\"/>" ;
		}
		if ($temp[0]=="text") {
			echo "<zonetext id=\"$i\" titre=\"$temp[1]\" valeur=\"\"/>" ;
		}
		if ($temp[0]=="radio") {
			echo "<choix titre=\"$temp[1]\" id=\"$i\" type=\"radio\" valeur=\"\">" ;
			for ($j=2 ; $j<count($temp) ; $j++) {
				echo "<option titre=\"".$temp[$j]."\" id=\"$j\"/>";
			}	
			echo "</choix>" ;
		}
		if ($temp[0]=="combo") {
			echo "<choix titre=\"$temp[1]\" id=\"$i\" type=\"combo\" valeur=\"\">" ;
			for ($j=2 ; $j<count($temp) ; $j++) {
				echo "<option titre=\"".$temp[$j]."\" id=\"$j\"/>";
			}	
			echo "</choix>" ;
		}
		if ($temp[0]=="check") {
			echo "<choix titre=\"$temp[1]\" id=\"$i\" type=\"checkbox\" valeur=\"\">" ;
			for ($j=2 ; $j<count($temp) ; $j++) {
				echo "<option titre=\"".$temp[$j]."\" id=\"$j\"/>";
			}	
			echo "</choix>" ;
		}
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

<formulaire id="form" titre="Votre formulaire">	
<?
	decode_sondage($contenu_form) ;
?>
</formulaire>



<formulaire id="ajout_simple" titre="Rajoute une explication" action="proposition/sondage.php">
	<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
	<zonetext id="explication" titre="Explication" valeur=""/>
	<bouton titre="Ajouter" id="ok_expli" />
</formulaire>
<formulaire id="ajout_champ" titre="Rajoute une question de type 'champ'" action="proposition/sondage.php">
	<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
	<champ id="question" titre="Question" valeur=""/>
	<bouton titre="Ajouter" id="ok_champ" />
</formulaire>
<formulaire id="ajout_champ" titre="Rajoute une question de type 'textarea'" action="proposition/sondage.php">	
	<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
	<champ id="question" titre="Question" valeur=""/>
	<bouton titre="Ajouter" id="ok_text" />
</formulaire>
<formulaire id="ajout_champ" titre="Rajoute une question de type 'radio'" action="proposition/sondage.php">	
	<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
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
	<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
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
	<hidden id="contenu_form" valeur="<?=$contenu_form?>"/> 	
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


</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
