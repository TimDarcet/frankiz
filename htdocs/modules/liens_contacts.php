<?php
/*
	$Id$

	Liens permettants de contacter les webmestres et faire des demandes.
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
