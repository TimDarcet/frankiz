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
	Page permettant de modifier son profil 
	
	$Log$
	Revision 1.3  2004/12/13 20:03:25  pico
	Les liens ne forment pas de blocs, il faut donc le spécifier

	Revision 1.2  2004/11/27 17:10:37  pico
	Hum.. commentaires qui vont pas bien
	
	Revision 1.1  2004/11/27 17:09:02  pico
	Page de choix des prefs à modifier
	
	
*/

require_once "../include/global.inc.php";
demande_authentification(AUTH_MAIL);

// Génération du la page XML
require "../include/page_header.inc.php";
?>

<page id="profil" titre="Frankiz : modification des préférences">
	<h1>Modification de ses préférences</h1>

	<h2>Changer mon profil sur le site</h2>
		<lien titre="Modifier mon compte frankiz" url="profil/profil.php"/><br/>
		<lien titre="Modifier ma fiche trombino" url="profil/profil.php#mod_trombino"/><br/>
		<lien titre="Modifier mon site perso" url="profil/siteweb.php"/><br/>
		
	<h2>Changer l'apparence du site</h2>
		<lien titre="Changer de skin" url="profil/skin.php"/><br/>
		<lien titre="Gérer mes liens perso" url="profil/liens_ext.php"/><br/>
		<lien titre="Gérer mes annonces externes" url="profil/liens_ext.php#form_rss"/><br/>
		
	<h2>Changer mon profil sur le réseau</h2>
		<lien titre="Gérer mes données réseau" url="profil/reseau.php"/><br/>
		<lien titre="Modifier le mot de passe Xnet" url="profil/reseau.php#mod_xnet_0"/><br/>
		<lien titre="Demander une nouvelle adresse ip" url="profil/demande_ip.php"/><br/>
		
		
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php"; ?>