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
	Pour faire peur aux gens qui ont des virus...
	
	$Log$
	Revision 1.1  2005/04/13 13:58:16  dei
	Voil� qui devrait faire peur � certains
	Bas� sur le script de fruneau module sur la page principale + page d'admin...

	
*/
// En-tetes
set_time_limit(0) ;

require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin')&&!verifie_permission('windows'))
	acces_interdit();

// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="nettoyer_virus" titre="Frankiz : gestion des virus">
<h2>Liste des personnes infect�es ou ayant eu un virus.</h2>
<note>Cette page sert � signaler qu'un virus detect� a bien �t� enlev� de l'ordinateur sur l'ip consid�r�e, apr�s s'en �tre assur� !</note>
<note>Les virus list�s ici sont ceux les plus susceptibles de pourrir le cache de la matrice.</note>
<?
	foreach ($_POST AS $keys => $val){
		$temp = explode("_",$keys) ;
		if ($temp[0] == "suppr") {
			$DB_admin->query("UPDATE infections SET solved='2' WHERE id='{$temp[1]}'");
			echo "<note> Le virus est consid�r� comme enlev� de l'odinateur. On le signale par mail � l'utilisateur.</note>";
			$contenu="Nous avons bien pris en compte la suppression du virus $temp[3] de ton ordinateur.<br><br>".
			"Nous te rappellons qu'il est de ta responsabilit� d'assurer la s�curit� de ton pc. Si tu ne sais pas comment faire utilise le domaine windows, il est l� pour �a. Tu trouveras tout les renseignements n�cessaires dans l'infoBR.<br>".
			"N'h�sites pas � nous signaler tout probl�me !<br><br>".
			"Tr�s Cordialement<br>".
			"Le BR<br>"  ;
			couriel($temp[2],"[Virus] Suppression de $temp[3]",$contenu,WINDOWS_ID);
		}
	}
	
	$DB_admin->query("SELECT i.ip,i.date,i.solved,e.login,e.eleve_id,l.nom,i.id FROM prises as p LEFT JOIN trombino.eleves as e ON e.piece_id=p.piece_id LEFT JOIN liste_virus as l ON l.port=i.port INNER JOIN infections as i ON p.ip=i.ip WHERE 1 ORDER BY i.ip, i.solved, l.nom");
?>
	<liste id="liste_virus" selectionnable="non" action="admin/nettoyer_virus.php">
		<entete id="ip" titre="IP"/>
		<entete id="login" titre="login"/>
		<entete id="date" titre="Depuis le"/>
		<entete id="statut" titre="Statut"/>
		<entete id="nomv" titre="Nom du virus"/>
		<entete id="nettoyer" titre=""/>
<?
	$num=0;
	while(list($ip,$date,$solved,$login,$eleve_id,$nomv,$id)= $DB_admin->next_row()){
		$statut="";
		if ($solved==0){
			$statut="Non signal�";
		}elseif ($solved==1){
			$statut="Signal�";
		}elseif ($solved==2){
			$statut="R�solu";
		}elseif ($solved==3){
			$statut="R�seau coup�";
		}
		echo "\t\t<element id=\"$num\">\n";
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
				<bouton titre="Nettoyer" id="suppr_<?echo "$id";?>_<?echo "$eleve_id";?>_<?echo "$nomv";?>" onClick="return window.confirm('Vous d�clarez que ce virus a �t� �radiqu� chez la personne consid�r�e ?')"/>
<?
		}
?>
			</colonne>
<?
		echo "\t\t</element>\n";
		$num=$num+1;
	}
?>
	</liste>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>