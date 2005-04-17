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
	
	$Log$
	Revision 1.7  2005/04/17 23:16:28  dei
	quelques modif :
	on signale au roots quand un ordinateur est clean si il avait le r�seau coup�on
	peut pr�venir les gens qui lisent pas frankiz par mail

	Revision 1.6  2005/04/13 17:09:58  pico
	Passage de tous les fichiers en utf8.
	
	Revision 1.5  2005/04/13 16:14:34  pico
	test
	
	Revision 1.4  2005/04/13 16:09:20  pico
	Correction
	
	Revision 1.3  2005/04/13 15:02:00  dei
	les logs qui vont bien pour savoir qui devirusise les gens
	
	Revision 1.2  2005/04/13 14:10:30  dei
	j'avais oublié ça , ça ne servait que pour le test...
	
	Revision 1.1  2005/04/13 13:58:16  dei
	Voilà qui devrait faire peur à certains
	Basé sur le script de fruneau module sur la page principale + page d'admin...
	
	
*/
// En-tetes

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('windows'))
	acces_interdit();


// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>

<page id="nettoyer_virus" titre="Frankiz : gestion des virus">
<h2>Liste des personnes infectées ou ayant eu un virus.</h2>
<note>Cette page sert à signaler qu'un virus detecté a bien été enlevé de l'ordinateur sur l'ip considérée, après s'en être assuré !</note>
<note>Les virus listés ici sont ceux les plus susceptibles de pourrir le cache de la matrice.</note>
<?
	foreach ($_POST AS $keys => $val){
		$temp = explode("_",$keys) ;
		if ($temp[0] == "suppr") {
			//sur qui est faite la manip ?
			$DB_trombino->query("SELECT nom,prenom,promo,piece_id FROM eleves WHERE eleve_id='{$temp[2]}'");
			list($nom,$prenom,$promo,$ksert) = $DB_trombino->next_row() ;
			//log de la partie admin...
			log_admin($_SESSION['user']->uid," certifié que $temp[3] a bien été supprimé de l'ordinateur de $nom $prenom ($promo) ") ;
			echo "<note> Le virus est considéré comme enlevé de l'odinateur. On le signale par mail à l'utilisateur.</note>";
			$contenu="Nous avons bien pris en compte la suppression du virus $temp[3] de ton ordinateur.<br><br>".
			"Nous te rappellons qu'il est de ta responsabilité d'assurer la sécurité de ton pc. Si tu ne sais pas comment faire utilise le domaine windows, il est là pour ça. Tu trouveras tout les renseignements nécessaires dans l'infoBR.<br>".
			"N'hésites pas à nous signaler tout problème !<br><br>".
			"Très Cordialement<br>".
			"Le BR<br>"  ;
			couriel($temp[2],"[Virus] Suppression de $temp[3]",$contenu,WINDOWS_ID);
			//on vérifie qu'il est clean...
			$DB_admin->query("SELECT id FROM infections WHERE ip='{$temp[5]}' AND NOT(solved='2')");
			if ($temp[4]==3 && $DB_admin->num_rows()==0){
				$contenu="L'ordinateur de $nom $prenom ($promo, ksert : $ksert) a été débarassé de ses virus, merci donc de bien vouloir lui remettre le réseau !<br><br>".
				"Très Cordialement<br>".
				"Le BR<br>"  ;
				couriel(ROOT_ID,"[Virus] L'ordinateur de $nom $prenom ($promo) est clean !",$contenu,WINDOWS_ID);
			}
			// on le prends en compte....
			$DB_admin->query("UPDATE infections SET solved='2' WHERE id='{$temp[1]}'");
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
	}
	
	$DB_admin->query("SELECT i.ip,i.date,i.date+10-CURDATE(),i.solved,e.login,e.eleve_id,l.nom,i.id FROM prises as p LEFT JOIN trombino.eleves as e ON e.piece_id=p.piece_id LEFT JOIN liste_virus as l ON l.port=i.port INNER JOIN infections as i ON p.ip=i.ip WHERE 1 ORDER BY i.ip, i.solved, l.nom");
?>
	<liste id="liste_virus" selectionnable="non" action="admin/nettoyer_virus.php">
		<entete id="ip" titre="IP"/>
		<entete id="login" titre="login"/>
		<entete id="date" titre="Depuis le"/>
		<entete id="statut" titre="Statut"/>
		<entete id="nomv" titre="Nom du virus"/>
		<entete id="nettoyer" titre=""/>
		<entete id="prevenir" titre=""/>
<?
	while(list($ip,$date,$rebour,$solved,$login,$eleve_id,$nomv,$id)= $DB_admin->next_row()){
		$statut="";
		if ($solved==0){
			$statut="Non signalé";
		}elseif ($solved==1){
			$statut="Signalé";
		}elseif ($solved==2){
			$statut="Résolu";
		}elseif ($solved==3){
			$statut="Réseau coupé";
		}
		echo "\t\t<element id=\"$id\">\n";
			echo "\t\t\t<colonne id=\"ip\">$ip</colonne>\n";
			echo "\t\t\t<colonne id=\"login\">$login</colonne>\n";
			echo "\t\t\t<colonne id=\"date\">".preg_replace('/^(.{4})-(.{2})-(.{2})$/','$3-$2-$1', $date)."</colonne>\n";
			echo "\t\t\t<colonne id=\"statut\">$statut</colonne>\n";
			echo "\t\t\t<colonne id=\"nomv\">$nomv</colonne>\n";
?>
			<colonne id="nettoyer">
<?
		if ($solved!=2){
?>
				<bouton titre="Nettoyer" id="suppr_<?echo "$id";?>_<?echo "$eleve_id";?>_<?echo "$nomv";?>_<?echo "$solved";?>_<?echo "$ip";?>" onClick="return window.confirm('Vous déclarez que ce virus a été éradiqué chez la personne considérée ?')"/>
<?
		}
?>
			</colonne>
			<colonne id="nettoyer">
<?
		if($rebour<5 && $rebour>-1 && $solved==0){
?>
				<bouton titre="Prévenir" id="prev_<?echo "$date";?>_<?echo "$eleve_id";?>_<?echo "$nomv";?>_<?echo "$rebour";?>_<?echo "$ip";?>_<?echo "$id";?>" onClick="return window.confirm('Voulez vous prévenir par mail cette personne quelle est infectée ?')"/>
<?
		}
?>
			</colonne>
<?
		echo "\t\t</element>\n";
	}
?>
	</liste>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>