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
	
	$Log$
	Revision 1.20  2005/06/22 12:51:08  pico
	Pour afficher les uptimes

	Revision 1.19  2005/04/13 17:10:00  pico
	Passage de tous les fichiers en utf8.
	
	Revision 1.18  2005/04/13 16:13:36  pico
	Hum...
	
	Revision 1.17  2005/04/11 20:13:54  fruneau
	Le retour des stats news sur frankiz
	
	Revision 1.16  2005/01/24 09:18:35  pico
	Lien vers les stats
	
	Revision 1.15  2005/01/04 12:28:06  pico
	Plus de stats
	
	Revision 1.14  2005/01/04 12:09:18  pico
	Retour des stats de frankiz !
	
	Revision 1.13  2005/01/03 23:27:05  pico
	Petites modifs
	
	Revision 1.12  2004/12/17 01:54:58  pico
	En attendant
	
	Revision 1.11  2004/12/17 01:52:08  pico
	On enlève les liens morts !
	
	Revision 1.10  2004/12/15 16:50:48  schmurtz
	Bug fix+info d'installation, maj de la version en prod du site
	
	Revision 1.9  2004/11/11 20:15:19  kikx
	Deplacemeent du fichier des binets pour que ca erste logique
	
	Revision 1.8  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.7  2004/10/19 13:08:30  pico
	Enlève un warning
	
	Revision 1.6  2004/10/19 12:34:15  pico
	Inclusion de l'état des serveurs d'après la sortie du script
	
	Revision 1.5  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.4  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
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
		<? if(file_exists(BASE_CACHE."status")) include BASE_CACHE."status"; else echo "<serveur nom='status' etat='down'/>\n"?>
		<? if(file_exists(BASE_CACHE."uptime")) include BASE_CACHE."uptime"; ?>
		</statistiques>
	</module>
<?php } ?>
