<?php
/*
	Copyright (C) 2004 Binet R�seau
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
	Gestion de la cr�ation d'un compte et de la perte de mot de passe.
	
	$Log$
	Revision 1.14  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site

	Revision 1.13  2004/10/16 01:47:44  schmurtz
	Bug dans l'envoi d'un mail
	
	Revision 1.12  2004/09/15 23:20:07  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.11  2004/09/15 21:42:21  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require "../include/global.inc.php";
require_once "../include/mail.inc.php";

$mail_envoye = false;

if(!empty($_REQUEST['loginpoly'])) {
	$DB_trombino->query("SELECT eleve_id,login,prenom,nom,promo,mail FROM eleves "
						   ."WHERE login='".$_REQUEST['loginpoly']."' ORDER BY promo DESC LIMIT 1");
	if($DB_trombino->num_rows() == 1) {
		list($id,$login,$prenom,$nom,$promo,$mail) = $DB_trombino->next_row();
		$hash = nouveau_hash();
		
		// Si le compte existe d�j� on met � jour le hash, sinon on cr�e le compte
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
		$contenu = "Pour te connecter sur Frankiz, il te suffit de cliquer sur le\n".
				   "lien ci-dessous�:\n\n".
				   "	[ http://".$_SERVER['SERVER_NAME'].$tempo[0]."profil/profil.php?uid=${id}&hash=${hash} ]\n\n".
				   "N'oublie pas ensuite de modifier ton mot de passe.";
		if (($mail=="")||($mail=="NULL")) $mail = $login."@poly.polytechnique.fr" ;
		
		$message = new Mail("Binet R�seau <br@frankiz.polytechnique.fr>","$nom $prenom <$mail>","[Frankiz] Cr�ation de compte/perte de mot de passe");
		$message->SetBody($contenu);
		$message->Send();
		
		$mail_envoye = true;
		
	} else {
		ajoute_erreur(ERR_LOGINPOLY);
	}
}

require "../include/page_header.inc.php";
echo "<page id='mdp_perdu' titre='Frankiz : creation de compte/perte de mot de passe'>\n";

if($mail_envoye) { ?>
	<p>Le mail a �t� envoy� avec succ�s � l'adresse <?php echo $mail?>.
	Il te permettra de te connecter une fois au site web Frankiz pour changer ton mot de passe
	ou choisir ton mot de passe si tu n'en a pas encore d�fini un.</p>
	
<?php } else { ?>
	<?php if(a_erreur(ERR_LOGINPOLY)) echo "<p>Le login que tu a donn� n'existe pas.</p>\n"?>
	<formulaire id="mdp_perdu" titre="Perte de mot de passe/ouverture de compte" action="profil/mdp_perdu.php">
		<commentaire>Si tu souhaites cr�er ton compte Frankiz, ou si tu as perdu ton mot de passe, entre ton
		login poly dans le champs si dessous. Tu receveras dans les minutes qui suivent un mail
		te permettant d'acc�der � la partie r�serv�e de Frankiz. Une fois authentifi� gr�ce
		au lien contenu dans le mail, n'oublie pas de changer ton mot de passe.</commentaire>
		<champ id="loginpoly" titre="Login poly" valeur=""/>
		<bouton id="valider" titre="Valider"/>
	</formulaire>
<?php }

echo "</page>\n";
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
