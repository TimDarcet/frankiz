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
	
	$Log$
	Revision 1.6  2004/11/06 20:03:06  kikx
	Suppression de liens inutiles

	Revision 1.5  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.4  2004/10/19 22:14:49  pico
	Suppression du lien corrige ton poly
	Redirection des mails que si authentifié
	
	Revision 1.3  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.2  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
?>
<module id="liens_ecole" titre="Liens école">
	<lien titre="La Kes" url="http://www.polytechnique.fr/eleves/binets/kes/" />
	<?php if(est_authentifie(AUTH_MINIMUM)){ ?>
		<lien titre="Redirection des mails" url="http://poly.polytechnique.fr/" /> 
	<? } ?>
	<lien titre="Site de l'école" url="http://www.polytechnique.fr/" />
	<lien titre="Site de la DE" url="http://www.edu.polytechnique.fr/" />
	<lien titre="Intranet" url="http://intranet.polytechnique.fr/" />
	<lien titre="Polytechnique.org" url="http://www.polytechnique.org/" />
</module>
