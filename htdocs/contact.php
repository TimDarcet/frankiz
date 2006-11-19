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
	
	$Id$

*/

require_once "include/global.inc.php";


// génération de la page
require "include/page_header.inc.php";
?>
<page id='contact' titre='Frankiz : contact'>
<h1>Contacts utiles</h1>

<cadre titre="Contacter la Kès">
	<p>La "Kès" est le Bureau des Élèves communément appelé "BdE" dans les autres Grandes Écoles.
	Elle est en charge pendant un an de la vie des élèves.</p>
	<p>Si vous désirez des <a href="mailto:<?=MAIL_CONTACT?>?subject=K%E8s%20:%20Cours%20Particuliers">cours particuliers</a> donnés par un élève de l'École.</p>
	<p>Si vous désirez des <a href="mailto:<?=MAIL_CONTACT?>?subject=K%E8s%20:%20Informations%20diverses">informations</a> sur polytechnique et les élèves.</p>
</cadre>

<cadre titre="Téléphone">
	<lien url="num_utiles.php" titre="Numéros Utiles"/>
</cadre>

<cadre titre="Contacter un élève">
	<h3>Par email (ou mel)</h3>
	<p>Si tu veux joindre un élève, rien de plus facile :</p>
	<p><strong>prénom.nom@polytechnique.edu</strong> (Où, bien sûr, on remplace le nom et le prénom de l'élève dans cette adresse :op)</p>

	<h3>Par la poste</h3>
	<p>Qui a dit que ce moyen de communication était démodé ????!!!!</p>
	<p>Bon voilà la typographie type (car sinon la lettre risque de ne jamais arriver), selon le type de bâtiment concerné :</p>
	
	<h4>Bâtiments Joffre, Foch, Maunoury, Fayolle et PEM (promotion impaire)</h4>
<html><![CDATA[
	<p style="text-align: center"><strong>
		Prénom Nom<br />
		Promotion X(1)/(2) Cie<br />
		École Polytechnique<br />
		91128 PALAISEAU Cedex
	</strong></p>
]]></html>
	<p>Sachant que :</p>
<html><![CDATA[
	<ul>
		<li>(1) est remplacé par la Promotion de l'élève (année d'intégration)</li>
		<li>(2) est remplacé par le numéro de sa compagnie</li>
	</ul>
]]></html>

	<h4>Bâtiments 70 à 80 (promotion paire)</h4>
<html><![CDATA[
	<p style="text-align: center"><strong>
		Prénom Nom<br />
		Bât. (1) App. (2)<br />
		Résidence (3)<br />
		91120 PALAISEAU
	</strong></p>
]]></html>
	<p>Sachant que :</p>
<html><![CDATA[
	<ul>
		<li>(1) est remplacé par le numéro de bâtiment (entre 70 et 80)</li>
		<li>(2) est remplacé par le numéro d'appartement, composé de quatre chiffres</li>
		<li>(3) est remplacé par le nom de résidence : Lemonnier pour les bâtiments 70 à 75, Schaeffer pour les bâtiments 76 à 80</li>
	</ul>
]]></html>

	<h4>Bâtiments Élèves Mariés (BEM)</h4>
<html><![CDATA[
	<p style="text-align: center"><strong>
		M. et Mme Untel<br />
		Bât. (1) App. (2)<br />
		Place André Citroën<br />
		91120 PALAISEAU
	</strong></p>
]]></html>
	<p>Sachant que :</p>
<html><![CDATA[
	<ul>
		<li>(1) est remplacé par le numéro de bâtiment</li>
		<li>(2) est remplacé par le numéro d'appartement</li>
	</ul>
]]></html>
</cadre>

<cadre titre="Contacter le Webmestre">
	<p>Car tu as un problème avec le site, des suggestions, des questions... N'hésite pas !
	<a href="mailto:<?=MAIL_WEBMESTRE?>?subject=Webmestre">Clique ici</a></p>
</cadre>

</page>
<?
require_once "include/page_footer.inc.php";
?>
