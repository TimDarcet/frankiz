<?php
/*
	Génération des données de statistiques des serveurs.
	
	Certaines données sont mise en statique pour exemple. Il serait bien de réécrir les scripts
	qui récupère ces informations pour qu'ils enregistrent leurs données dans des fichiers directement
	en XML, et non sous forme d'une suite de "0" et de "1".
	
	TODO : limiter à l'état des services (up/down), les pluparts des élèves s'en fout complètement
	Par ailleurs, il faudra créer des pages de stats assez complètes pour les admins (avec les usages
	de bande passante, de cpu). 
	
	$Log$
	Revision 1.5  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

	Revision 1.4  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/
if(est_authentifie(AUTH_MINIMUM)) { ?>
	<module id="stats" titre="Statistiques">
		<statistiques>
			<serveur nom="frankiz" etat="up" uptime="<?php @include 'http://frankiz.polytechnique.fr/uptimefkz' ?>" />
			<serveur nom="gwennoz" etat="up" uptime="<?php @include 'http://gwennoz.polytechnique.fr/uptimegwz' ?>" />
			<serveur nom="heol" etat="up" uptime="<?php @include 'http://heol.polytechnique.fr/uptimeheol' ?>" />
			<service nom="web frankiz" stat="http://frankiz.polytechnique.fr/webalizer/" />
			<service nom="web binets" stat="http://gwennoz.polytechnique.fr/webalizer/" />
			<service nom="news" stat="http://frankiz.polytechnique.fr/news/" />
			<service nom="xnet" stat="http://frankiz.polytechnique.fr/accueil/xnetstatquick.php" />
			<serveur nom="kuzh" etat="down" />
			<serveur nom="sil" etat="up" />
			<serveur nom="poly" etat="up" />
		</statistiques>
	</module>
<?php } ?>
