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
	Plan pour venir � l'X
	
	$Log$
	Revision 1.1  2004/10/31 18:20:24  kikx
	Rajout d'une page pour les plan (venir � l'X)


*/

require_once "include/global.inc.php";

// g�n�ration de la page
require "include/page_header.inc.php";
?>
<page id='plan' titre='Frankiz : plan'>
<h1>Pour venir � l'X</h1>
<p>L'�cole Polytechnique est � Palaiseau (ou plus pr�cisement sur le plateau de Saclay) � 15 km au sud de Paris</p>
<p>&nbsp;</p>
<cadre titre="En RER">
	<html>
<?

$text = "<p>De Paris, prendre la ligne RER B vers le sud en direction de Saint-Remy-les-Chevreuse. (En gros le trajet dure 30min � partir de Denfert-Rochereau).<p>" ;
$text .= "<p>En descendant du RER vous vous situerez du mauvais c�t� de la voie ... Traverser la voie puis ayant la voie dans le dos, prenez � gauche un petit chemin qui monte... C'est le chemin de Loz�re : L'X est en haut de la pente, alors courage!</p>" ;
echo htmlspecialchars($text) ;
?>
	</html>
</cadre>

<cadre titre="En voiture">
	<html>
<?

$text = "<strong><u>1er m�thode : par l'A6</u></strong>" ;
$text .= "<p>Sur le p�riph�rique ext�rieur, juste apr�s la porte d'orl�ans, vous avez une sortie vers l'A6
5km plus loin, il y aura une s�paration d'autoroute : Suivez l'A6a/E50 en direction d'Orl�ans/Palaiseau (9.7km).
Ensuite direction A10 en suivant Palaiseau (6.1km).</p>" ;
$text .= "<p>Au panneau \"Cit� Scientifique\" vous quittez l'A10 et vous vous engagez sur la N444 (1.2km).
Vous bifurquez sur la gauche vers Saclay sur la D36 (1.6km).
En haut de la mont�e vous arriverez � un tr�s grand sens giratoire: L'X se situera � votre gauche donc tournez autour et �a sera indiqu� !</p>" ;
$text .= "<br><strong><u>2�me m�thode : par la N118</u></strong>" ;
$text .= "<p>A la porte de saint Cloud, sortez du boulevard p�riph�rique et prenez l'Avenue de la Porte de Saint-Cloud sur 300 m.
Prenez la N10 (2.2km) via Boulogne-Billancourt.</p>" ;
$text .= "<p>Enfin prenez la N118 en direction de Bi�vres - Bordeaux - Chartres - Nantes - Orl�ans pendant 13km environ.
Vous allez sortir � la route de bi�vre et ensuite la D36 en suivant Polytechnique.</p>" ;

echo htmlspecialchars($text) ;
?>
	</html>
</cadre>

</page>
<?
require_once "include/page_footer.inc.php";
?>