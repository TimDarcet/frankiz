<?php
/*
	Les divers cadres de liens de la page d'accueil.
	
	$Id$
*/
?>

	<module id="liens_navigation" titre="Frankiz">
		<lien titre="Frankiz" url="." />
		<?php if(est_authentifie(AUTH_MINIMUM)): ?>
			<lien titre="Se déconnecter" url="index.php?logout=1" />
			<lien titre="Profil" url="profil/profil.php" />
			<lien titre="Skins" url="profil/skin.php" />
			<lien titre="InfoBr" url="documentation/infobr.pdf" />
		<?php else: ?>
			<lien titre="Se connecter" url="login.php" />			
		<?php endif; ?>
		<lien titre="Docs/Manuels" url="documentation/" />
		<lien titre="FAQ" url="faq/" />
		<lien titre="Télécharger" url="xshare/" />
		<lien titre="Binets" url="binets.php" />
		<?php if(est_authentifie(AUTH_MINIMUM)): ?>
			<lien titre="Trombino" url="trombino/" />
			<lien titre="Xplorateur" url="xplorateur/" />
		<?php endif; ?>
		<?php if(verifie_permission("admin")): ?>
			<lien titre="Admin" url="admin/" />
		<?php endif; ?>
	</module>

	<module id="liens_contacts" titre="Contacts" visible="<?php echo skin_visible("liens_contacts"); ?>">
		<lien titre="Écrire aux webmasters" url="mailto:web@frankiz" />
		<?php if(est_authentifie(AUTH_MINIMUM)): ?>
			<lien titre="Proposer une annonce" url="gestion/annonce.php" />
			<lien titre="Proposer une activité" url="gestion/activite.php" />
			<lien titre="Mise à jour d'un binet" url="gestion/majbinet.php" />
			<lien titre="Demander un mail promo" url="gestion/mailpromo.php" />
			<lien titre="Proposer un sondage" url="gestion/sondage.php/" />
		<?php endif; ?>
	</module>

	<module id="liens_ecole" titre="Liens école" visible="<?php echo skin_visible("liens_ecole"); ?>">
		<lien titre="La Kes" url="http://binets.polytechnique.fr/kes/" />
		<lien titre="Aide aux binets" url="http://binets.polytechnique.fr/kes-binets/" />
		<lien titre="Le site des élèves" url="http://www.polytechnique.fr/eleves/" />
		<lien titre="Redirection des mails" url="http://poly.polytechnique.fr/" />
		<lien titre="Corrige ton poly" url="http://binets.polytechnique.fr/corrigepoly/" />
		<lien titre="Site de l'école" url="http://www.polytechnique.fr/" />
		<lien titre="Site de la DE" url="http://www.edu.polytechnique.fr/" />
		<lien titre="Bureau des effectifs" url="http://intranet-effectifs.polytechnique.fr/" />
		<lien titre="Intranet" url="http://intranet.polytechnique.fr/" />
		<lien titre="Polytechnique.org" url="http://www.polytechnique.org/" />
		<lien titre="Les photos" url="http://www.polytechnique.fr/eleves/binets/photo/photos/photos.htm" />
	</module>
