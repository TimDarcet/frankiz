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
	Liens permettants de contacter les webmestres et faire des demandes.
	
	$Log$
	Revision 1.16  2004/11/06 20:10:53  kikx
	Id des liens pour le module contact ...

	Revision 1.15  2004/10/31 18:20:24  kikx
	Rajout d'une page pour les plan (venir à l'X)
	
	Revision 1.14  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.13  2004/10/20 22:19:08  kikx
	Une belle page de contact :)
	
	Revision 1.12  2004/10/20 21:09:06  kikx
	Pendant que j'y pense
	
	Revision 1.11  2004/10/19 20:19:31  kikx
	pas de sondage pour l'instant
	
	Revision 1.10  2004/10/13 20:03:59  pico
	Ajout du lien
	
	Revision 1.9  2004/10/06 14:12:27  kikx
	Page de mail promo quasiment en place ...
	envoie en HTML ...
	Page pas tout a fait fonctionnel pour l'instant
	
	Revision 1.8  2004/09/20 22:19:28  kikx
	test
	
	Revision 1.7  2004/09/20 08:53:48  kikx
	Schmurtz tu fais chié :)
	
	Revision 1.6  2004/09/20 08:29:24  kikx
	Rajout d'une page pour envoyer des mail d'amour a ses webmestres adorés
	
	Revision 1.5  2004/09/17 09:05:32  kikx
	La personne peut maintenant rajouter une annonce
	Ceci dit je ne comprend pas trop comment on protège les champs avec les <!CDATA
	-> j'ai laisser ca comme ca mais faudra modifier
	
	Revision 1.4  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.3  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
?>
<module id="liens_contacts" titre="Utiles">
	<lien id="utile_contact" titre="Contact" url="contact.php" />
	<?php if(est_authentifie(AUTH_MINIMUM)): ?>
		<lien id="utile_annonce" titre="Proposer une annonce" url="proposition/annonce.php" />
		<lien id="utile_activite" titre="Proposer une activité" url="proposition/affiche.php" />
		<lien id="utile_qdj" titre="Proposer une qdj" url="proposition/qdj.php" />
		<lien id="utile_mailpromo" titre="Demander un mail promo" url="proposition/mail_promo.php" />
		<!--<lien titre="Proposer un sondage" url="proposition/sondage.php/" />-->
	<?php endif; ?>
	<?php if(!est_authentifie(AUTH_MINIMUM)): ?>
		<lien id="utile_plan" titre="Venir à l'X" url="plan.php" />
	<?php endif; ?>
	<lien id="utile_liens" titre="Liens utiles" url="liens.php" />
</module>
