<?php
/*
	Liens permettants d'acc�der aux autres sites de l'�cole.
	
	$Log$
	Revision 1.4  2004/10/19 22:14:49  pico
	Suppression du lien corrige ton poly
	Redirection des mails que si authentifi�

	Revision 1.3  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.2  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
?>
<module id="liens_ecole" titre="Liens �cole">
	<lien titre="La Kes" url="http://binets.polytechnique.fr/kes/" />
	<lien titre="Aide aux binets" url="http://binets.polytechnique.fr/kes-binets/" />
	<lien titre="Le site des �l�ves" url="http://www.polytechnique.fr/eleves/" />
	<?php if(est_authentifie(AUTH_MINIMUM)){ ?><lien titre="Redirection des mails" url="http://poly.polytechnique.fr/" /> <? } ?>
	<lien titre="Site de l'�cole" url="http://www.polytechnique.fr/" />
	<lien titre="Site de la DE" url="http://www.edu.polytechnique.fr/" />
	<lien titre="Bureau des effectifs" url="http://intranet-effectifs.polytechnique.fr/" />
	<lien titre="Intranet" url="http://intranet.polytechnique.fr/" />
	<lien titre="Polytechnique.org" url="http://www.polytechnique.org/" />
	<lien titre="Les photos" url="http://www.polytechnique.fr/eleves/binets/photo/photos/photos.htm" />
</module>
