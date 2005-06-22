<?php
/*
	Copyright (C) 2004 Binet Réseau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
/*
	Liens de navigation dans le site web.	
	
	$Id$

*/
?>
<module id="liens_navigation" titre="Frankiz">
	<?php if(!est_authentifie(AUTH_MINIMUM)): ?>
		<lien id="connect" titre="Se connecter" url="login.php" key="l"/>
	<?php endif; ?>
	<lien id="annonces" titre="Annonces" url="." key="a"/>
	<lien id="activites" titre="Activités" url="activites.php"/>
	<lien id="xshare" titre="Télécharger" url="xshare.php" key="x"/>
	<lien id="faq" titre="FAQ" url="faq.php" key="f"/>
	<?php if(est_authentifie(AUTH_INTERNE)){ ?>
		<lien id="trombino" titre="Trombino" url="trombino.php" key="t"/>
	<?php } ?>
	<lien id="binets"  titre="Binets" url="binets.php" key="b"/>
	<?php if(est_authentifie(AUTH_MINIMUM) && est_interne()): ?>
		<lien id="siteseleves" titre="Sites élèves" url="http://perso.frankiz"/>
	<?php else: ?>
		<lien id="siteseleves" titre="Sites élèves" url="siteseleves.php"/>
	<?php endif; ?>
	<lien id="meteo" titre="Météo" url="meteo.php" key="m"/>
	<lien id="vocab" titre="Vocabulaire" url="vocabulaire.php" key="v"/>
</module>

<?php if(est_authentifie(AUTH_MINIMUM)): ?>
<module id="liens_profil" titre="Préférences">
<?
	if(isset($_SESSION['sueur']))
		echo "<warning>ATTENTION, su en cours. Pour revenir à ta vrai identité, clique <a href='index.php?logout=1'>ici</a></warning>";
?>
	<lien id="profil"  titre="Préférences" url="profil/index.php" key="p"/>
	<?php if ((count($_SESSION['user']->perms)>1)&&($_SESSION['user']->perms[0]!="")) { ?>
		<lien id="admin" titre="Administration" url="gestion/" key="g"/>
	<?php } ?>
		<?php if(est_authentifie(AUTH_FORT)): ?>
		<lien id="deconnect" titre="Se déconnecter" url="index.php?logout=1" key="l"/>
	<?php endif; ?>
</module>
<?php endif; ?>
