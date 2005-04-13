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
	Page qui permet de demander une clé windows
	
	$Log$
	Revision 1.24  2005/04/13 17:10:00  pico
	Passage de tous les fichiers en utf8.

	Revision 1.23  2005/03/16 18:51:25  dei
	bon oui je suis un boulet et alors ?
	
	Revision 1.22  2005/02/16 21:20:42  dei
	lui aussi a changé...
	
	Revision 1.21  2005/02/02 14:31:11  dei
	la recherche de la dernière promo se fait dans la bonne base...
	
	Revision 1.20  2005/01/30 21:57:06  dei
	là ça marchera mieux...
	
	Revision 1.19  2005/01/30 21:35:46  dei
	-----------------------------------------------------------------------
	le probleme de l'attribution des licences est transparent pour
	l'utilisateur
	
	Revision 1.18  2005/01/18 20:52:17  dei
	c'est plus clair comme ça
	
	Revision 1.17  2005/01/18 15:56:06  dei
	l'utilisateur doit donner une raison non vide pour demander des licences
	supplémentaires
	
	Revision 1.16  2005/01/17 23:46:28  pico
	Bug fix
	
	Revision 1.15  2005/01/17 23:25:08  pico
	Suite des corrections dei
	
	Revision 1.14  2005/01/17 23:15:37  pico
	debug pages de dei
	
	Revision 1.13  2005/01/17 22:07:33  dei
	mise a jour de la page, compatible avec admin/admin_licences.php
	
	Revision 1.12  2004/12/17 21:46:16  falco
	un peu de rigueur ne fait pas de mal
	
	Revision 1.11  2004/12/17 21:09:14  dei
	<br/>...
	mopufles!
	
	Revision 1.10  2004/12/17 20:33:54  dei
	Bugfix affichage
	
	Revision 1.9  2004/12/17 20:27:02  pico
	On va éviter une ptite erreur
	
	Revision 1.8  2004/12/17 20:25:20  pico
	Ajout des logs
	
	
*/
	

require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);


// Données sur l'utilisateur
$DB_trombino->query("SELECT eleve_id,eleves.nom,prenom,promo,mail FROM eleves WHERE eleve_id='{$_SESSION['user']->uid}'");
list($eleve_id,$nom,$prenom,$promo,$mail) = $DB_trombino->next_row();

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";
require_once BASE_LOCAL."/include/rss_func.inc.php";

?>
<page id="licences" titre="Frankiz : Les Licences">
<?	
	$log=array('visualstudio' => 'Visual Studio .NET','winxp' => 'Windows XP Professionnel','2k3serv' => 'Windows Serveur 2003','2k3access'=>'Access 2003','2k3onenote'=>'One Note 2003','2k3visiopro'=>'Visio Professionnel 2003','win2k'=>'Windows 2000 Professionnel');
	//on vérifie que la raison n'est pas vide... si elle l'est il se tape tout le formulaire pour recommencer.
	if(isset($_POST['raison'])&&$_POST['raison']==""){
?>
		 <warning>Il faut une raison pour obtenir ces licences !</warning>
<?
	}
	//on vérifie que la demande est faite pour windows xp pro
	if(isset($_POST['accord'])){
		//on regarde si par hasard ce n'est pas une clé admin...
		$DB_msdnaa->query("SELECT cle FROM cles_admin WHERE log='".$_POST['logiciel']."' LIMIT 0,1");
		if($DB_msdnaa->num_rows()!=0){
			list($cle) = $DB_msdnaa->next_row();
			?>
			<commentaire>Ta clé va t'être expédiée sur ta boite mail.</commentaire>
			<?php
				$contenu="La clé qui vous a été attribuée pour ".$log[$_POST['logiciel']]." est : $cle <br><br>".
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
				couriel($eleve_id,"[Frankiz] Demande de licence Microsoft de $nom $prenom X $promo",$contenu,WINDOWS_ID);
				$contenu="La clé demandée par $nom $prenom X $promo pour ".$log[$_POST['logiciel']]." est : $cle <br><br>".
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
				couriel(WINDOWS_ID,"[Frankiz] Demande de licence Microsoft de $nom $prenom X $promo",$contenu,$eleve_id);
		} else {
			//on lance la requête qui va bien pour voir la clé
			$DB_msdnaa->query("SELECT cle,attrib FROM cles_{$_POST['logiciel']} WHERE eleve_id='".$_SESSION['user']->uid."' LIMIT 0,1");
			//on verifie que le demandeur existe dans la base
			if($DB_msdnaa->num_rows()!=0){
				//on a la clé attribuée de manière unique par le BR.
				list($cle,$attrib) = $DB_msdnaa->next_row();
				//si la personne a déjà demandé sa clé...
				if($attrib != 0){
					?>
					<commentaire>Tu as déjà demandé ta clé, elle va t'être ré-expédiée sur ta boite mail.</commentaire>
					<?php
					$contenu="La clé qui vous a déjà été attribuée pour ".$log[$_POST['logiciel']]." est : $cle <br><br>".
						"Très Cordialement<br>" .
						"Le BR<br>"  ;
					//a completer couriel(WEBMESTRE_ID,"[Frankiz] Validation d'une annonce",$contenu,$eleve_id);
					couriel($eleve_id,"[Frankiz] Demande de licence Microsoft de $nom $prenom X $promo",$contenu,WINDOWS_ID);
					$contenu="La clé demandée par $nom $prenom X $promo pour ".$log[$_POST['logiciel']]." est : $cle <br><br>".
						"Très Cordialement<br>" .
						"Le BR<br>"  ;
					couriel(WINDOWS_ID,"[Frankiz] Demande de licence Microsoft de $nom $prenom X $promo",$contenu,$eleve_id);
				} else {
					?>
					<commentaire>Ta nouvelle clé va t'être expédiée sur ta boite mail.</commentaire>
					<?php
					// sinon on l'ajoute... et on update la base...
						$DB_msdnaa->query("UPDATE cles_{$_POST['logiciel']} SET attrib='1' WHERE eleve_id='".$_SESSION['user']->uid."'");
						$contenu="La clé qui vous a été attribuée pour ".$log[$_POST['logiciel']]." est : $cle <br><br>".
							"Très Cordialement<br>" .
							"Le BR<br>"  ;
					couriel($eleve_id,"[Frankiz] Demande de licence Microsoft $nom $prenom X $promo ",$contenu,WINDOWS_ID);
					$contenu="La clé attribuée à $nom $prenom X $promo pour ".$log[$_POST['logiciel']]." est : $cle <br><br>".
						"Très Cordialement<br>" .
						"Le BR<br>"  ;
					couriel(WINDOWS_ID,"[Frankiz] Demande de licence Microsoft de $nom $prenom X $promo",$contenu,$eleve_id);
				}
			} else {
				//on prévient les admins@windows qu'il ya une clée à rajouter à la main...
				//et le gens donne sa raison
				if($_POST['logiciel']=="winxp"){
					$DB_web->query("SELECT valeur FROM parametres WHERE nom='lastpromo_oncampus'");
					list($promo_temp) = $DB_web->next_row() ;
					if($promo==$promo_temp-1 || $promo==$promo_temp){
					?>
				
				<formulaire id="dem_licence" titre= "Les licences pour les logiciels Microsoft" action="profil/licences.php">
					<note>Ta requéte a bien été prise en compte, un admin@windows s'en occupe.</note>
					<hidden id="raison" valeur="sur le platal"/>
					<? if(isset($_POST['logiciel'])){ echo "<hidden id=\"logiciel\" valeur=\"".$_POST['logiciel']."\" />"; } ?>
					<bouton id='envoyer' titre='Continuer'/>
				</formulaire>	
				<?php 
					}else{
				?>
				<formulaire id="dem_licence" titre= "Les licences pour les logiciels Microsoft" action="profil/licences.php">
					<warning>Tu ne figures pas dans la liste des personnes ayant droit à une licence dans le cadre du programme MSDNAA</warning>
					<p>Seuls les étudiants sur le platâl peuvent faire une demande pour une license Microsoft dans le cadre MSDNAA, s'il s'agit d'une erreur tu peux le signaler aux admin@windows.</p>
					<p>Si c'est le cas indique la raison de ta demande :</p>
					<zonetext titre="Raison" id="raison"></zonetext>
					<? if(isset($_POST['logiciel'])){ echo "<hidden id=\"logiciel\" valeur=\"".$_POST['logiciel']."\" />"; } ?>
					<bouton id='envoyer' titre='Continuer'/>
					<bouton id='' titre='Ne rien faire'/>
				</formulaire>
				<?
					}
				}
			
				//traitement particulier pour les autres logiciels
				else{
	?>
				<formulaire id="dem_licence" titre= "Les licences pour les logiciels Microsoft" action="profil/licences.php">
					<warning>Vu le faible nombre de licences que nous possédons pour ce logiciel, il nous faut une raison valable pour te l'attribuer.</warning>
					<zonetext titre="Raison" id="raison"></zonetext>
					<? if(isset($_POST['logiciel'])){ echo "<hidden id=\"logiciel\" valeur=\"".$_POST['logiciel']."\" />"; } ?>
					<bouton id='envoyer' titre='Envoyer'/>
					<bouton id='' titre='Ne rien faire'/>
				</formulaire>
	<?php
				}
			}
		}
	}
	//On regarde si le bouton a été activé et si oui on interroge la base et on envoie le mail avec la licence...
	else if(isset($_POST['valid']) && !isset($_POST['refus'])){
		//on affiche la charte...
		// et on demande l'accord
	?>
		<formulaire id="dem_licence" titre= "Les licences pour les logiciels Microsoft" action="profil/licences.php">
		
		<h1>Contrat d'utilisation étudiant</h1>
		<p>En sa qualité de membre de MSDN® Academic Alliance (MSDNAA), l'établissement auquel vous êtes inscrit est autorisé à vous fournir des logiciels à utiliser sur votre ordinateur personnel. Vous devez respecter les instructions d'utilisation générales de MSDNAA citées ci-dessous, ainsi que les termes et conditions du Contrat de Licence Utilisateur final (CLUF) MSDN, l'Amendement du Contrat de Licence et les conditions imposées par votre établissement.</p>
		<p>L'administrateur du programme MSDNAA de votre établissement devra consigner toutes les données relatives à l'utilisation des élèves, fournir des données consolidées à Microsoft® sur demande et s'assurer que tous les utilisateurs, notamment les élèves, les enseignants et le personnel technique, respectent strictement toutes les conditions du programme.</p>
		<p>Par l'installation, la copie ou toute autre utilisation des logiciels, vous acceptez de vous conformer aux termes et conditions du CLUF et de l'Amendement du Contrat de Licence. Si vous refusez de vous y conformer, il vous est interdit d'installer, copier ou utiliser les logiciels.</p>
		<h3>Instructions relatives à l'installation</h3>
		<p>Pour pouvoir installer des logiciels sur votre ordinateur personnel, vous devez être inscrit à au moins un cours dispensé par l'établissement abonné.</p>
		<p>Votre établissement peut soit vous donner accès à un serveur de téléchargement, soit vous prêter une copie des logiciels de façon temporaire afin que l'installiez sur votre ordinateur personnel.</p>
		<p>Dans le cas de certains produits, une clé de produit vous sera remise pour installer les logiciels. Il est interdit de divulguer cette clé à un tiers.</p>
		<h3>Instructions relatives à l'utilisation</h3>
		<p>Vous n'avez pas le droit de donner à un tiers des copies des logiciels empruntés ou téléchargés. Les autres élèves autorisés doivent se procurer les logiciels conformément aux procédures définies par l'administrateur du programme MSDNAA.</p>
		<p>Vous pouvez utiliser les logiciels à des fins non lucratives, notamment à des fins d'enseignement, de recherche et/ou de conception, de développement et de test dans le cadre de projets pédagogiques personnels. Il est interdit d'utiliser les logiciels MSDNAA pour le développement de logiciels à but lucratif.</p>
		<p>Lorsque vous n'êtes plus inscrit à aucun cours dispensé par l'établissement abonné, vous ne pouvez plus vous procurer des logiciels MSDNAA. Toutefois, vous pouvez continuer à utiliser les produits précédemment installés sur votre ordinateur, à condition de vous conformer toujours aux instructions du programme MSDNAA.</p>
		<p>Si vous contrevenez aux termes et conditions stipulés dans le CLUF et l'Amendement du Contrat de Licence, l'administrateur du programme MSDNAA exigera la confirmation de la désinstallation des logiciels de votre ordinateur personnel.</p>
		<? if(isset($_POST['logiciel'])){ echo "<hidden id=\"logiciel\" valeur=\"".$_POST['logiciel']."\" />"; } ?>
		<bouton id='accord' titre="J'accepte" /> 
		<bouton id='refus' titre="Je refuse" onClick="return window.confirm('Tu refuses ta clé gratuite ?')"/>
		
		</formulaire>
	<?php
	} else {
		if(isset($_POST['envoyer'])&&$_POST['raison']!=""){
			//on teste si il n'y a pas déjà une demande en attente
			$DB_msdnaa->query("SELECT 0 FROM valid_licence WHERE eleve_id='{$_SESSION['user']->uid}'");
			if ($DB_msdnaa->num_rows()>0) { 
	?>
		<warning>Tu as déjà fait une demande d'attribution d'une licence Windows. Le BR s'en occupe...</warning>
		
	<?php	
			} else {
				//on prends en compte la demande...
				$DB_msdnaa->query("INSERT valid_licence SET raison='{$_POST['raison']}', logiciel='{$_POST['logiciel']}', eleve_id='{$_SESSION['user']->uid}'");
	?>
			<warning>Ta requête a bien été prise en compte.</warning>
			<?php
				$contenu="La demande de clé de $nom $prenom X $promo pour ".$log[$_POST['logiciel']]." n'a pas aboutit faute de clé. Si sa demande est légitime, veuillez aller sur la page d'administration pour en ajouter une. <br/><br/>".
					"Très Cordialement<br>" .
					"Le BR<br>"  ;
				couriel(WINDOWS_ID,"[Frankiz] echec de la demande de licence Microsoft de $nom $prenom X $promo",$contenu,$eleve_id);
				
			}
		}
	?>
		<formulaire id="dem_licence" titre= "Les licences pour les logiciels Microsoft" action="profil/licences.php">
			<note>Dans le cadre de l'accord MSDNAA, chaque étudiant de polytechnique à le droit à une version de Windows XP Pro gratuite, légale et attibuée à vie</note>
			<p>Les licences disponibles</p>
			<choix titre="Logiciels" id="logiciel" type="combo" valeur="">
				<option titre="Windows XP Pro" id="winxp"/>
				<option titre="Windows 2003 Serveur" id="2k3serv"/>
				<option titre="Windows 2000 Professionnel" id="win2k"/>
				<option titre="Access 2003" id="2k3access"/>
				<option titre="One Note 2003" id="2k3onenote"/>
				<option titre="Visio 2003 Professionnel" id="2k3visiopro"/>
				<option titre="Visual Studio .NET" id="visualstudio"/>
			</choix>	
			<bouton id='valid' titre='Envoyer'/>
		</formulaire>
	<?
	}
	?>
	
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>
