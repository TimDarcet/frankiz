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
	Liens permettants d'acc�der aux autres sites de l'�cole.
	
	$Log$
	Revision 1.10  2005/02/03 09:28:59  kikx
	erreur (deslo�)

	Revision 1.8  2005/01/26 17:26:27  pico
	Pas de liens morts � l'ext�rieur...
	
	Revision 1.7  2005/01/17 23:46:28  pico
	Bug fix
	
	Revision 1.6  2005/01/10 07:43:05  pico
	Bug #20
	
	Revision 1.5  2005/01/02 10:50:25  pico
	Passage de certaines pages en visibles de l'int�rieur (non loggu�)
	
	Revision 1.4  2004/11/09 22:39:06  pico
	Ajout des accesskeys dans les liens de navigation
	
	Revision 1.3  2004/11/08 08:47:57  kikx
	Pour la gestion online des sites de binets
	
	Revision 1.2  2004/11/06 20:57:13  kikx
	correction pour etre plus clair
	
	Revision 1.1  2004/11/06 20:52:08  kikx
	Reordonnancement des liens
	
	Revision 1.7  2004/11/06 20:07:01  kikx
	Id des liens pour les liens ecole
	
	Revision 1.6  2004/11/06 20:03:06  kikx
	Suppression de liens inutiles
	
	Revision 1.5  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.4  2004/10/19 22:14:49  pico
	Suppression du lien corrige ton poly
	Redirection des mails que si authentifi�
	
	Revision 1.3  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.2  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
$DB_trombino->query("SELECT promo FROM eleves WHERE eleve_id='{$_SESSION['user']->uid}'");
list($promo) = $DB_trombino->next_row();
?>
<module id="liens_ecole" titre="Liens utiles">
	
	<lien id="utile_contact" titre="Contacter les �l�ves" url="contact.php" key="c"/>
	<?php if(!est_authentifie(AUTH_INTERNE)): ?>
		<lien id="utile_plan" titre="Venir � l'X" url="plan.php" />
	<?php endif; ?>
	<lien id="utile_liens" titre="Liens utiles" url="liens.php" />
	<?php if(est_authentifie(AUTH_MINIMUM)){ ?>
		<lien id="emploi_temps" titre="Emploi du temps" url="http://de.polytechnique.fr/scolarite/emploi_du_temps/X<?=$promo?>/index.html"/>
		<lien id="utile_licence" titre="Licences Msdnaa" url="profil/licences.php"/>
		<lien id="utile_redmail" titre="Redirection des mails" url="http://poly.polytechnique.fr/" /> 
	<? } ?>
	<?php if(est_authentifie(AUTH_INTERNE)): ?><lien id="utile_irc" titre="Acc�der � l'IRC" url="http://ircserver/"/><?php endif; ?>
	<lien id="utile_ecole" titre="Site de l'�cole" url="http://www.polytechnique.fr/" />
	<lien id="utile_ecole_de" titre="Site de la DE" url="http://www.edu.polytechnique.fr/" key="d"/>
	<?php if(est_authentifie(AUTH_INTERNE)): ?><lien id="utile_intranet" titre="Intranet" url="http://intranet.polytechnique.fr/" key="i"/><?php endif; ?>
	<lien id="utile_xorg" titre="Polytechnique.org" url="http://www.polytechnique.org/" key="o"/>
</module>
