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
	Affichage d'un lien sur la page d'accueil vers le tol.
	
	$Log$
	Revision 1.5  2005/01/13 17:10:58  pico
	Mails de validations From le validateur qui va plus ou moins bien

	Revision 1.4  2005/01/12 20:58:08  pico
	Recherche trombi par appui sur enter
	
	Revision 1.3  2005/01/02 10:50:25  pico
	Passage de certaines pages en visibles de l'intérieur (non loggué)
	
	Revision 1.2  2004/11/24 13:05:23  schmurtz
	Ajout d'un attribut type='discret' pour les liste et formulaire, afin d'avoir
	une presentation par defaut sans gros cadres autour.
	
*/

if(est_authentifie(AUTH_INTERNE)) {
?>
<module id="lien_tol" titre="Tol">
	<formulaire id="lien_trombino" action="trombino.php" type="discret">
		 <hidden id="cherchertol" valeur="ok" />
		 <champ titre="Rechercher" id="q_search" valeur="" />
		<bouton titre="Chercher" id="ok" />
	</formulaire>
</module>
<? } ?>
