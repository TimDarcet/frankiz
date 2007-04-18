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
	Gestion de la création d'un compte et de la perte de mot de passe.
	
	$Id$

*/

require_once "../include/global.inc.php";
require_once "../include/mail.inc.php";

$mail_envoye = false;

if(!empty($_REQUEST['loginpoly'])) {
	$temp = explode(".",$_REQUEST['loginpoly']) ;
	$login = $temp[0] ;
	if (count($temp)==2) 
		$promo=$temp[1] ;
	else
		$promo="" ;
		
	$DB_trombino->query("SELECT eleve_id,login,prenom,nom,promo,mail FROM eleves "
						   ."WHERE login='$login' AND promo='$promo' ORDER BY promo DESC LIMIT 1");
	if($DB_trombino->num_rows() == 1) {
		list($id,$login,$prenom,$nom,$promo,$mail) = $DB_trombino->next_row();
		$hash = nouveau_hash();
		
		// Si le compte existe déjà on met à jour le hash, sinon on crée le compte
		//$DB_web->query("INSERT INTO compte_frankiz SET eleve_id='$id',passwd='',perms='',hash='$hash',hashstamp=NOW()+3600*6 "
		//		   ."ON DUPLICATE KEY UPDATE hash='$hash',hashstamp=NOW()+3600*6");
		// (MySQL 4.1 uniquement)
		$DB_web->query("SELECT 0 FROM compte_frankiz WHERE eleve_id='$id'");
		if($DB_web->num_rows() > 0)
			$DB_web->query("UPDATE compte_frankiz SET hash='$hash',hashstamp=DATE_ADD(NOW(),INTERVAL 6 HOUR) WHERE eleve_id='$id'");
		else
			$DB_web->query("INSERT INTO compte_frankiz SET eleve_id='$id',passwd='',perms='',hash='$hash', hashstamp=DATE_ADD(NOW(),INTERVAL 6 HOUR)");
		
		// Envoie le mail contenant l'url avec le hash
		$tempo = explode("profil",$_SERVER['REQUEST_URI']) ;
		$contenu = "<b>Bonjour</b><br>
				Pour te connecter sur Frankiz, il te suffit de cliquer sur le".
				   "lien ci-dessous :<br/>\n\n".
				   "<a href=\"".
				   BASE_URL."/profil/profil.php?uid=${id}&hash=${hash}".
				   "\">".
				   BASE_URL."/profil/profil.php?uid=${id}&hash=${hash}".
				   "</a>\n\n".
				   "N'oublie pas ensuite de modifier ton mot de passe.<br><br> Très cordialement<br>Le BR";
		if (($mail=="")||($mail=="NULL")) $mail = $login."@poly.polytechnique.fr" ;
		
		couriel($id,"[Frankiz] Création de compte/perte de mot de passe",$contenu);
		
		$mail_envoye = true;
		
	} else {
		ajoute_erreur(ERR_LOGINPOLY);
	}
}

require "../include/page_header.inc.php";
echo "<page id='mdp_perdu' titre='Frankiz : creation de compte/perte de mot de passe'>\n";

if($mail_envoye) { ?>
	<commentaire>Le mail a été envoyé avec succès à l'adresse <?php echo $mail?>.
	Il te permettra de te connecter une fois au site web Frankiz pour changer ton mot de passe
	ou choisir ton mot de passe si tu n'en a pas encore défini un.</commentaire>
	
<?php } else { ?>
	<?php if(a_erreur(ERR_LOGINPOLY)) echo "<warning>Le login que tu a donné n'existe pas.</warning>\n"?>
	<formulaire id="mdp_perdu" titre="Perte de mot de passe/ouverture de compte" action="profil/mdp_perdu.php">
		<note>Si tu souhaites créer ton compte Frankiz, ou si tu as perdu ton mot de passe, entre ton
		loginpoly.promo (par exemple dupont.2002) dans le champs ci dessous. Tu recevras dans les minutes qui suivent un courriel
		te permettant d'accéder à la partie réservée de Frankiz. Une fois authentifié grâce
		au lien contenu dans le courriel, n'oublie pas de changer ton mot de passe.</note>
		<champ id="loginpoly" titre="login.promo" valeur=""/>
		<bouton id="valider" titre="Valider"/>
	</formulaire>
<?php }

echo "</page>\n";
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
