<?php
/*
	$Id$

	Liens permettants de contacter les webmestres et faire des demandes.
*/
?>
<module id="liens_contacts" titre="Contacts">
	<lien titre="Écrire aux webmasters" url="mailto:web@frankiz" />
	<?php if(est_authentifie(AUTH_MINIMUM)): ?>
		<lien titre="Proposer une annonce" url="gestion/annonce.php" />
		<lien titre="Proposer une activité" url="gestion/activite.php" />
		<lien titre="Mise à jour d'un binet" url="gestion/majbinet.php" />
		<lien titre="Demander un mail promo" url="gestion/mailpromo.php" />
		<lien titre="Proposer un sondage" url="gestion/sondage.php/" />
	<?php endif; ?>
</module>
