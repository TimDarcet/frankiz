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
	Liens de navigation dans le site web.	
	
	$Log$
	Revision 1.21  2004/11/25 00:20:39  schmurtz
	Parce que faq ne prend pas d's dans ce cas.

	Revision 1.20  2004/11/25 00:10:31  schmurtz
	Suppression des dossiers ne contenant qu'un unique fichier index.php
	
	Revision 1.19  2004/11/23 07:17:16  pico
	Correction du 'key'
	
	Revision 1.18  2004/11/22 23:07:28  kikx
	Rajout de lines vers les pages perso
	
	Revision 1.17  2004/11/12 23:32:14  schmurtz
	oublie dans le deplacement du trombino
	
	Revision 1.16  2004/11/11 20:15:19  kikx
	Deplacemeent du fichier des binets pour que ca erste logique
	
	Revision 1.15  2004/11/09 22:39:06  pico
	Ajout des accesskeys dans les liens de navigation
	
	Revision 1.14  2004/11/06 15:17:58  kikx
	Suppression des liens vers l'InfoBR et les docs
		-> Telecharger+FAQ = InfoBR
		-> docs -> FAQ
	
	Revision 1.13  2004/10/31 22:14:11  kikx
	Vocabulaire de L'X
	
	Revision 1.12  2004/10/29 16:42:31  kikx
	Rajout des id sur les liens de navigation
	Ca pêrmet au skinneur soit de mettre en gras certain liens specifique soit de remplacer les liens par des images comme il le souhaite
	
	Revision 1.11  2004/10/26 17:52:07  kikx
	J'essaie de respecter la charte de weather.com mais c'est chaud car il demande le mettre leur nom en gras ... et je peux pas le faire avec la skin
	
	Revision 1.10  2004/10/25 19:41:58  kikx
	Rend clair la page d'accueil et les annonces
	
	Revision 1.9  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.8  2004/10/20 21:45:01  kikx
	Pour que ca soit propre
	
	Revision 1.7  2004/10/16 01:50:22  schmurtz
	Affichage des annonces publiques (exterieure) pour les personnes non authentifiees
	
	Revision 1.6  2004/10/07 22:52:20  kikx
	Correction de la page des activites (modules + proposition + administration)
		rajout de variables globales : DATA_DIR_LOCAL
						DATA_DIR_URL
	
	Comme ca si ca change, on est safe :)
	
	Revision 1.5  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.4  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
?>
<module id="liens_navigation" titre="Frankiz">
	<lien id="annonces" titre="Annonces" url="index.php" key="a"/>
	<?php if(est_authentifie(AUTH_MINIMUM)): ?>
		<lien id="deconnect" titre="Se déconnecter" url="index.php?logout=1" key="l"/>
		<lien id="profil"  titre="Profil" url="profil/profil.php" key="p"/>
		<lien id="profil_rezal" titre="Profil réseau" url="profil/reseau.php" key="r"/>
		<lien id="skins" titre="Skins" url="profil/skin.php" key="s"/>
		
	<?php else: ?>
		<lien id="connect" titre="Se connecter" url="login.php" key="l"/>			
	<?php endif; ?>
	<lien id="meteo" titre="Météo" url="meteo.php" key="m"/>
	<!--<lien id="infobr" titre="InfoBr" url="documentation/InfoBR.pdf" />
	<lien id="doc" titre="Docs/Manuels" url="documentation/" />-->
	<lien id="faq" titre="FAQ" url="faq.php" key="f"/>
	<lien id="xshare" titre="XShare" url="xshare.php" key="x"/>
	<lien id="binets"  titre="Binets" url="binets.php" key="b"/>
	<?php if(est_authentifie(AUTH_MINIMUM)): ?>
		<lien id="trombino" titre="Trombino" url="trombino.php" key="t"/>
	<?php endif; ?>
	<?php if(!empty($_SESSION['user']->perms)): ?>
		<lien id="admin" titre="Administration" url="gestion/" key="g"/>
	<?php endif; ?>
	<lien id="siteseleves" titre="Sites Eleves" url="siteseleves.php"/>	
	<lien id="vocab" titre="Vocabulaire" url="vocabulaire.php" key="v"/>			

</module>
