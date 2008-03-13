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
	Pour faire peur aux gens qui ont des virus...
	
	$Id$
	
*/
// En-tetes

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin')&&!verifie_permission('windows'))
	acces_interdit();

function makedelete($id,$eleve_id,$nomv,$solved,$ip) {
        if ($solved!=2){
		return "<bouton titre=\"Nettoyer\" id=\"suppr_{$id}_{$eleve_id}_{$nomv}_{$solved}_{$ip}\" onClick=\"return window.confirm('Vous déclarez que ce virus a été éradiqué chez la personne)\"/>";
	}
	return "";
}

function getstate($solved) {
	if ($solved==0){
		return "Non signalé";
	}elseif ($solved==1){
		return "Signalé";
	}elseif ($solved==2){
		return "Résolu";
	}elseif ($solved==3){
		return "plus de 10j";
	}
	return "";
}

// Génération de la page
//===============
require_once BASE_FRANKIZ."include/page_header.inc.php";

?>

<page id="nettoyer_virus" titre="Frankiz : gestion des virus">
<h2>Liste des infections.</h2>
<note>Cette page sert à signaler qu'un virus detecté a bien été enlevé de l'ordinateur sur l'ip considérée, après s'en être assuré !</note>
<note>Cette page permet également de rajouter une infection manuellemet ou encore de définir un nouveau virus</note>
<note>Les virus listés ici sont ceux les plus susceptibles de pourrir le cache de la matrice.</note>
<?php
	//  Notification d'une nouvelle infection
	if (isset($_POST['notifier'])) {
		$ip = $_POST['ip'];
		$virus_id = $_POST['v_id'];
		$DB_admin->query("INSERT into `infections` (`id`, `ip`, `date`, `virus_id`, `solved`) VALUES ('','$ip',CURDATE(),'$virus_id','');");
	}
?>

<?php
	// Ajout d'un virus dans la table liste_virus
	if (isset($_POST['ajouter'])) {
		$port = $_POST['port'];
		$nomv = $_POST['nomv'];
		$DB_admin->query("INSERT INTO `liste_virus` (`virus_id`, `port`, `nom`) VALUES ('','$port','$nomv');");
	}
?>

<?php
	foreach ($_POST AS $keys => $val){
		$temp = explode("_",$keys) ;
		if ($temp[0] == "suppr") {
			//sur qui est faite la manip ?
			$DB_trombino->query("SELECT nom,prenom,promo,piece_id FROM eleves WHERE eleve_id='{$temp[2]}'");
			list($nom,$prenom,$promo,$ksert) = $DB_trombino->next_row() ;
			//log de la partie admin...
			log_admin($_SESSION['uid']," certifié que $temp[3] a bien été supprimé de l'ordinateur de $nom $prenom ($promo) ") ;
			echo "<note> Le virus est considéré comme enlevé de l'odinateur. On le signale par mail à l'utilisateur.</note>";
			$contenu="Nous avons bien pris en compte la suppression du virus $temp[3] de ton ordinateur.<br><br>".
			"Nous te rappellons qu'il est de ta responsabilité d'assurer la sécurité de ton pc. Si tu ne sais pas comment faire utilise le domaine windows, il est là pour ça. Tu trouveras tout les renseignements nécessaires dans l'infoBR.<br>".
			"N'hésites pas à nous signaler tout problème !<br><br>".
			"Très Cordialement<br>".
			"Le BR<br>"  ;
			couriel($temp[2],"[Virus] Suppression de $temp[3]",$contenu,WINDOWS_ID);
			//on vérifie qu'il est clean...
			$DB_admin->query("SELECT id FROM infections WHERE ip='{$temp[5]}' AND NOT(solved='2') GROUP BY virus_id, ip");
			if ($temp[4]==3 && $DB_admin->num_rows()==0){
				$contenu="L'ordinateur de $nom $prenom ($promo, ksert : $ksert) a été débarassé de ses virus, merci donc de bien vouloir lui remettre le réseau !<br><br>".
				"Très Cordialement<br>".
				"Le BR<br>"  ;
				couriel(ROOT_ID,"[Virus] L'ordinateur de $nom $prenom ($promo) est clean !",$contenu,WINDOWS_ID);
			}
			// on le prends en compte....
			$DB_admin->query("UPDATE infections AS i1, infections AS i2 SET i1.solved='2' WHERE i2.id='{$temp[1]}' AND i1.ip = i2.ip AND i1.virus_id = i2.virus_id");
		}
		if($temp[0]=="prev"){
			$contenu="Attention ! ton ordinateur est actuellement infecté par le virus $temp[3] depuis le ".preg_replace('/^(.{4})-(.{2})-(.{2})$/','$3-$2-$1', $temp[1]).". Dans $temp[4] jours nous serons obligés de te couper le réseau !<br><br>".
			"Nous te rappellons qu'il est de ta responsabilité d'assurer la sécurité de ton pc. Si tu ne sais pas comment faire pour enlever ce virus, contacte un <a href='mailto:windows@frankiz.polytechnique.fr'>admin@windows</a>.<br>".
			"N'hésites pas à nous signaler tout problème !<br><br>".
			"Très Cordialement<br>".
			"Le BR<br>";
			couriel($temp[2],"[Virus] ton pc est infecté par $temp[3] !",$contenu,WINDOWS_ID);
			//voilà il est préviendu
			$DB_admin->query("UPDATE infections SET solved='1' WHERE id='{$temp[6]}'");
		}
		if($temp[0]=="detail" && $temp[2] == "x") {
			$DB_admin->query("SELECT i.id, i.ip, i.date, i.solved, v.nom, t.eleve_id FROM infections AS i LEFT JOIN liste_virus AS v ON v.virus_id = i.virus_id LEFT JOIN prises AS p ON p.ip = i.ip LEFT JOIN trombino.eleves AS t ON t.piece_id = p.piece_id WHERE i.date > '0000-00-00' AND t.login = '{$temp[1]}' ORDER BY i.ip, i.date");
		echo "<liste id='liste_login' selectionnable='non' titre='Historique de {$temp[1]}' action='admin/nettoyer_virus.php'>\n";
?>
			<entete id="ip" titre="IP"/>
			<entete id="date" titre="Infection"/>
			<entete id="nom" titre="Nom du virus"/>
			<entete id="statut" titre="Statut"/>
			<entete id="nettoyer" titre=""/>
<?php
			while(list($id,$ip,$date,$solved,$nomv,$eleve_id) = $DB_admin->next_row()) {
				echo "\t\t<element id='$id'>\n";
				echo "\t\t\t<colonne id='ip'>$ip</colonne>\n";
				echo "\t\t\t<colonne id='date'>$date</colonne>\n";
				echo "\t\t\t<colonne id='nom'>$nomv</colonne>\n";
				echo "\t\t\t<colonne id='statut'>".(getstate($solved))."</colonne>\n";
				echo "\t\t\t<colonne id='nettoyer'>".(makedelete($id,$eleve_id,$nomv,$solved,$ip))."</colonne>\n";
				echo "\t\t</element>\n";
			}
			echo "</liste>\n";
//			echo "<lien titre=\"Voir la fiche TOL de {$temp[1]}\" url=\"trombino.php?chercher&login={$temp[1]}\" /><br/>\n";
			echo "<lien titre=\"Voir la fiche TOL de {$temp[1]}\" url=\"trombino.php?chercher&amp;loginpoly={$temp[1]}\" />";
		}
	}
	

        $DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
	list($promo_temp) = $DB_web->next_row() ;
	$DB_admin->query("SELECT i.ip,i.date,DATEDIFF(DATE_ADD(i.date,INTERVAL 10 DAY),CURDATE()),i.solved,e.login,pi.tel,e.eleve_id,l.nom,i.id FROM infections AS i LEFT JOIN prises as p ON p.ip=i.ip LEFT JOIN trombino.eleves as e ON e.piece_id=p.piece_id LEFT JOIN trombino.pieces as pi ON pi.piece_id = p.piece_id  LEFT JOIN liste_virus AS l ON l.virus_id=i.virus_id INNER JOIN infections WHERE i.solved != 2 GROUP BY i.virus_id, i.ip ORDER BY i.ip, i.solved, l.nom");
?>
	<liste id="liste_virus" selectionnable="non" titre="Infections courantes"  action="admin/nettoyer_virus.php">
		<entete id="ip" titre="IP"/>
		<entete id="login" titre="login"/>
		<entete id="tel" titre="N°Tel"/>
		<entete id="date" titre="Depuis le"/>
		<entete id="statut" titre="Statut"/>
		<entete id="nomv" titre="Nom du virus"/>
		<entete id="nettoyer" titre=""/>
		<entete id="prevenir" titre=""/>
<?php
	while(list($ip,$date,$rebours,$solved,$login,$tel,$eleve_id,$nomv,$id)= $DB_admin->next_row()){
		echo "\t\t<element id=\"$id\">\n";
		$ip1 = "<bouton titre='Historique' id='detail_$login' type='detail'/>$ip";
		echo "\t\t\t<colonne id=\"ip\">$ip1</colonne>\n";
		echo "\t\t\t<colonne id=\"login\">$login</colonne>\n";
		echo "\t\t\t<colonne id=\"tel\">$tel</colonne>\n";
		echo "\t\t\t<colonne id=\"date\">".preg_replace('/^(.{4})-(.{2})-(.{2})$/','$3-$2-$1', $date)."</colonne>\n";
		echo "\t\t\t<colonne id=\"statut\">".(getstate($solved))."</colonne>\n";
		echo "\t\t\t<colonne id=\"nomv\">$nomv</colonne>\n";
		echo "\t\t\t<colonne id=\"nettoyer\">".(makedelete($id,$eleve_id,$nomv,$solved,$ip))."</colonne>\n";
?>
			<colonne id="prevenir">
<?php
		if($rebours<5 && $rebours>-1 && $solved==0){
?>
				<bouton titre="Prévenir" id="prev_<?php echo "$date";?>_<?php echo "$eleve_id";?>_<?php echo "$nomv";?>_<?php echo "$rebours";?>_<?php echo "$ip";?>_<?php echo "$id";?>" onClick="return window.confirm('Voulez vous prévenir par mail cette personne quelle est infectée ?')"/>
<?php
		}
?>
			</colonne>
<?php
		echo "\t\t</element>\n";
	}
?>
	</liste>

<?php
// Notifier la presence d'une nouvelle infection
?>
	<liste id="notifier" selectionable="non" titre="Notifier la présence d'une nouvelle infection" action="admin/nettoyer_virus.php">
		<entete id="ip" titre="IP"/>
		<entete id="virus" titre="Virus"/>
		<entete id="notifier" titre=""/>

		<element id="notifier">
			<colonne id="ip">
				<champ id="ip" titre="" valeur=""/>
			</colonne>
			<colonne id="virus">
				<choix id="v_id"  type="combo" valeur="Ajout">
                                       <option titre="" id="default"/>
					<?php
                                        $DB_admin->query("SELECT virus_id,nom FROM liste_virus");
					while (list($virus_id,$nomv) = $DB_admin->next_row())
					echo "<option titre=\"$nomv\" id=\"$virus_id\"/>\n";
					?>
				</choix>
			</colonne>
			<colonne id="notifier">
				<bouton id="notifier" titre="Notifier"/>
			</colonne>
		</element>
	</liste>

<?php
// Ajouter un nouveau virus dans la base
?>
	<liste id="ajouter" selectionable="non" titre="Répertorier un nouveau virus" action="admin/nettoyer_virus.php">
		<entete id="nomv" titre="Nom du virus"/>
		<entete id="port" titre="Port caractéristique"/>
		<entete id="ajouter" titre=""/>

		<element id="ajouter">
			<colonne id="nomv"><champ id="nomv" titre="" valeur=""/></colonne>
			<colonne id="port"><champ id="port" titre="" valeur=""/></colonne>
			<colonne id="ajouter"><bouton id="ajouter" titre="Ajouter"/></colonne>
		</element>
	</liste>
		
</page>

<?php
require_once BASE_FRANKIZ."include/page_footer.inc.php";
?>
