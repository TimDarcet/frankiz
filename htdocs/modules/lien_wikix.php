<?php
/*
	Copyright (C) 2006 Binet Réseau
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
	Affichage d'un champs de recherche pour le wikix
	
	$Id$

*/

if (est_authentifie(AUTH_INTERNE)) {
?>
<module id="lien_wikix" titre="WikiX">
<? if (est_interne()) { ?>
        <formulaire id="lien_wiki_x" action="http://frankiz.polytechnique.fr/eleves/wikix/Special:Search" type="discret">
<? } else { ?>
	<formulaire id="lien_wiki_x" action="http://www.polytechnique.fr/eleves/wikix/Special:Search" type="discret">
<? } ?>
		<hidden id="go" valeur="Consulter" />
		<champ titre="Rechercher" id="search" valeur="" />
		<bouton titre="Chercher" id="ok" />
	</formulaire>
</module>
<?php
}
?>
