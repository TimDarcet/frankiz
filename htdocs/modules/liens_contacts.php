<?php
/*
	Liens permettants de contacter les webmestres et faire des demandes.
	
	$Log$
	Revision 1.4  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

	Revision 1.3  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
?>
<module id="liens_contacts" titre="Contacts">
	<lien titre="Écrire aux webmasters" url="mailto:web@frankiz" />
	<?php if(est_authentifie(AUTH_MINIMUM)): ?>
		<lien titre="Proposer une annonce" url="propositions/annonce.php" />
		<lien titre="Proposer une activité" url="propositions/activite.php" />
		<lien titre="Demander un mail promo" url="propositions/mailpromo.php" />
		<lien titre="Proposer un sondage" url="propositions/sondage.php/" />
	<?php endif; ?>
</module>
