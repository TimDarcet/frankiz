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


require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MINIMUM);

// Données sur l'utilisateur
$DB_trombino->query("SELECT eleve_id,eleves.nom,prenom,promo,mail FROM eleves WHERE eleve_id='{$_SESSION['user']->uid}'");
list($eleve_id,$nom,$prenom,$promo,$mail) = $DB_trombino->next_row();

// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";
require_once BASE_LOCAL."/include/rss_func.inc.php";


?>
<page id="licences" titre="Frankiz : Les Licences">
<?	if(isset($_POST['accord'])){
		//on lance la requête qui va bien pour voir la clé
		$DB_msdnaa->query("SELECT cle,attrib FROM cles_winxp WHERE eleve_id='".$_SESSION['user']->uid."' LIMIT 1");
		//on a la clé attribuée de manière unique par le BR.
		if($DB_msdnaa->num_rows()!=0){
			list($cle,$attrib) = $DB_msdnaa->next_row());
			//si la personne a déjà demandé sa clé...
			if($attrib != 0){
				?>
				<p>Tu as déjà demandé ta clé, elle va t'être ré-expédiée à <?echo "$mail" ?></p>
				<?php
				$contenu="La clé qui vous a déjà été attribué est : $cle";
				couriel($eleve_id,"[Frankiz] Demande de licence Microsoft de $nom $prenom X $promo",$contenu,WINDOWS_ID);
				$contenu="La clé demandée par $nom $prenom X $promo est : $cle";
				couriel(WINDOWS_ID,"[Frankiz] Demande de licence Microsoft de $nom $prenom X $promo",$contenu,$eleve_id);
			} else {
				?>
				<p>Ta nouvelle clé, va t'être expédiée à <?echo "$mail" ?></p>
				<?php
				// sinon on l'ajoute... et on update la base...
				$DB_msdnaa->query("UPDATE cles_winxp SET attrib='1' WHERE eleve_id='".$_SESSION['user']->uid."'");
				$contenu="La clé qui vous a été attribué est : $cle";
				//a completer
				couriel($eleve_id,"[Frankiz] Demande de licence Microsoft $nom $prenom X $promo ",$contenu,WINDOWS_ID);
				$contenu="La clé attribuée à $nom $prenom X $promo est : $cle";
				couriel(WINDOWS_ID,"[Frankiz] Demande de licence Microsoft de $nom $prenom X $promo",$contenu,$eleve_id);
			}
		}else{
			// Rajouter le gars dans la liste
		}
	}

	//On regarde si le bouton a été activé et si oui on interroge la base et on envoie le mail avec la licence...
	if(isset($_POST['valid']) && !isset($_POST['refus'])){
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
		<bouton id='accord' titre="J'accepte" /> 
		<bouton id='refus' titre="Je refuse" onClick="return window.confirm('Tu refuses ta clé gratuite ?')"/>
		</formulaire>
	<?
	} else {
	?>
		<formulaire id="dem_licence" titre= "Les licences pour les logiciels Microsoft" action="profil/licences.php">
			<note>Dans le cadre de l'accord MSDNAA, chaque étudiant de polytechnique à le droit à une version de Windows XP Pro gratuite, légale et attibuée à vie</note>
			<p>Les licences disponibles</p>
			<choix titre="Logiciels" id="logiciel" type="combo" valeur="">
				<option titre="Windows XP Pro" id="xp_pro"/>
				<? //<option titre="Windows 2003 Serveur" id="server_2003" />
				//<option titre="Access 2003" id="access_2003" /> ?>
			</choix>
			<bouton id='valid' titre='Envoyer'/>
		
		</formulaire>
	<?
	}
	?>
	
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>