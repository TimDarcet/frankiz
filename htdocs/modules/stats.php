<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	G�n�ration des donn�es de statistiques des serveurs.
	
	Certaines donn�es sont mise en statique pour exemple. Il serait bien de r��crir les scripts
	qui r�cup�re ces informations pour qu'ils enregistrent leurs donn�es dans des fichiers directement
	en XML, et non sous forme d'une suite de "0" et de "1".
	
	TODO�: limiter � l'�tat des services (up/down), les pluparts des �l�ves s'en fout compl�tement
	Par ailleurs, il faudra cr�er des pages de stats assez compl�tes pour les admins (avec les usages
	de bande passante, de cpu). 
	
	$Log$
	Revision 1.11  2004/12/17 01:52:08  pico
	On enl�ve les liens morts !

	Revision 1.10  2004/12/15 16:50:48  schmurtz
	Bug fix+info d'installation, maj de la version en prod du site
	
	Revision 1.9  2004/11/11 20:15:19  kikx
	Deplacemeent du fichier des binets pour que ca erste logique
	
	Revision 1.8  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.7  2004/10/19 13:08:30  pico
	Enl�ve un warning
	
	Revision 1.6  2004/10/19 12:34:15  pico
	Inclusion de l'�tat des serveurs d'apr�s la sortie du script
	
	Revision 1.5  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.4  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
if(est_authentifie(AUTH_MINIMUM)) { ?>
	<module id="stats" titre="Statistiques">
		<statistiques>
			<!--<service nom="web frankiz" stat="http://frankiz.polytechnique.fr/webalizer/" />
			<service nom="web binets" stat="http://gwennoz.polytechnique.fr/webalizer/" />
			<service nom="news" stat="http://frankiz.polytechnique.fr/news/" />
			<service nom="xnet" stat="http://frankiz.polytechnique.fr/accueil/xnetstatquick.php" />-->
		<? if(file_exists(BASE_CACHE."status")) include BASE_CACHE."status"; else echo "<serveur nom='status' etat='down'/>\n"?>
		</statistiques>
	</module>
<?php } ?>
