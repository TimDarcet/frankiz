<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Rajout d'une page pour les plan (venir � l'X)

	Revision 1.3  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.2  2004/10/21 12:24:48  kikx
	Correction d'un bug suite a un commit
	
	Revision 1.1  2004/10/20 22:19:08  kikx
	Une belle page de contact :)
*/

require_once "include/global.inc.php";


// g�n�ration de la page
require "include/page_header.inc.php";
?>
<page id='contact' titre='Frankiz : contact'>
<h1>Contacts utiles</h1>
<cadre titre="Contacter la KES">
	<html>
<?

$text = "<p>La \"Kes\" est le Bureau des Eleves commun�ment appel� \"Bde\" dans les autres Grandes Ecoles. Elle est en charge pendant un an de la vie des �l�ves<p>" ;
$text .= "Si vous d�sirez des <a href='mailto:".MAIL_CONTACT."?subject=Kes : Cours Particuliers'>cours particuliers</a> donn�s par un �l�ve de l'Ecole<br>" ;
$text .= "Si vous d�sirez des <a href='mailto:".MAIL_CONTACT."?subject=Kes : Informations diverses'>informations</a> sur polytechnique et les �l�ves" ;
echo htmlspecialchars($text) ;
?>
	</html>
</cadre>

<cadre titre="Contacter un �l�ve">
	<html>
<?
$text = "<h3>Par email (ou mel)</h3>" ;
$text .= "Si tu veux joindre un �l�ve, rien de plus facile:<br>" ;
$text .= "<b>pr�nom.nom@polytechnique.fr</b>" ;
$text .= "(O�, bien s�r, on remplace le nom et le pr�nom de l'�l�ve dans cette adresse :op)" ;
$text .= "<h3>Par la poste</h3>" ;
$text .= "<p>Qui a dit que ce moyen de communication �tait d�mod� ????!!!!</p>" ;
$text .= "Bon voil� la typographie type (car sinon la lettre risque de ne jamais arriver)<br>" ;
$text .= "<p style=\"text-align: center\"><b>Pr�nom Nom</b><br>" ;
$text .= "<b>Promotion X(1) / (2) Cie</b><br>" ;
$text .= "<b>Ecole Polytechnique</b><br>" ;
$text .= "<b>91128 Palaiseau Cedex</b></p>" ;
$text .= "<p>Donc 2 choses importantes :</p>" ;
$text .= "<ul><li>(1) est remplac� par la Promotion de l'�l�ve (ann�e d'int�gration)</li>" ;
$text .= "<li>(2) est remplac� par le num�ro de sa compagnie (ben... �a faut lui demander !)</li></ul>" ;

echo htmlspecialchars($text) ;
?>
	</html>
</cadre>

<cadre titre="Contacter le Webmestre">
	<html>

<?
$text = "<p>Car tu as un probl�me avec le site, des suggestions, des questions ... N'h�site pas !<p>" ;
$text .= "<a href='mailto:".MAIL_WEBMESTRE."?subject=Webmestre'>Clique ici</a>" ;
echo htmlspecialchars($text) ;
?>
	</html>
</cadre>

</page>
<?
require_once "include/page_footer.inc.php";
?>