<?
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
	Affichage de flux rss externes.

	$Log$
	Revision 1.19  2005/01/02 22:14:33  pico
	Devrait fixer les pbs concernant les flux rss

	Revision 1.18  2004/11/24 22:56:18  schmurtz
	Inclusion de wiki.inc.php par les fichiers concerne uniquement et non de facon
	globale pour tous les fichiers.
	
	Revision 1.17  2004/11/24 16:37:09  pico
	Ajout des news externes en tant que module
	
	Revision 1.16  2004/11/24 16:24:09  pico
	Passage du formulaire de choix des rss à afficher dans une page spéciale
	
	Revision 1.15  2004/11/24 15:55:33  pico
	Code pour gérer les liens perso + les rss au lancement de la session
	
	Revision 1.14  2004/11/24 15:37:37  pico
	Lis et sauvegarde les infos de session depuis la sql
	
	Revision 1.13  2004/11/24 15:18:19  pico
	Mise en place des liens sur une base sql
	
	Revision 1.12  2004/11/24 13:45:24  pico
	Modifs skins pour le wiki et l'id de la page d'annonces
	
	Revision 1.11  2004/11/24 13:31:42  pico
	Modifs pages liens rss
	
	Revision 1.10  2004/11/23 21:17:41  pico
	Ne charge qu'au login ou à l'établissemnt de la session (ce code va buger, je fais juste un travail préparatoire)
	
	
*/


require_once "include/global.inc.php";
require_once "include/rss_func.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="rss" titre="Frankiz : News Externes">

<?

$liens = $_SESSION['rss'];
if(is_array($liens)){
	foreach($liens as $value => $mode){
		if($mode == 'complet' || $mode == 'sommaire') rss_xml($value,$mode);
	}
}
?>
</page>
<?
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>