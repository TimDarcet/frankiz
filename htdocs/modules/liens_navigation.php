<?php
/*
	Liens de navigation dans le site web.	
	
	$Log$
	Revision 1.5  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

	Revision 1.4  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
?>
<module id="liens_navigation" titre="Frankiz">
	<lien titre="Frankiz" url="." />
	<?php if(est_authentifie(AUTH_MINIMUM)): ?>
		<lien titre="Se déconnecter" url="index.php?logout=1" />
		<lien titre="Profil" url="profil/profil.php" />
		<lien titre="Profil réseau" url="profil/reseau.php" />
		<lien titre="Skins" url="profil/skin.php" />
		<lien titre="InfoBr" url="documentation/infobr.pdf" />
	<?php else: ?>
		<lien titre="Se connecter" url="login.php" />			
	<?php endif; ?>
	<lien titre="Docs/Manuels" url="documentation/" />
	<lien titre="FAQ" url="faq/" />
	<lien titre="Télécharger" url="xshare/" />
	<lien titre="Binets" url="binets/" />
	<?php if(est_authentifie(AUTH_MINIMUM)): ?>
		<lien titre="Trombino" url="trombino/" />
	<?php endif; ?>
	<?php if(!empty($_SESSION['user']->perms)): ?>
		<lien titre="Administration" url="admin/" />
	<?php endif; ?>
</module>
