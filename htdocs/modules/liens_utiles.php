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
	Liens permettants d'accéder aux autres sites de l'école.
	
	$Id$

*/
$DB_trombino->query("SELECT promo FROM eleves WHERE eleve_id='{$_SESSION['user']->uid}'");
list($promo) = $DB_trombino->next_row();
?>
<module id="liens_ecole" titre="Liens utiles">
	
	<lien id="utile_contact" titre="Contacter les élèves" url="contact.php" key="c"/>
	<?php if(!est_authentifie(AUTH_INTERNE)): ?>
		<lien id="utile_plan" titre="Venir à l'X" url="plan.php" />
	<?php endif; ?>
	<lien id="utile_liens" titre="Liens utiles" url="liens.php" />
	<?php if(est_authentifie(AUTH_MINIMUM)){ ?>
		<lien id="emploi_temps" titre="Emploi du temps" url="http://de.polytechnique.fr/index.php?page=edt"/>
		<lien id="utile_licence" titre="Licences Msdnaa" url="profil/licences.php"/>
		<lien id="utile_redmail" titre="Redirection des mails" url="http://poly.polytechnique.fr/" /> 
	<?php } ?>
	<?php if(est_authentifie(AUTH_INTERNE)): ?><lien id="utile_irc" titre="Accéder à l'IRC" url="http://ircserver.eleves.polytechnique.fr/"/><?php endif; ?>
	<lien id="utile_ecole" titre="Site de l'école" url="http://www.polytechnique.fr/" />
	<lien id="utile_ecole_de" titre="Site de la DE" url="http://www.edu.polytechnique.fr/" key="d"/>
	<?php if(est_authentifie(AUTH_INTERNE)): ?><lien id="utile_intranet" titre="Intranet" url="http://intranet.polytechnique.fr/" key="i"/><?php endif; ?>
	<lien id="utile_xorg" titre="Polytechnique.org" url="http://www.polytechnique.org/" key="o"/>
	<lien id="utile_net" titre="Polytechnique.net" url="http://www.polytechnique.net/" key="n"/>
	<?php if (est_interne() || est_authentifie(AUTH_MINIMUM)) { ?>
		<lien id="partenariats" titre="Partenariats" url="partenaires.php"/>
	<?php } ?>
	<lien id="anciennefaq" titre="Ancienne FAQ" url="faq.php"/>
</module>
