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
	Revision 1.1  2005/01/12 22:40:39  pico
	Ajout des fêtes à souhaiter

	Revision 1.16  2005/01/02 10:50:25  pico
	Passage de certaines pages en visibles de l'intérieur (non loggué)
	
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

if(est_authentifie(AUTH_INTERNE)) {
	echo "<module id=\"fetes\" titre=\"Fête du jour\">\n";

	if(!cache_recuperer('fetes',strtotime(date("Y-m-d",time())))) {
		$DB_trombino->query("SELECT prenom FROM fetes WHERE MONTH(date)=MONTH(NOW()) AND DAYOFMONTH(date)=DAYOFMONTH(NOW()) ");
		while(list($prenom) = $DB_trombino->next_row())
			echo "\t<eleve prenom='$prenom'/>\n";
		
		cache_sauver('fetes');
	}

	echo "</module>\n";
}
