<?php
/*
	G�n�ration des donn�es de statistiques des serveurs.
	
	Certaines donn�es sont mise en statique pour exemple. Il serait bien de r��crir les scripts
	qui r�cup�re ces informations pour qu'ils enregistrent leurs donn�es dans des fichiers directement
	en XML, et non sous forme d'une suite de "0" et de "1".
	

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
