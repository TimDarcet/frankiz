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
	Page des contacts utiles
	
	$Log$
	Revision 1.4  2004/10/31 18:20:24  kikx
	Rajout d'une page pour les plan (venir à l'X)

	Revision 1.3  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.2  2004/10/21 12:24:48  kikx
	Correction d'un bug suite a un commit
	
	Revision 1.1  2004/10/20 22:19:08  kikx
	Une belle page de contact :)
*/

require_once "include/global.inc.php";


// génération de la page
require "include/page_header.inc.php";
?>
<page id='contact' titre='Frankiz : contact'>
<h1>Contacts utiles</h1>
<cadre titre="Contacter la KES">
	<html>
<?

$text = "<p>La \"Kes\" est le Bureau des Eleves communément appelé \"Bde\" dans les autres Grandes Ecoles. Elle est en charge pendant un an de la vie des élèves<p>" ;
$text .= "Si vous désirez des <a href='mailto:".MAIL_CONTACT."?subject=Kes : Cours Particuliers'>cours particuliers</a> donnés par un élève de l'Ecole<br>" ;
$text .= "Si vous désirez des <a href='mailto:".MAIL_CONTACT."?subject=Kes : Informations diverses'>informations</a> sur polytechnique et les élèves" ;
echo htmlspecialchars($text) ;
?>
	</html>
</cadre>

<cadre titre="Contacter un élève">
	<html>
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
	</html>
</cadre>

<cadre titre="Contacter le Webmestre">
	<html>

<?
$text = "<p>Car tu as un problème avec le site, des suggestions, des questions ... N'hésite pas !<p>" ;
$text .= "<a href='mailto:".MAIL_WEBMESTRE."?subject=Webmestre'>Clique ici</a>" ;
echo htmlspecialchars($text) ;
?>
	</html>
</cadre>

</page>
<?
require_once "include/page_footer.inc.php";
?>