<?php
/*
	Page des contacts utiles
	
	$Log$
	Revision 1.1  2004/10/20 22:19:08  kikx
	Une belle page de contact :)


	
*/

require_once "include/global.inc.php";

function get_categorie($en_haut,$stamp,$perime) {
	if($en_haut==1) return "important";
	elseif($stamp > date("YmdHis",time()-12*3600)) return "nouveau";
	elseif($perime < date("YmdHis",time()+24*3600)) return "vieux";
	else return "reste";
}

// génération de la page
require "include/page_header.inc.php";
?>
<page id='contact' titre='Frankiz : contact'>
<h1>Contacts utiles</h1>
<cadre titre="Contacter la KES">
<?
$text = "<p>La \"Kes\" est le Bureau des Eleves communément appelé \"Bde\" dans les autres Grandes Ecoles. Elle est en charge pendant un an de la vie des élèves<p>" ;
$text .= "Si vous désirez des <a href='mailto:".MAIL_WEBMESTRE."?subject=Kes : Cours Particuliers'>cours particuliers</a> donnés par un élève de l'Ecole<br>" ;
$text .= "Si vous désirez des <a href='mailto:".MAIL_WEBMESTRE."?subject=Kes : Informations diverses'>informations</a> sur polytechnique et les élèves" ;
echo htmlspecialchars($text) ;
?>
</cadre>

<cadre titre="Contacter un élève">
<?
$text = "<h3>Par email (ou mel)</h3>" ;
$text .= "Si tu veux joindre un élève, rien de plus facile:<br>" ;
$text .= "<b>prénom.nom@polytechnique.fr</b>" ;
$text .= "(Où, bien sûr, on remplace le nom et le prénom de l'élève dans cette adresse :op)" ;
$text .= "<h3>Par la poste</h3>" ;
$text .= "<p>Qui a dit que ce moyen de communication était démodé ????!!!!</p>" ;
$text .= "Bon voilà la typographie type (car sinon la lettre risque de ne jamais arriver)<br>" ;
$text .= "<p style=\"text-align: center\"><b>Prénom Nom</b><br>" ;
$text .= "<b>Promotion X(1) / (2) Cie</b><br>" ;
$text .= "<b>Ecole Polytechnique</b><br>" ;
$text .= "<b>91128 Palaiseau Cedex</b></p>" ;
$text .= "<p>Donc 2 choses importantes :</p>" ;
$text .= "<ul><li>(1) est remplacé par la Promotion de l'élève (année d'intégration)</li>" ;
$text .= "<li>(2) est remplacé par le numéro de sa compagnie (ben... ça faut lui demander !)</li></ul>" ;

echo htmlspecialchars($text) ;
?>
</cadre>

<cadre titre="Contacter le Webmestre">
<?
$text = "<p>Car tu as un problème avec le site, des suggestions, des questions ... N'hésite pas !<p>" ;
$text .= "<a href='mailto:".MAIL_WEBMESTRE."?subject=Webmestre'>Clique ici</a>" ;
echo htmlspecialchars($text) ;
?>
</cadre>

</page>
<?
require_once "include/page_footer.inc.php";
?>