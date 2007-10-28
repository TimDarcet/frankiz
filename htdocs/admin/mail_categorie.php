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
	Page d'envoi dee mail par catégories
	
	$Id$

*/
// En-tetes
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_COOKIE);

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";
require_once BASE_LOCAL."/include/wiki.inc.php";

$DB_trombino->query("SELECT eleve_id,nom,prenom,surnom,mail,login,promo FROM eleves WHERE eleve_id='".$_SESSION['uid']."'");
list($eleve_id,$nom,$prenom,$surnom,$mail,$login,$promo) = $DB_trombino->next_row();

?>
<page id="admin_mailcategorie" titre="Frankiz : Envoi des mails par catégories">
<?php
if (!isset($_POST['envoie'])||isset($_POST['continuer'])) {
?>
	<formulaire id="mail_categorie" titre="Mail par catégorie" action="admin/mail_categorie.php">
		<note>
			Le texte du mail promo utilise le format wiki rappelé en bas de la page et décrit dans l'<lien url="helpwiki.php" titre="aide wiki"/><br/>
		</note>
		<choix titre="Promo" id="promo" type="combo" valeur="">
			<option titre="Sur le campus" id="campus"/>
			<option titre="Toutes" id="" />

<?php
			$DB_trombino->query("SELECT DISTINCT promo FROM eleves ORDER BY promo DESC");
			while( list($promo) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$promo\" id=\"$promo\"/>\n";
?>

		</choix>
		
		<choix titre="Section" id="section" type="combo" valeur="">
			<option titre="Toutes" id=""/>
<?php
			$DB_trombino->query("SELECT section_id,nom FROM sections ORDER BY nom ASC");
			while( list($section_id,$section_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$section_nom\" id=\"$section_id\"/>\n";
?>
		</choix>
			
		<choix titre="Binet" id="binet" type="combo" valeur="">
			<option titre="Tous" id=""/>
<?php
			$DB_trombino->query("SELECT binet_id,nom FROM binets ORDER BY nom ASC");
			while( list($binet_id,$binet_nom) = $DB_trombino->next_row() )
				echo "\t\t\t<option titre=\"$binet_nom\" id=\"$binet_id\"/>\n";
?>
		</choix>
		
		<choix titre="Postes" id="postes" type="combo" valeur="">
			<option titre="" id=""/>
			<option titre="Prez" id="prez"/>
			<option titre="Webmestre" id="web"/>
		</choix>
		<note>Le numéro de casert est auto-complété par la droite, ce qui permet de selectionner un étage, un batiment...</note>
		<champ titre="Casert" id="casert" valeur="" />
		
		<champ titre="Sujet" id="sujet" valeur="<?php if (isset($_POST['sujet'])) echo $_POST['sujet']?>" />
		<champ titre="From" id="from" valeur="<?php if (isset($_POST['from'])){ echo $_POST['from'] ;} else {echo "$prenom $nom &lt;".$mail."&gt;" ;} ?>" />
		<zonetext titre="Mail" id="mail" type="grand"><?php if (isset($_POST['mail'])) echo $_POST['mail']?></zonetext>
		<bouton titre="Tester" id="upload"/>
		<bouton titre="Envoyer" id="envoie"  onClick="return window.confirm('Voulez vous vraiment envoyer ce mail ?')"/>
	</formulaire>
	<?php affiche_syntaxe_wiki() ?>
<?php
//==================================================
//=
//= Permet de visualiser son mail avant de l'envoyer
//=
//==================================================
	if (isset($_POST['upload'])) {
?>
		<cadre  titre="Mail Promo : <?php if (isset($_POST['sujet'])) echo $_POST['sujet']?>" >
			<?php echo wikiVersXML($_POST['mail']) ; ?>
		</cadre>
<?php
	}
//==================================================
//=
//= Envoi du mail
//=
//==================================================
} elseif($_POST['sujet']==""){
?>
	<formulaire id="mail_categorie" titre="Mail par catégorie" action="admin/mail_categorie.php">"
		<note>Il faut mettre un sujet !</note>
<?php
		if(isset($_POST['mail'])){ 
			echo "<hidden id=\"mail\" valeur=\"".$_POST['mail']."\" />"; 
		}
		if(isset($_POST['from'])){ 
			echo "<hidden id=\"from\" valeur=\"".$_POST['from']."\" />"; 
		}  
?>
		<bouton titre="Continuer" id="continuer"/>
	</formulaire>
<?php
} elseif($_POST['from']==""){
?>
	<formulaire id="mail_categorie" titre="Mail par catégorie" action="admin/mail_categorie.php">"
		<note>Il faut mettre un expéditeur !</note>
<?php
		if(isset($_POST['mail'])){ 
			echo "<hidden id=\"mail\" valeur=\"".$_POST['mail']."\" />"; 
		}
		if(isset($_POST['sujet'])){ 
			echo "<hidden id=\"sujet\" valeur=\"".$_POST['sujet']."\" />"; 
		}  
?>
		<bouton titre="Continuer" id="continuer"/>
	</formulaire>
<?php
} elseif($_POST['mail']==""){
?>
	<formulaire id="mail_categorie" titre="Mail par catégorie" action="admin/mail_categorie.php">
		<note>Le corps du mail ne doit pas être vide !</note>
<?php
		if(isset($_POST['sujet'])){ 
			echo "<hidden id=\"sujet\" valeur=\"".$_POST['sujet']."\" />";
		}
		if(isset($_POST['from'])){ 
			echo "<hidden id=\"from\" valeur=\"".$_POST['from']."\" />"; 
		}  
?>
		<bouton titre="Continuer" id="continuer"/>
	</formulaire>
<?php
} else {
	//envoi du mail...
	//on vérifie qu'un champ au moins est rempli, autre que "promo"
	if($_POST['section']!=""||$_POST['casert']!=""||$_POST['binet']!=""||$_POST['postes']!=""){
		//construction requete
		$req="SELECT e.eleve_id,e.nom,e.prenom,e.promo FROM eleves as e LEFT JOIN frankiz2.compte_frankiz as cpt ON e.eleve_id=cpt.eleve_id";
		$log=" envoyé un mail groupé";
		if(isset($_POST['binet']) && $_POST['binet']!=""){
			$req=$req." LEFT JOIN membres as m ON e.eleve_id=m.eleve_id WHERE 1 AND m.binet_id='{$_POST['binet']}'";
			$DB_trombino->query("SELECT nom FROM binets WHERE binet_id='{$_POST['binet']}' LIMIT 0,1");
			list($nom_binet)=$DB_trombino->next_row();
			$log=$log." au Binet $nom_binet";
		} else {
			$req=$req."  WHERE 1";
		}
		if(isset($_POST['section']) && $_POST['section']!=""){
			$req=$req." AND e.section_id='{$_POST['section']}'";
			$DB_trombino->query("SELECT nom FROM sections WHERE section_id='{$_POST['section']}' LIMIT 0,1");
			list($nom_section)=$DB_trombino->next_row();
			$log=$log." à la section $nom_section";
		}
		if(isset($_POST['casert']) && $_POST['casert']!=""){
			$req=$req." AND e.piece_id LIKE '{$_POST['casert']}%'";
			$log=$log." aux kserts {$_POST['casert']}";
		}
		if(isset($_POST['postes']) && $_POST['postes']!=""){
			if($_POST['postes']=="prez"){
				$req=$req." AND cpt.perms LIKE '%prez_%'";
				$log=$log." aux prez des Binets";
			} elseif ($_POST['postes']=="web"){
				$req=$req." AND cpt.perms LIKE '%webmestres_%'";
				$log=$log." aux webmestres des Binets";
			}
		}
		if(isset($_POST['promo']) && $_POST['promo']!=""){
			if($_POST['promo']=="campus"){
				$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
				list($promo_temp) = $DB_web->next_row() ;
				$promo_temp_=$promo_temp-1;
				$req=$req." AND (e.promo='$promo_temp' OR e.promo='$promo_temp_')";
				$log=$log." aux promos sur le campus";
			} else {
				$req=$req." AND e.promo='{$_POST['promo']}'";
				$log=$log." à la promo {$_POST['promo']}";
			}
		}
		//log de l'action
		log_admin($_SESSION['uid'],$log) ;
		
		$from = $_POST['from'] ;
		$DB_trombino->query("$req");
		$cnt = 0 ;
		$mail_contenu = wikiVersXML($mail,true) ;
		$titre_mail = $_POST['sujet'];
		// On crée le fichier de log qui va bien
		/*$fich_log = BASE_DATA."mailcategorie/mail.log.{$_POST['id']}"; 
		touch($fich_log) ;
		$from = html_entity_decode(base64_decode($_POST['sender'])) ;
		exec("echo \"".$mail_contenu."\" >>".$fich_log) ;*/
?>
<formulaire id="mail_categorie" titre="Mail par catégorie" action="admin/mail_categorie.php">"
<?php
		while(list($eleve_id,$nom,$prenom,$promo)=$DB_trombino->next_row()){
			$DB_trombino->push_result() ;
			//couriel($eleve_id,"".$titre_mail,$mail_contenu, STRINGMAIL_ID, $from) ;
			$DB_trombino->pop_result() ;
			/*print("Envoi à $nom $prenom ($promo) [".($cnt+1)."]<br>") ;
			flush() ;*/
			$cnt ++ ;
			//exec("echo \"Mail envoyé à $nom $prenom ($eleve_id)\n\" >>".$fich_log) ;
			usleep(100000); // Attends 100 millisecondes
			echo "<note>Mail envoyé à : $prenom $nom ($promo)</note>";
		}
?>
		<bouton titre="Continuer" id="continuer"/>
	</formulaire>
<?php
	}else{
		echo "<note>Ceci ne sert pas à envoyer de mails promos !</note>";
	}
}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
