<?php
/*
	Liens permettants de contacter les webmestres et faire des demandes.
	
	$Log$
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
<module id="liens_contacts" titre="Contacts">
	<lien titre="Écrire aux webmasters" url="proposition/mail_web.php" />
	<?php if(est_authentifie(AUTH_MINIMUM)): ?>
		<lien titre="Proposer une annonce" url="proposition/annonce.php" />
		<lien titre="Proposer une activité" url="proposition/activite.php" />
		<lien titre="Demander un mail promo" url="proposition/mailpromo.php" />
		<lien titre="Proposer un sondage" url="proposition/sondage.php/" />
	<?php endif; ?>
</module>
