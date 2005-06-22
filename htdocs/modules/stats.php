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
	Génération des données de statistiques des serveurs.
	
	Certaines données sont mise en statique pour exemple. Il serait bien de réécrir les scripts
	qui récupère ces informations pour qu'ils enregistrent leurs données dans des fichiers directement
	en XML, et non sous forme d'une suite de "0" et de "1".
	
	TODO : limiter à l'état des services (up/down), les pluparts des élèves s'en fout complètement
	Par ailleurs, il faudra créer des pages de stats assez complètes pour les admins (avec les usages
	de bande passante, de cpu). 
	
	$Id$

*/
if(est_authentifie(AUTH_MINIMUM)) { ?>
	<module id="stats" titre="Statistiques">
		<statistiques>
			<service nom="web frankiz" stat="webalizer/" />
			<service nom="web perso" stat="http://perso.frankiz.eleves.polytechnique.fr/webalizer/" />
			<service nom="web binets" stat="http://binets.frankiz.eleves.polytechnique.fr/webalizer/" />
			<service nom="web binets (old)" stat="http://gwennoz.polytechnique.fr/webalizer/" />
			<service nom="news" stat="stats/news.php" />
			<service nom="xnet" stat="stats/xnet.php" />
		<? if(file_exists(BASE_CACHE."uptime")) include BASE_CACHE."uptime"; ?>
		<? if(file_exists(BASE_CACHE."status")) include BASE_CACHE."status"; else echo "<serveur nom='status' etat='down'/>\n"?>
		
		</statistiques>
	</module>
<?php } ?>
