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
	Revision 1.5  2004/11/04 16:36:42  schmurtz
	Modifications cosmetiques

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

<cadre titre="Contacter la Kès">
	<p>La "Kès" est le Bureau des Élèves communément appelé "BdE" dans les autres Grandes Écoles.
	Elle est en charge pendant un an de la vie des élèves.</p>
	<p>Si vous désirez des <a href="mailto:<?=MAIL_CONTACT?>?subject=K%E8s%20:%20Cours%20Particuliers">cours particuliers</a> donnés par un élève de l'École.</p>
	<p>Si vous désirez des <a href="mailto:<?=MAIL_CONTACT?>?subject=K%E8s%20:%20Informations%20diverses">informations</a> sur polytechnique et les élèves.</p>
</cadre>

<cadre titre="Contacter un élève">
	<h3>Par email (ou mel)</h3>
	<p>Si tu veux joindre un élève, rien de plus facile :</p>
	<p><strong>prénom.nom@polytechnique.fr</strong> (Où, bien sûr, on remplace le nom et le prénom de l'élève dans cette adresse :op)</p>

	<h3>Par la poste</h3>
	<p>Qui a dit que ce moyen de communication était démodé ????!!!!</p>
	<p>Bon voilà la typographie type (car sinon la lettre risque de ne jamais arriver) :</p>
<html><![CDATA[
	<p style="text-align: center"><strong>
		Prénom Nom<br />
		Promotion X(1)/(2) Cie<br />
		École Polytechnique<br />
		91128 Palaiseau Cedex
	</strong></p>
]]></html>
	<p>Dont 2 choses importantes :</p>
<html><![CDATA[
	<ul>
		<li>(1) est remplacé par la Promotion de l'élève (année d'intégration)</li>
		<li>(2) est remplacé par le numéro de sa compagnie (ben... ça faut lui demander !)</li>
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