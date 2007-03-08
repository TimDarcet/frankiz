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
	Gestion des utilisateurs et de leur droits. L'accès se fait par le trombino,
	après une recherche.
	
	ATTENTION : il n'y a volontairement pas de pages web d'administration permettant l'ajout
	ou la suppression d'un utilisateur. En effet, il n'y a aucune raison de supprimer un utilisateur,
	et pour l'ajout d'utilisateur, l'opération a lieu par bloc pour toute une promo or c'est beaucoup
	plus facile de le faire via un fichier de commande MySQL ou avec un interface web dédiée.
	
	L'ID de l'utilisateur à modifier est passer dans le paramètre GET 'user'.
	
	$Id$
	
*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('trombino'))
	acces_interdit();

// On vérifie que la personne envoie bien l'id sinon ca sert a rien ...
if(!isset($_REQUEST['id']))
	rediriger_vers("trombino.php");


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="admin_user" titre="Frankiz : gestion des utilisateurs">
<?php

$id = $_REQUEST['id'] ;
// Traitement des demandes de modifications !
//============================

// Modification de la partie "général"

if (isset($_POST['mod_generale'])) {
	$nom = $_POST['nom'];
	$prenom = $_POST['prenom'];
	$surnom = $_POST['surnom'];
	$date_nais = $_POST['date_nais'];
	$sexe = $_POST['sexe'];
	$piece_id = $_POST['piece_id'];
	if ($piece_id == "")
		$piece_id = "NULL";
	else
		$piece_id = "'$piece_id'";
	$section_id = $_POST['section_id'];
	$cie = $_POST['cie'];
	$promo = $_POST['promo'];
	$login = $_POST['login'];
	$mail = $_POST['mail'];
	$DB_trombino->query("UPDATE eleves SET nom='$nom', prenom='$prenom', surnom='$surnom', date_nais='$date_nais', sexe='$sexe', piece_id=$piece_id, section_id='$section_id', cie='$cie', promo='$promo', login='$login', mail='$mail' WHERE eleve_id=$id ");
	
	echo "<commentaire>Modification de la partie générale faite avec succès</commentaire>" ;
}

// Modification de la partie "binets"

// Modification d'un commentaire sur un binet
if (isset($_POST["mod_binet"])) {
	foreach($_POST["commentaire"] as $key=>$val)
		$DB_trombino->query("UPDATE membres SET remarque='$val' WHERE eleve_id='$id' AND binet_id='$key'");
	$DB_trombino->query("UPDATE eleves SET commentaire='{$_POST['perso']}' WHERE eleve_id='$id'");
	$commentaire = $_POST['perso'];
	echo "<commentaire>Modification des commentaires effectuée avec succès</commentaire>";
}

// Suppression d'un binet
if (isset($_POST["suppr_binet"])) {
	$count = 0;
	if (isset($_POST['elements'])) {
		$ids = "";
		foreach($_POST['elements'] as $key => $value) {
			if ($value == "on") $ids .= (empty($ids) ? "" : ",") . "'$key'";
			$count ++;
		}
	}
	if ($count >= 1) {
		$DB_trombino->query("DELETE FROM membres WHERE binet_id IN ($ids) AND eleve_id='$id'");
		echo "<commentaire>Suppression de $count binet(s).</commentaire>";
	} else {
		echo "<warning>Aucun binet n'est sélectionné. Aucun binet n'a donc été supprimé de la liste de ses binets.</warning>";
	}
}

// Ajout d'un binet
if (isset($_POST['add_binet'])) {
	if ($_POST['liste_binet'] != 'default') {
		$DB_trombino->query("REPLACE INTO membres SET eleve_id='$id', binet_id='{$_POST['liste_binet']}'");
		echo "<commentaire>Binet correctement ajouté</commentaire>";
	} else {
		echo "<warning>Aucun binet sélectionné. Aucun binet n'a donc été ajouté à la liste de ses binets.</warning>";
	}
}







// Modification de la partie "compte FrankizII"

if (verifie_permission('admin') && isset($_POST['mod_compte_fkz'])) {
	if ($_POST['pass']!="") {
		$pass2 = hash_shadow($_POST['pass']) ;
		$DB_web->query("UPDATE compte_frankiz SET passwd='$pass2' WHERE eleve_id=$id");

		$DB_trombino->query("SELECT login FROM eleves WHERE eleve_id = $id LIMIT 0,1");
		list($login) = $DB_trombino->next_row();
		if (isset($login))
		{
			// Synchronisation avec le wifi
			$DB_wifi->query("UPDATE alias SET Password='$pass2' WHERE Alias='$login' AND Method='TTLS';");
			$DB_wifi->query("UPDATE radcheck SET Value='$pass2' WHERE UserName='$login' AND Attribute='Crypt-Password';");
		}
		echo "<commentaire>Modification du mot de passe réalisée correctement</commentaire>" ;
	}
        $perms = "" ;
        foreach(liste_droits() as $droits => $nom) {
            if (isset($_POST[$droits]))
                $perms .= "$droits," ;
        }
	if (isset($_POST['perms_binets']))
		$perms .= $_POST['perms_binets'];
	$DB_web->query("UPDATE compte_frankiz SET perms='$perms' WHERE eleve_id=$id");
	
	echo "<commentaire>Modification de la partie Compte Frankiz faite avec succès</commentaire>" ;
}

// Modification de ses variables génériques
?>
	<formulaire id="user_general" titre="Général" action="admin/user.php?id=<?php echo $id?>">
<?php
		$DB_trombino->query("SELECT nom,prenom,surnom,date_nais,sexe,piece_id,section_id,cie,promo,login,mail,commentaire FROM eleves WHERE eleve_id=$id ORDER BY nom ASC");
		list($nom,$prenom,$surnom,$date_nais,$sexe,$piece_id,$section,$cie,$promo,$login,$mail,$commentaire) = $DB_trombino->next_row() ;
?>
		<champ id="nom" titre="Nom" valeur="<?php echo $nom?>"/>
		<champ id='prenom' titre='Prénom' valeur='<?php echo $prenom?>'/>
		<champ id='surnom' titre='Surnom' valeur='<?php echo $surnom?>'/>
		<champ id='login' titre='Login' valeur='<?php echo $login?>'/>
		<champ id='date_nais' titre='Date de naissance' valeur='<?php echo $date_nais?>'/>
		<champ id='sexe' titre='Sexe' valeur='<?php echo $sexe?>'/>
		<champ id='piece_id' titre='Ksert' valeur='<?php echo $piece_id?>'/>
		<champ id='section_id' titre='Section' valeur='<?php echo $section?>'/>
		<champ id='cie' titre='Cie' valeur='<?php echo $cie?>'/>		
		<champ id='promo' titre='Promo' valeur='<?php echo $promo?>'/>
		<champ id='mail' titre='Mail' valeur='<?php echo $mail?>'/>
		
		<bouton id='mod_generale' titre='Changer'/>
	</formulaire>

<?php
// Modification de ses binets et des commentaires sur les binets  
?>
	<liste id="user_binets" selectionnable="oui" action="admin/user.php?id=<?php echo $id; ?>" titre="Ses Binets">
		<entete id="binet" titre="Binet" />
		<entete id="commentaire" titre="Commentaire" />
<?php
$DB_trombino->query("SELECT membres.remarque,membres.binet_id,binets.nom FROM membres LEFT JOIN binets USING(binet_id) WHERE eleve_id=$id ORDER BY binets.nom ASC");
while (list($remarque,$binet_id,$nom) = $DB_trombino->next_row()) {
?>
		<element id="<?php echo $binet_id; ?>">
			<colonne id="binet"><?php echo $nom; ?> :</colonne>
			<colonne id="commentaire"><champ id="commentaire[<?php echo $binet_id; ?>]" titre="" valeur="<?php echo $remarque; ?>"/></colonne>
		</element>
<?php
}
?>
		<element id="-1" selectionnable="non">
			<colonne id="binet">Rajouter un binet</colonne>
			<colonne id="commentaire">
				<choix id="liste_binet" type="combo" valeur="Ajout">
					<option titre="" id="default"/>
<?php
$DB_trombino->query("SELECT nom,binet_id FROM binets ORDER BY nom ASC");
while (list($nom_binet,$binet_id) = $DB_trombino->next_row()) {
?>
					<option titre="<?php echo $nom_binet; ?>" id="<?php echo $binet_id; ?>"/>
<?php
}
?>
				</choix>
				<bouton id="add_binet" titre="Ajouter" />
			</colonne>
		</element>

		<element id="-2" selectionnable="non">
			<colonne id="binet">Autres commentaires</colonne>
			<colonne id="commentaire">
				<zonetext id="perso" titre="Commentaire perso" type="moyen"><?php echo $commentaire; ?></zonetext>
			</colonne>
		</element>
		<bouton id="suppr_binet" titre="Supprimer" onClick="return window.confirm('Es-tu sûr de vouloir supprimer ce binet ?')"/>
		<bouton id="mod_binet" titre="Enregistrer les commentaires"/>
	</liste>


<?php

// SU et modifs du compte frankiz => seul l'admin peut le faire.
if(verifie_permission('admin')){
?>
	<formulaire id="user_su" titre="Se Logguer en tant que cet utilisateur" action="?su=<?php echo $id?>">
		<bouton id='do_su' titre='SU'/>
	</formulaire>
<?php


// Modification de ses préferences FrankizII
		$DB_web->query("SELECT perms FROM compte_frankiz WHERE eleve_id=$id");
		list($perms) = $DB_web->next_row();
		$perms = split(",",$perms);
?>
	<formulaire id="user_compt_fkz" titre="Compte Frankiz" action="admin/user.php?id=<?php echo $id?>">
                <note>Pour le mot de passe : Si vous le laissez vide, il ne sera pas modifié !</note>
		<champ id="pass" titre="Mot de passe" valeur=""/>
                <note>Pour les webmestres et prez de binets, il faut allez dans le binet en question pour les modifier</note>
		<choix titre="Droits" id="droits" type="checkbox" valeur="<?php
			foreach(liste_droits() as $droits => $nom){
				foreach($perms as $tmp => $perm)
					if($perm == $droits){
						echo "$droits ";
						break;
					}
			}
					?>">
<?php
			foreach(liste_droits() as $droits => $nom)
					echo "\t\t\t<option titre=\"$nom\" id=\"$droits\"/>\n";
?>
		</choix>
<?php			$perms_binets = "";
			foreach($perms as $tmp => $perm)
					if(eregi("webmestre_",$perm)||eregi("prez_",$perm)){
						$perms_binets .= "$perm,";
					}
			if($perms_binets != "")
				echo "<hidden id=\"perms_binets\" valeur=\"".$perms_binets."\" />";
?>
		<bouton id='mod_compte_fkz' titre='Changer'/>
	</formulaire>
<?php 
} 
?>

</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
