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
	Page qui permet l'administartion des licences windows.
	
	$Log$
	Revision 1.4  2005/01/18 12:25:09  dei
	ajout test du formatage de la cl�
	ajout interface de recherche dans la base des cl�s

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

// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin'))
	acces_interdit();
//
//
// G�n�ration de la page
//===============	
require_once BASE_LOCAL."/include/page_header.inc.php";	
?>
<page id="valid_licences" titre="Frankiz : gestion des licences Microsoft">
<?
//on teste si la cle entr�e � la main a une forme standard...
function test_cle($key){
	$key=explode("-",$key);
	if(sizeof($key==5){
		for($i=0;$i<5;$i++){
			if(!ereg("(^[A-Z0-9]{5})",$str)){
				return false;
			}
		}
		return true;
	} else {
		return false;
	}
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
					"Nous sommes d�sol�s de pas pouvoir t'attribuer une licence pour la raison suivante�:<br>".
					$_POST[$bla]."<br>".
					"Il y a certainement une autre fa�on de proc�der pour atteindre ton but.<br>".
					"<br>" .
					"Tr�s Cordialement<br>" .
					"Le BR<br>"  ;
	
		couriel($temp[1],"[Frankiz] Ta demande a �t� refus�e ",$contenu,WINDOWS_ID);
		echo "<commentaire>Envoie d'un mail. On pr�vient l'utilisateur que sa demande n'a pas �t� accept�e.</commentaire>" ;
	}
	// On accepte la demande de licence suppl�mentaire
	//===========================
	if ($temp[0] == "ok" && test_cle($_POST[$temp2])) {
		$temp2 = "ajout_licence_".$temp[1] ;
		//on cherche ds les cl�s attribu�es au logiciel...
		$DB_msdnaa->query("SELECT 0 FROM cles_$temp[2] WHERE eleve_id='{$temp[1]}'");
		// S'il n'y a aucune entr�e avec cette licence dans la base
		if($DB_msdnaa->num_rows()==0){
			$DB_msdnaa->query("DELETE FROM valid_licence WHERE eleve_id='{$temp[1]}'");
			//on l'ajoute � la base concern�e...
			$DB_msdnaa->query("INSERT cles_$temp[2] SET eleve_id='{$temp[1]}', attrib='1', cle='$_POST[$temp2]'");
			
			$contenu = "Bonjour, <br><br>".
						"Nous t'avons attribu� la licence suivante�:<br>".
						$_POST[$temp2]."<br>".
						"<br>" .
						"Tr�s Cordialement<br>" .
						"Le BR<br>";
		
			couriel($temp[1],"[Frankiz] Ta demande a �t� accept�e",$contenu,WINDOWS_ID);
			echo "<commentaire>Envoie d'un mail. On pr�vient l'utilisateur que sa demande a �t� accept�e (nouvelle licence�: ".$_POST[$temp2].")</commentaire>" ;
		}
		// S'il y  a deja une entr�e comme celle demand� dans la base !
		else {
			echo "<warning>IMPOSSIBLE D'ATTRIBUER CETTE LICENCE. L'utilisateur en poss�de d�j� une.</warning>" ;
		}
	}
	if(test_cle($_POST[$temp2])){
		echo "<warning>La Cl� entr�e n'a pas un formatage standard !</warning>" ;
	}
}
$DB_msdnaa->query("UNLOCK TABLES");
?>
<h2>Liste des personnes demandant</h2>
	<liste id="liste" selectionnable="non" action="admin/valid_licences.php">
		<entete id="logiciel" titre="Logiciel"/>
		<entete id="eleve" titre="�l�ve"/>
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
						<bouton titre="Refuser" id="vtff_<? echo $eleve_id ;?>_<? echo $logiciel ;?>" onClick="return window.confirm('Voulez vous vraiment ne pas valider cette licence ?')"/>
					</p>
				</colonne>
			</element>
<?
		}
?>
	</liste>
<?php
if(isset($_POST['chercher']){
	$req="SELECT e.nom,e.prenom,e.login,e.eleve_id,v.logiciel,v.cle FROM trombino.eleves as e LEFT JOIN valid_".$_POST['logiciel']." as v USING(eleves_id) WHERE";
	if(isset(($_POST['login'])){
		$req=$req."login=".$_POST['login'];
		if(isset($_POST['licence'])){
			$req=$req." AND cle=".$_POST['licence'];
		}
	}else{
		if(isset($_POST['licence'])){
			$req=$req."cle=".$_POST['licence'];
	}
	$DB_msdnaa->query($req);
	while(list($nom,$prenom,$login,$eleve_id,$logiciel,$cle) = $DB_msdnaa->next_row()){
?>
		<element id="<? echo $eleve_id ;?>">
				<colonne id="logiciel"><? echo "$logiciel" ?></colonne>
				<colonne id="eleve"><? echo "$nom $prenom" ?></colonne>
				<colonne id="login"><? echo "$login" ?></colonne>
				<colonne id="licence"><? echo "$cle" ?></colonne>
		</element>
<?
	}
}
?>
<h2>Rechercher un utilisateur dans la base</h2>
	<formulaire id="chercher" action="admin/valid_licences.php">
	<champ titre="Login poly" id="login" valeur="" />
	<champ titre="Licence" id="licence" valeur="" />
	<choix titre="Logiciel" id="logiciel" type="combo" valeur="">
		<option titre="Windows XP Pro" id="winxp"/>
		<option titre="Windows 2003 Serveur" id="2k3serv"/>
	</choix>
	<bouton id='chercher' titre='Rechercher'/>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>