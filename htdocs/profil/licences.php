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
	Page qui permet de demander une cl� windows
	
	$Log$
	Revision 1.10  2004/12/17 20:33:54  dei
	Bugfix affichage

	Revision 1.9  2004/12/17 20:27:02  pico
	On va �viter une ptite erreur
	
	Revision 1.8  2004/12/17 20:25:20  pico
	Ajout des logs
	
	
*/
	

require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_MINIMUM);

// Donn�es sur l'utilisateur
$DB_trombino->query("SELECT eleve_id,eleves.nom,prenom,promo,mail FROM eleves WHERE eleve_id='{$_SESSION['user']->uid}'");
list($eleve_id,$nom,$prenom,$promo,$mail) = $DB_trombino->next_row();

// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";
require_once BASE_LOCAL."/include/rss_func.inc.php";

?>
<page id="licences" titre="Frankiz : Les Licences">
<?	//on v�rifie que la demande est faite pour windows xp pro
	if(isset($_POST['accord']) && ($_POST['logiciel']=="xp_pro")){
		//on lance la requ�te qui va bien pour voir la cl�
		$DB_msdnaa->query("SELECT cle,attrib FROM cles_winxp WHERE eleve_id='".$_SESSION['user']->uid."' LIMIT 0,1");
		//on verifie que le demandeur existe dans la base
		if($DB_msdnaa->num_rows()!=0){
			//on a la cl� attribu�e de mani�re unique par le BR.
			list($cle,$attrib) = $DB_msdnaa->next_row();
			//si la personne a d�j� demand� sa cl�...
			if($attrib != 0){
				?>
				<p>Tu as d�j� demand� ta cl�, elle va t'�tre r�-exp�di�e sur ta boite mail.</p>
				<?php
				$contenu="La cl� qui vous a d�j� �t� attribu� est : $cle <br><br>".
					"Tr�s BR-ement<br>".
					"L'automate :)<br>";
				//a completer couriel(WEBMESTRE_ID,"[Frankiz] Validation d'une annonce",$contenu,$eleve_id);
				couriel($eleve_id,"[Frankiz] Demande de licence Microsoft de $nom $prenom X $promo",$contenu,WINDOWS_ID);
				$contenu="La cl� demand�e par $nom $prenom X $promo est : $cle <br><br>".
					"Tr�s BR-ement<br>" .
					"L'automate :)<br>";
				couriel(WINDOWS_ID,"[Frankiz] Demande de licence Microsoft de $nom $prenom X $promo",$contenu,$eleve_id);
			} else {
				?>
				<p>Ta nouvelle cl�, va t'�tre exp�di�e � sur ta boite mail.</p>
				<?php
				// sinon on l'ajoute... et on update la base...
				$DB_msdnaa->query("UPDATE cles_winxp SET attrib='1' WHERE eleve_id='".$_SESSION['user']->uid."'");
				$contenu="La cl� qui vous a �t� attribu� est : $cle <br><br>".
					"Tr�s BR-ement<br>" .
					"L'automate :)<br>";
				couriel($eleve_id,"[Frankiz] Demande de licence Microsoft $nom $prenom X $promo ",$contenu,WINDOWS_ID);
				$contenu="La cl� attribu�e � $nom $prenom X $promo est : $cle <br><br>".
					"Tr�s BR-ement<br>" .
					"L'automate :)<br>";
				couriel(WINDOWS_ID,"[Frankiz] Demande de licence Microsoft de $nom $prenom X $promo",$contenu,$eleve_id);
			}
		} else {
			//on pr�vient les admins@windows qu'il ya une cl�e � rajouter � la main...
			?>
			<warning>Tu ne figures pas dans la liste des personnes ayant droit � une licence dans le cadre du programme MSDNAA</warning>
			<p>Seuls les �tudiants sur le plat�l peuvent faire une demande pour une license Microsoft dans le cadre MSDNAA, s'il s'agit d'une erreur tu peux le signaler au admin@windows.</p>
			<p>Si c'est le cas clique i�i :</p>
			<bouton id='envoyer' titre='Envoyer'/>
			<bouton id='' titre='Ne rien faire'/>
			<?php
		}
	}

	//On regarde si le bouton a �t� activ� et si oui on interroge la base et on envoie le mail avec la licence...
	if(isset($_POST['valid']) && !isset($_POST['refus'])){
		//on affiche la charte...
		// et on demande l'accord
	?>
		<formulaire id="dem_licence" titre= "Les licences pour les logiciels Microsoft" action="profil/licences.php">
		
		<h1>Contrat d'utilisation �tudiant</h1>
		<p>En sa qualit� de membre de MSDN� Academic Alliance (MSDNAA), l'�tablissement auquel vous �tes inscrit est autoris� � vous fournir des logiciels � utiliser sur votre ordinateur personnel. Vous devez respecter les instructions d'utilisation g�n�rales de MSDNAA cit�es ci-dessous, ainsi que les termes et conditions du Contrat de Licence Utilisateur final (CLUF) MSDN, l'Amendement du Contrat de Licence et les conditions impos�es par votre �tablissement.</p>
		<p>L'administrateur du programme MSDNAA de votre �tablissement devra consigner toutes les donn�es relatives � l'utilisation des �l�ves, fournir des donn�es consolid�es � Microsoft� sur demande et s'assurer que tous les utilisateurs, notamment les �l�ves, les enseignants et le personnel technique, respectent strictement toutes les conditions du programme.</p>
		<p>Par l'installation, la copie ou toute autre utilisation des logiciels, vous acceptez de vous conformer aux termes et conditions du CLUF et de l'Amendement du Contrat de Licence. Si vous refusez de vous y conformer, il vous est interdit d'installer, copier ou utiliser les logiciels.</p>
		<h3>Instructions relatives � l'installation</h3>
		<p>Pour pouvoir installer des logiciels sur votre ordinateur personnel, vous devez �tre inscrit � au moins un cours dispens� par l'�tablissement abonn�.</p>
		<p>Votre �tablissement peut soit vous donner acc�s � un serveur de t�l�chargement, soit vous pr�ter une copie des logiciels de fa�on temporaire afin que l'installiez sur votre ordinateur personnel.</p>
		<p>Dans le cas de certains produits, une cl� de produit vous sera remise pour installer les logiciels. Il est interdit de divulguer cette cl� � un tiers.</p>
		<h3>Instructions relatives � l'utilisation</h3>
		<p>Vous n'avez pas le droit de donner � un tiers des copies des logiciels emprunt�s ou t�l�charg�s. Les autres �l�ves autoris�s doivent se procurer les logiciels conform�ment aux proc�dures d�finies par l'administrateur du programme MSDNAA.</p>
		<p>Vous pouvez utiliser les logiciels � des fins non lucratives, notamment � des fins d'enseignement, de recherche et/ou de conception, de d�veloppement et de test dans le cadre de projets p�dagogiques personnels. Il est interdit d'utiliser les logiciels MSDNAA pour le d�veloppement de logiciels � but lucratif.</p>
		<p>Lorsque vous n'�tes plus inscrit � aucun cours dispens� par l'�tablissement abonn�, vous ne pouvez plus vous procurer des logiciels MSDNAA. Toutefois, vous pouvez continuer � utiliser les produits pr�c�demment install�s sur votre ordinateur, � condition de vous conformer toujours aux instructions du programme MSDNAA.</p>
		<p>Si vous contrevenez aux termes et conditions stipul�s dans le CLUF et l'Amendement du Contrat de Licence, l'administrateur du programme MSDNAA exigera la confirmation de la d�sinstallation des logiciels de votre ordinateur personnel.</p>
		<? if(isset($_POST['logiciel'])){ echo "<hidden id=\"logiciel\" valeur=\"".$_POST['logiciel']."\" />"; } ?>
		<bouton id='accord' titre="J'accepte" /> 
		<bouton id='refus' titre="Je refuse" onClick="return window.confirm('Tu refuses ta cl� gratuite ?')"/>
		
		</formulaire>
	<?php
	} else {
		if(isset($_POST['envoyer'])){
		?>
			<warning>Ta requ�te a bien �t� prise en compte.</warning>
		<?
		}
	?>
		<formulaire id="dem_licence" titre= "Les licences pour les logiciels Microsoft" action="profil/licences.php">
			<note>Dans le cadre de l'accord MSDNAA, chaque �tudiant de polytechnique � le droit � une version de Windows XP Pro gratuite, l�gale et attibu�e � vie</note>
			<p>Les licences disponibles</p>
			<choix titre="Logiciels" id="logiciel" type="combo" valeur="">
				<option titre="Windows XP Pro" id="xp_pro"/>
			</choix>	
			<bouton id='valid' titre='Envoyer'/>
		</formulaire>
	<?
	}
	?>
	
</page>
<?php require_once BASE_LOCAL."/include/page_footer.inc.php" ?>