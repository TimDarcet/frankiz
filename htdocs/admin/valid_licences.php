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
	Revision 1.15  2005/01/23 21:37:26  dei
	voilà version finale ! (j'espère)

	Revision 1.14  2005/01/23 21:05:02  dei
	la ça devrait mieux marcher
	
	Revision 1.13  2005/01/23 20:28:48  dei
	correction bug attribution automatique licence
	
	Revision 1.12  2005/01/21 21:44:24  dei
	Ajout d'une fonction pour rentrer les licences en vrac depuis un
	fichier, séparées par des ";"
	
	Revision 1.11  2005/01/20 12:47:22  dei
	rajout du warning dans la liste
	
	Revision 1.10  2005/01/20 12:32:19  dei
	ajout de la gestion des clés libres
	
	Revision 1.9  2005/01/19 19:28:58  dei
	Corrections des recherches sql qui pouvaient vider la base par erreur...
	
	Revision 1.8  2005/01/19 12:11:31  dei
	debug gestion d'ajout de licence sur demande
	
	Revision 1.7  2005/01/18 22:40:44  dei
	ajout d'une fonction pour mettre des clés dans la base à la main
	
	Revision 1.6  2005/01/18 21:38:41  dei
	ajout de fonctionnalité de recherche et correction test des clés
	
	Revision 1.5  2005/01/18 12:52:31  pico
	Page d'admin windows (debug)
	
	Revision 1.4  2005/01/18 12:25:09  dei
	ajout test du formatage de la clé
	ajout interface de recherche dans la base des clés
	
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
//on teste si la cle entrée à la main a une forme standard...
function test_cle($key){
	$key=explode("-",$key);
	if(sizeof($key)==5){
		for($i=0;$i<sizeof($key);$i++){
			if(!ereg('^[[:alnum:]]+$', $key[$i])){
				echo "<warning>Cette clé n'a pas le bon formatage !</warning>";
				return false;
			}
		}
		return true;
	}
	echo "<warning>$key : cette clé n'a pas le bon formatage !</warning>";
	return false;
}

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
	if ($temp[0] == "ok"){
		$temp2 = "ajout_licence_".$temp[1] ;
		if(test_cle($_POST[$temp2])) {
			//on vérifie que la requete existe encore...
			$DB_msdnaa->query("SELECT 0 FROM valid_licence WHERE eleve_id='{$temp[1]}'");
			if($DB_msdnaa->num_rows()!=0){
				//on cherche ds les clés attribuées au logiciel..
				$DB_msdnaa->query("SELECT 0 FROM cles_$temp[2] WHERE cle='{$_POST[$temp2]}'");
				// S'il n'y a aucune entrée avec cette licence dans la base
				if($DB_msdnaa->num_rows()==0){
					echo "<note>c'est bon</note>";
					$DB_msdnaa->query("DELETE FROM valid_licence WHERE eleve_id='{$temp[1]}'");
					$DB_msdnaa->query("DELETE FROM cles_libres WHERE cle='{$_POST[$temp2]}' AND logiciel='{$temp[2]}'");
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
				}else{
					echo "<warning>La clé ".$_POST[$temp2]." existe déjà et est attribuée. Elle a été supprimée de la base des clés libres.</warning>";
					$DB_msdnaa->query("DELETE FROM cles_libres WHERE cle='{$_POST[$temp2]}' AND logiciel='{$temp[2]}'");
				}
			}else{
				echo "<warning>La demande a déjà été traitée par un autre administrateur du systeme</warning>";
			}
		}
		// S'il y  a deja une entrée comme celle demandé dans la base !
		else {
			echo "<warning>IMPOSSIBLE D'ATTRIBUER CETTE LICENCE. L'utilisateur en possède déjà une.</warning>" ;
		}
	}
}
$DB_msdnaa->query("UNLOCK TABLES");
?>
<h2>Liste des personnes demandant une licence</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_licences.php">
		<entete id="logiciel" titre="Logiciel"/>
		<entete id="eleve" titre="Élève"/>
		<entete id="raison" titre="Raison"/>
		<entete id="licence" titre="licence"/>
<?php
		$DB_msdnaa->query("SELECT v.raison,v.logiciel,e.nom,e.prenom,e.eleve_id FROM valid_licence as v LEFT JOIN trombino.eleves as e ON e.eleve_id=v.eleve_id");
		while(list($raison,$logiciel,$nom,$prenom,$eleve_id) = $DB_msdnaa->next_row()) {
			$DB_msdnaa->push_result();
			$DB_msdnaa->query("SELECT cle FROM cles_libres WHERE logiciel='{$logiciel}' LIMIT 1");
			list($cle_libre)=$DB_msdnaa->next_row();
			$DB_msdnaa->pop_result();
			if($cle_libre==""){
				echo "<warning>Plus de clés libres pour $logiciel</warning>";
			}
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
						<champ titre="" id="ajout_licence_<? echo $eleve_id ;?>" valeur="<? echo $cle_libre ; ?>"/>
						<bouton titre="Ok" id="ok_<? echo $eleve_id ;?>_<? echo $logiciel ;?>" />
						<bouton titre="Refuser" id="vtff_<? echo $eleve_id ;?>_<? echo $logiciel ;?>" onClick="return window.confirm('Voulez vous vraiment ne pas valider cette licence ?')"/>
					</p>
				</colonne>
			</element>
<?
		}
?>
	</liste>
<?php
if(isset($_POST['chercher'])){
	$req="SELECT e.nom,e.prenom,e.login,e.eleve_id,e.promo,v.cle,v.attrib FROM trombino.eleves as e LEFT JOIN cles_".$_POST['logiciel']." as v USING(eleve_id) WHERE 1";
	if($_POST['login']!=""){
		$req=$req." AND e.login LIKE '%{$_POST['login']}%'";
	}
	if($_POST['cle']!=''){
		$req=$req." AND v.cle LIKE '%{$_POST['cle']}%'";
	}
	if(isset($_POST['promo'])){
			$req=$req." AND e.promo='{$_POST['promo']}'";
	}
	$DB_msdnaa->query($req);
?>
	<h2>Résultats de la recherche</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_licences.php">
		<entete id="logiciel" titre="Logiciel"/>
		<entete id="promo" titre="Promo"/>
		<entete id="eleve" titre="Élève"/>
		<entete id="login" titre="Login"/>
		<entete id="licence" titre="Licence"/>
		<entete id="attrib" titre="Attribuée"/>
<?
	while(list($nom,$prenom,$login,$eleve_id,$promo,$cle,$attrib) = $DB_msdnaa->next_row()){
?>
		<element id="<? echo $eleve_id ;?>">
				<colonne id="logiciel"><? echo  $_POST['logiciel']; ?></colonne>
				<colonne id="promo"><? echo $_POST['promo'] ?></colonne>
				<colonne id="eleve"><? echo "$nom $prenom" ?></colonne>
				<colonne id="login"><? echo "$login" ?></colonne>
				<colonne id="licence"><? echo "$cle" ?></colonne>
				<colonne id="attrib"><? if($attrib!="0" && $attrib!=""){echo "oui";}else{echo "non";} ?></colonne>
		</element>
<?php
	}
	echo "</liste>";
}

if((isset($_POST['ajout']))&&test_cle($_POST['cle'])&&$_POST['login']!=""){
	$DB_trombino->query("SELECT eleve_id FROM eleves WHERE login='{$_POST['login']}' AND promo='{$_POST['promo']}' LIMIT 1");
	list($eleve_id)=$DB_trombino->next_row();
	$DB_msdnaa->query("SELECT 0 FROM cles_{$_POST['logiciel']} WHERE cle='{$_POST['cle']}'");
	if($DB_msdnaa->num_rows()==0){
		$DB_msdnaa->query("INSERT cles_{$_POST['logiciel']} SET eleve_id='{$eleve_id}', attrib='0', cle='{$_POST['cle']}'");
		echo "<note>La clé a bien été ajoutée</note>";
	} else {
		if($DB_msdnaa->num_rows()!=0){
			$DB_msdnaa->query("UPDATE cles_{$_POST['logiciel']} SET attrib='0', cle='{$_POST['cle']}' WHERE eleve_id='{$eleve_id}' LIMIT 1");
			echo "<note>La clé a bien été mise à jour</note>";
		}
	}
}
if(isset($_POST['effacer'])&&$_POST['login']!=""){
	$DB_trombino->query("SELECT eleve_id FROM eleves WHERE login='{$_POST['login']}' AND promo='{$_POST['promo']}' LIMIT 1");
	list($eleve_id)=$DB_trombino->next_row();
	$DB_msdnaa->query("SELECT 0 FROM cles_{$_POST['logiciel']} WHERE eleve_id='{$eleve_id}'");
	if($DB_msdnaa->num_rows()!=0){
		$DB_msdnaa->query("DELETE FROM cles_{$_POST['logiciel']} WHERE eleve_id='{$eleve_id}' LIMIT 1");
		echo "<note>La clé a bien été supprimée</note>";
	}
}

?>
<h2>Recherche/Ajout/Suppression d'une clé dans la base</h2>
	<formulaire id="ajout" action="admin/valid_licences.php">
		<note>Cette fonction sert seulement à corriger un oubli ou une erreur !</note>
		<champ titre="Login poly" id="login" valeur=""/>
		<choix titre="Promo" id="promo" type="combo" valeur="">
<?php
			$DB_trombino->query("SELECT DISTINCT promo FROM eleves ORDER BY promo DESC");
			while( list($promo) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$promo\" id=\"$promo\"/>\n";
?>
		</choix>
		<champ titre="Licence" id="cle" valeur=""/>
		<choix titre="Logiciel" id="logiciel" type="combo" valeur="">
			<option titre="Windows XP Pro" id="winxp"/>
			<option titre="Windows 2003 Serveur" id="2k3serv"/>
		</choix>
		<bouton id='chercher' titre='Rechercher'/>
		<bouton id='ajout' titre="Ajouter"/>
		<bouton id='effacer' titre="Supprimer" onClick="return window.confirm('Es-tu sûr de vouloir supprimer cette licence ?')"/>
	</formulaire>
<?php

if(isset($_POST['update'])&&is_readable($_FILES['file']['tmp_name'])){
	$file=fopen($_FILES['file']['tmp_name'],"r");
	$ligne="";
	while(!feof($file)){
		$ligne=$ligne.fgets($file,255);
	}
	fclose($file); 
	$ligne=explode(";",$ligne);
	$nb_cle=sizeof($ligne);
	$DB_trombino->query("SELECT eleve_id FROM eleves WHERE promo='{$_POST['promo']}'");
	$nb_eleves=$DB_trombino->num_rows();
	$nb_diff=0;
	list($eleve_id)=$DB_trombino->next_row();
	$nb_min=min($nb_cle,$nb_eleves);
	$nb_max=max($nb_cle,$nb_eleves);
	$i=0;
	while($i<$nb_min){
		$cle_tmp=trim($ligne[$i]);
		if(test_cle($cle_tmp)){
			$DB_msdnaa->query("SELECT 0 FROM cles_{$_POST['logiciel']} WHERE cle='{$cle_tmp}' UNION SELECT 0 FROM cles_libres WHERE cle='{$cle_tmp}'");
			if($DB_msdnaa->num_rows()==0){
				$DB_msdnaa->query("SELECT cle FROM cles_{$_POST['logiciel']} WHERE eleve_id='{$eleve_id}'");
				list($cle_test)=$DB_msdnaa->next_row();
				if($cle_test==""){
					$DB_msdnaa->query("INSERT cles_{$_POST['logiciel']} SET eleve_id='{$eleve_id}', attrib='0', cle='{$cle_tmp}'");
					$i++;
				}
				list($eleve_id)=$DB_trombino->next_row();
			}else{
				echo "<warning>La clé $cle_tmp existe déjà dans les bases !</warning>";
				$i++;
			}
		}else{
			$i++;
		}
	}
	if($nb_cle<$nb_eleves){
		$DB_msdnaa->query("SELECT cle FROM cles_libres WHERE logiciel='{$_POST['logiciel']}'");
		while(list($cle_tmp)=$DB_msdnaa->next_row()){
			$DB_msdnaa->push_result();
			$DB_msdnaa->query("SELECT 0 FROM cles_{$_POST['logiciel']} WHERE cle='{$cle_tmp}'");
			if($DB_msdnaa->num_rows()==0){
				$DB_msdnaa->query("SELECT cle FROM cles_{$_POST['logiciel']} WHERE eleve_id='{$eleve_id}'");
				list($cle_test)=$DB_msdnaa->next_row();
				if($cle_test==""){
					$DB_msdnaa->query("INSERT cles_{$_POST['logiciel']} SET eleve_id='{$eleve_id}', attrib='0', cle='{$cle_tmp}'");
					$DB_msdnaa->query("DELETE FROM cles_libres WHERE cle='{$cle_tmp}' AND logiciel='{$_POST['logiciel']}'");
				}
				list($eleve_id)=$DB_trombino->next_row();
			}
			$DB_msdnaa->pop_result();
		}
	}else{
		for($i=$nb_min ; $i<$nb_max ; $i++){
			$cle_tmp=trim($ligne[$i]);
			if(test_cle($cle_tmp)){
				$DB_msdnaa->query("SELECT 0 FROM cles_{$_POST['logiciel']} WHERE cle='{$cle_tmp}' UNION SELECT 0 FROM cles_libres WHERE cle='{$cle_tmp}'");
				if($DB_msdnaa->num_rows()==0){
					$DB_msdnaa->query("INSERT cles_libres SET logiciel='{$_POST['logiciel']}', cle='{$cle_tmp}'");
				}else{
					echo "<warning>La clé $cle_tmp existe déjà dans les bases !</warning>";
				}
			}
		}
	}
}
?>
<h2>Ajout des clés pour une promo dans la base</h2>
	<formulaire id="ajout" action="admin/valid_licences.php">
		<choix titre="Promo" id="promo" type="combo" valeur="">
<?php
			$DB_trombino->query("SELECT DISTINCT promo FROM eleves ORDER BY promo DESC");
			while( list($promo) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$promo\" id=\"$promo\"/>\n";
?>
		</choix>
		<fichier id="file" titre="Nouvelles Licences" taille="200000"/>
		<choix titre="Logiciel" id="logiciel" type="combo" valeur="">
			<option titre="Windows XP Pro" id="winxp"/>
		</choix>
		<bouton id='update' titre="Ajouter"/>
	</formulaire>
</page>
<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>