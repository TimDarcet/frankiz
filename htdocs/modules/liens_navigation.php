<?php
/*
	$Id$
	
	Liens de navigation dans le site web.	
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
