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
	Page qui permet l'administartion des licences windows.
	
	$Log$
	Revision 1.3  2005/01/17 23:46:28  pico
	Bug fix

	Revision 1.2  2005/01/17 23:15:37  pico
	debug pages de dei
	
	Revision 1.1  2005/01/17 22:12:01  dei
	page d'administration et de gestion de l'ajout des nouvelles
	licences krosoft
	

*/
set_time_limit(0) ;

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	acces_interdit();
//
//
// Génération de la page
//===============	
require_once BASE_LOCAL."/include/page_header.inc.php";	
?>
<page id="valid_licences" titre="Frankiz : gestion des licences Microsoft">
<?
$DB_msdnaa->query("LOCK TABLES valid_licence WRITE ,cles_winxp WRITE,cles_2k3serv WRITE");
$DB_msdnaa->query("SET AUTOCOMMIT=0");

foreach ($_POST AS $keys => $val){
$temp = explode("_",$keys) ;
	
	// On refuse la demande de licence
	//==========================
	if ($temp[0] == "vtff") {
		$DB_msdnaa->query("DELETE FROM valid_licence WHERE eleve_id='{$temp[1]}'");
		$bla = "refus_".$temp[1] ;
		$contenu = "Bonjour, <br><br>".
					"Nous sommes désolés de pas pouvoir t'attribuer une licence pour la raison suivante :<br>".
					$_POST[$bla]."<br>".
					"Il y a certainement une autre façon de procéder pour atteindre ton but.<br>".
					"<br>" .
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
	
		couriel($temp[1],"[Frankiz] Ta demande a été refusée ",$contenu,WINDOWS_ID);
		echo "<commentaire>Envoie d'un mail. On prévient l'utilisateur que sa demande n'a pas été acceptée.</commentaire>" ;
	}
	// On accepte la demande de licence supplémentaire
	//===========================
	if ($temp[0] == "ok") {
		$temp2 = "ajout_licence_".$temp[1] ;
		//on cherche ds les clés attribuées au logiciel...
		$DB_msdnaa->query("SELECT 0 FROM cles_$temp[2] WHERE eleve_id='{$temp[1]}'");
		// S'il n'y a aucune entrée avec cette licence dans la base
		if($DB_msdnaa->num_rows()==0){
			$DB_msdnaa->query("DELETE FROM valid_licence WHERE eleve_id='{$temp[1]}'");
			//on l'ajoute à la base concernée...
			$DB_msdnaa->query("INSERT cles_$temp[2] SET eleve_id='{$temp[1]}', attrib='1', cle='$_POST[$temp2]'");
			
			$contenu = "Bonjour, <br><br>".
						"Nous t'avons attribué la licence suivante :<br>".
						$_POST[$temp2]."<br>".
						"<br>" .
						"Très Cordialement<br>" .
						"Le BR<br>";
		
			couriel($temp[1],"[Frankiz] Ta demande a été acceptée",$contenu,WINDOWS_ID);
			echo "<commentaire>Envoie d'un mail. On prévient l'utilisateur que sa demande a été acceptée (nouvelle licence : ".$_POST[$temp2].")</commentaire>" ;
		}
		// S'il y  a deja une entrée comme celle demandé dans la base !
		else {
			echo "<warning>IMPOSSIBLE D'ATTRIBUER CETTE LICENCE. L'utilisateur en possède déjà une.</warning>" ;
		}
	}
}
$DB_msdnaa->query("UNLOCK TABLES");
?>
<h2>Liste des personnes demandant</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_licences.php">
		<entete id="logiciel" titre="Logiciel"/>
		<entete id="eleve" titre="Élève"/>
		<entete id="raison" titre="Raison"/>
		<entete id="licence" titre="licence"/>
<?php
		$DB_msdnaa->query("SELECT v.raison,v.logiciel,e.nom,e.prenom,e.eleve_id FROM valid_licence as v LEFT JOIN trombino.eleves as e USING(eleve_id)");
		while(list($raison,$logiciel,$nom,$prenom,$eleve_id) = $DB_msdnaa->next_row()) {
?>
			<element id="<? echo $eleve_id ;?>">
				<colonne id="logiciel"><? echo "$logiciel" ?></colonne>
				<colonne id="eleve"><? echo "$nom $prenom" ?></colonne>
				<colonne id="raison">
					<p><textsimple titre="" id="raison_<? echo $eleve_id ;?>" valeur="Raison = <? echo $raison ;?>"/></p>
					<p><textsimple titre="" id="raison2_<? echo $eleve_id ;?>" valeur="Raison si refus :"/></p>
					<zonetext titre="Raison du Refus si refus" id="refus_<? echo $eleve_id ;?>"></zonetext>
				</colonne>
				<colonne id="licence">
					<p>
						<champ titre="" id="ajout_licence_<? echo $eleve_id ;?>" valeur="" /> 
						<bouton titre="Ok" id="ok_<? echo $eleve_id ;?>_<? echo $logiciel ;?>" />
						<bouton titre="Vtff" id="vtff_<? echo $eleve_id ;?>_<? echo $logiciel ;?>" onClick="return window.confirm('Voulez vous vraiment ne pas valider cette licence ?')"/>
					</p>
				</colonne>
			</element>
<?
		}
?>
	</liste>


</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>