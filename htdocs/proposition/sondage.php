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
	Page qui permet aux utilisateurs de demander le rajout d'une activité
	
	$Log$
	Revision 1.1  2004/11/16 15:35:27  kikx
	Pour les sondages

	Revision 1.7  2004/10/29 14:09:10  kikx
	Envoie des mail en HTML pour la validation des affiche
	
	Revision 1.6  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.5  2004/10/19 14:58:43  schmurtz
	Creation d'un champ de formulaire specifique pour les fichiers (sans passer
	l'element champ, qui actuellement est un peu acrobatique).
	
	Revision 1.4  2004/10/10 21:40:49  kikx
	Pour permettre aux eleves de demander à mettre une activité visible de l'exterieur
	
	Revision 1.3  2004/10/07 22:52:20  kikx
	Correction de la page des activites (modules + proposition + administration)
		rajout de variables globales : DATA_DIR_LOCAL
						DATA_DIR_URL
	
	Comme ca si ca change, on est safe :)
	
	Revision 1.2  2004/10/04 21:48:54  kikx
	Modification du champs fichier pour uploader des fichiers
	
	Revision 1.1  2004/09/20 22:31:28  kikx
	oubli
	

	
*/

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);

$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['user']->uid."'");
list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();

$msg="" ;
//---------------------------------------------------------------------------------
// Differents traitement
//---------------------------------------------------------------------------------


//=================
//===============
// Génération de la page
//===============
//=================

require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="propoz_sondage" titre="Frankiz : Propose un sondage">
<h1>Proposition de sondage</h1>



</page>
<?php

require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
