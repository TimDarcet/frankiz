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
	Affichage des anniversaires avec gestion d'un cache mis à jour une fois par jour.
	
	$Log$
	Revision 1.15  2004/12/15 20:57:59  pico
	Affiche un lien vers la fiche trombi pour les anniversaires...

	Revision 1.14  2004/11/23 07:43:25  pico
	Les différentes promos ne sont plus codées en dur, mais utilisent la variable sql
	
	Revision 1.13  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.12  2004/09/20 20:33:47  schmurtz
	Mise en place d'un systeme de cache propre
	
	Revision 1.11  2004/09/17 16:27:26  schmurtz
	Simplification de l'affichage des anniversaires et correction d'un bug d'affichage.
	
	Revision 1.10  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.9  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

if(est_authentifie(AUTH_MINIMUM)) {
	echo "<module id=\"anniversaires\" titre=\"Anniversaires\">\n";

	if(!cache_recuperer('anniversaires',strtotime(date("Y-m-d",time())))) {
		$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
		list($promo_temp) = $DB_web->next_row() ;
		$DB_trombino->query("SELECT nom,prenom,surnom,promo,mail,login FROM eleves "
							   ."WHERE MONTH(date_nais)=MONTH(NOW()) AND DAYOFMONTH(date_nais)=DAYOFMONTH(NOW()) "
							   ."AND (promo=$promo_temp OR promo=".($promo_temp -1).")");
		while(list($nom,$prenom,$surnom,$promo,$mail,$login) = $DB_trombino->next_row())
			echo "\t<eleve nom='$nom' prenom='$prenom' surnom='$surnom' promo='$promo' mail='$mail' login='$login'/>\n";
		
		cache_sauver('anniversaires');
	}

	echo "</module>\n";
}
