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
	Page qui permet aux admins de valider une qdj
	
	$Log$
	Revision 1.15  2004/11/29 17:27:32  schmurtz
	Modifications esthetiques.
	Nettoyage de vielles balises qui trainaient.

	Revision 1.14  2004/11/27 20:16:55  pico
	Eviter le formatage dans les balises <note> <commentaire> et <warning> lorsque ce n'est pas necessaire
	
	Revision 1.13  2004/11/27 15:02:17  pico
	Droit xshare et faq + redirection vers /gestion et non /admin en cas de pbs de droits
	
	Revision 1.12  2004/11/27 14:56:15  pico
	Debut de mise en place de droits sp�ciaux (qdj + affiches)
	+ g�n�ration de la page d'admin qui va bien
	
	Revision 1.11  2004/11/16 18:32:34  schmurtz
	Petits problemes d'interpretation de <note> et <commentaire>
	
	Revision 1.10  2004/11/02 17:54:12  pico
	Correction bug:
	- derniere qdj planifi�e
	- possibilit� de d�programmer une qdj en remettant 0000-00-00 comme date
	- compte des qdj planifi�es
	
	Revision 1.9  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.8  2004/10/17 18:55:50  pico
	.
	
	Revision 1.7  2004/10/15 20:49:46  pico
	Affichage plus compress� pour plus de lisibilit�
	
	Revision 1.6  2004/10/15 20:32:01  pico
	Reorganisation de la page
	
	Revision 1.5  2004/10/14 22:15:24  pico
	- Ajout de boutons "un jour plus t�t" "un jour plus tard"
	- Emp�che de d�finir une date pass�e pour la qdj
	
	Revision 1.4  2004/10/14 19:59:37  pico
	Correction de bug
	
	Revision 1.3  2004/10/14 19:21:41  pico
	- Affichage de la planification existante
	- Possibilit� de replanifier une QDJ
	
	Revision 1.2  2004/10/14 13:48:13  pico
	Am�lioration du comportement de la planification des qdj
	- possibilit� d'ins�rer une qdj et de d�caler les autres
	- ou remplacer la qdj d�j� plac�e par la courante et remettre l'ancienne dans les qdj � planifier
	

	
	
*/
	
require_once "../include/global.inc.php";

// V�rification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin') && !verifie_permission('qdjmaster'))
	rediriger_vers("/gestion/");



// G�n�ration de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="valid_qdj" titre="Frankiz : Planifie tes qdj">
<h1>Planification des qdj</h1>
<?
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

	// Fixe une date de parution � la QDJ
	
	if ($temp[0]=='valid') {
		if((strtotime($_REQUEST['date']) <=(time()-3025 ))&&($_REQUEST['date']!="0000-00-00"))
		{ ?>
			<warning>ERREUR: Veuillez choisir une date sup�rieure � aujourd'hui</warning>
		<?
		}
		else
		{
			$DB_web->query("SELECT qdj_id FROM qdj WHERE date='{$_REQUEST['date']}' LIMIT 1");
			if($DB_web->num_rows())
			{
				if(!(isset($_REQUEST['decalage']))) //On remplace la qdj pr�vue par la qdj selectionn�e, l'ancienne est remise dans la liste des qdj � planifier
				{
					list($qdj_id) = ($DB_web->next_row()); 
					$DB_web->query("UPDATE qdj SET date='0000-00-00' WHERE qdj_id='{$qdj_id}'");
				}
				else //On ins�re cette qdj et on repousse toutes les autres.
				{
					//nb de qdj planifi�es
					
					$DB_web->query("SELECT qdj_id,date FROM qdj WHERE date>='{$_REQUEST['date']}'  ORDER BY date ASC");
					while(list($id,$date_tmp) = $DB_web->next_row()) 
					{
						$DB_web->push_result();
						$date_tmp = date("Y-m-d",strtotime($date_tmp)+24*3600);
						$DB_web->query("UPDATE qdj SET date='{$date_tmp}' WHERE qdj_id='{$id}'");
						$DB_web->query("SELECT qdj_id FROM qdj WHERE date='$date_tmp'");
						if(($DB_web->num_rows())<2) break; //plus rien � d�caler
						$DB_web->pop_result() ;
					} 
					
				}
				
			}
			$DB_web->query("UPDATE qdj SET date='{$_REQUEST['date']}'WHERE qdj_id='{$temp[1]}'");
	
		?>
			<commentaire>Planification effectu�e</commentaire>
		<?
		}	
	}
	
	// D�placer un jour apr�s
	if ($temp[0]=='augdate') {
		$date1 = date("Y-m-d",strtotime($temp[2])+24*3600);
		$DB_web->query("UPDATE qdj SET date='{$temp[2]}'  WHERE date='{$date1}'");
		$DB_web->query("UPDATE qdj SET date='{$date1}' WHERE qdj_id='{$temp[1]}'");
	}
	
	// D�placer un jour avant
	if ($temp[0]=='reddate') {
		$date1 = date("Y-m-d",strtotime($temp[2])-24*3600);
		$DB_web->query("UPDATE qdj SET date='{$temp[2]}'   WHERE date='{$date1}'");
		
		$DB_web->query("UPDATE qdj SET date='{$date1}' WHERE qdj_id='{$temp[1]}'");
	}
	
	// Formulaire pour modifier la date de parution de la QDJ d�j� planifi�e
	
	if($temp[0]=='modif') {
		$DB_web->query("SELECT question,reponse1,reponse2 FROM qdj WHERE qdj_id='{$temp[1]}'");
		list($question,$reponse1,$reponse2) = $DB_web->next_row(); 
		$id = $temp[1];
?>
		<warning>Cette QDJ est d�j� planifi�e pour le <?echo $temp[2] ?></warning>
		<module titre="QDJ">
			<qdj type="aujourdhui" >
				<question><?php echo $question ?></question>
				<reponse id="1"><?php echo $reponse1?></reponse>
				<reponse id="2"><?php echo $reponse2?></reponse>
			</qdj>
		</module>
		<formulaire id="qdj_<? echo $id ?>" action="admin/planif_qdj.php">
			<champ id="date" titre="date" valeur="<? echo $temp[2] ?>"/>
			<choix type="checkbox">
				<option id="decalage" titre="D�caler si necessaire ?"/>
			</choix>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider la planification de cette qdj ?')"/>
			
		</formulaire>
<?	
	}
	
	// Supprime la qdj de la liste
	
	if ($temp[0]=='suppr') {
		$DB_web->query("DELETE FROM qdj WHERE qdj_id='{$temp[1]}'") ;
	

	?>
		<warning>Suppression d'une qdj</warning>
	<?
	}
}

//nb de qdj planifi�es
$date = date("Y-m-d", time()-3025);
?>
	<p>Nous sommes le�: <?= $date ?></p>
<?
//Cherche la date de la prochaine qdj libre
for ($i = 0; ; $i++) 
{
	$date_last = date("Y-m-d", time()-3025 + $i*24*3600);
	$DB_web->query("SELECT qdj_id FROM qdj WHERE date='$date_last' LIMIT 1");
	if(!$DB_web->num_rows()) break;
}
?>
	<p>La planification est faite jusqu'au�: <?= $date_last ?></p>
	<? $DB_web->query("SELECT qdj_id FROM qdj WHERE date>'$date' "); ?>
	<p>Nb de QDJ planifi�es: <? echo $DB_web->num_rows() ?></p>
	<? $DB_web->query("SELECT qdj_id FROM qdj WHERE date='0000-00-00' "); ?>
	<p>Nb de QDJ disponibles: <? echo $DB_web->num_rows() ?></p>
	
<?
// Affiche la planification existante
if(isset($_REQUEST['show'])) {
	?>
	<h2>Pr�visions</h2>
	<?

	$date = date("Y-m-d", time()-3025 + 24*3600);
	$DB_web->query("SELECT qdj_id,date,question,reponse1,reponse2 FROM qdj WHERE date>='$date'  ORDER BY date ASC");
	while(list($id,$date,$question,$reponse1,$reponse2) = $DB_web->next_row()){

?>
		<formulaire id="<? echo $id ?>" action="admin/planif_qdj.php">
			<note><?= $date ?></note>
			<note><?= $question ?></note>
			<note><?= "$reponse1 / $reponse2" ?></note>
		
			<? if(strtotime($date) >time()-3025 + 24*3600){ ?><bouton titre="Un jour plus t�t" id="reddate_<? echo $id ?>_<? echo $date ?>"/><? } ?>
			<bouton titre="Un jour plus tard" id="augdate_<? echo $id ?>_<? echo $date ?>"/>
			<bouton id='modif_<? echo $id ?>_<? echo $date ?>' titre='Modifier la date manuellement'/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette qdj ?!!!!!')"/>
			<hidden id="show"/>
		
		</formulaire>
<? 
	}
}


// Afficher le bouton pour montrer la plaification d�j� �tablie
if(!isset($_REQUEST['show']))
{
?>
	<formulaire action="admin/planif_qdj.php">
			<bouton id='show' titre='Voir la planification existante' />
	</formulaire>
<?



//===============================
// Affiche la qdj de demain et apr�s demain
	$date = date("Y-m-d", time()-3025 + 24*3600);
	
	$DB_web->query("SELECT question,reponse1,reponse2 FROM qdj WHERE date='$date' LIMIT 1");
	list($question,$reponse1,$reponse2) = $DB_web->next_row(); 
?>
		<h2>Pr�vue demain</h2>
		<module titre="QDJ">
			<qdj type="aujourdhui" >
				<question><?php echo $question ?></question>
				<reponse id="1"><?php echo $reponse1?></reponse>
				<reponse id="2"><?php echo $reponse2?></reponse>
			</qdj>
		</module>
<?
	$date = date("Y-m-d", time()-3025 + 48*3600);
	
	$DB_web->query("SELECT question,reponse1,reponse2 FROM qdj WHERE date='$date' LIMIT 1");
	list($question,$reponse1,$reponse2) = $DB_web->next_row(); 
?>
		<h2>Pr�vue apr�s-demain</h2>
		<module titre="QDJ">
			<qdj type="aujourdhui" >
				<question><?php echo $question ?></question>
				<reponse id="1"><?php echo $reponse1?></reponse>
				<reponse id="2"><?php echo $reponse2?></reponse>
			</qdj>
		</module>

<?
}
?>
 

		
<!-- Affiche les QDJ � planifier -->
		<h2>Disponibles</h2>
<?
	$DB_web->query("SELECT qdj_id,question, reponse1, reponse2 FROM qdj WHERE date = '0000-00-00'");
	while(list($id,$question,$reponse1,$reponse2) = $DB_web->next_row()) {
?>
		<module titre="QDJ">
			<qdj type="aujourdhui" >
				<question><?php echo $question ?></question>
				<reponse id="1"><?php echo $reponse1?></reponse>
				<reponse id="2"><?php echo $reponse2?></reponse>
			</qdj>
		</module>
		<formulaire id="qdj_<? echo $id ?>" action="admin/planif_qdj.php">

			<champ id="date" titre="date" valeur="<? echo $date_last ?>"/>
			<choix type="checkbox">
				<option id="decalage" titre="D�caler si necessaire ?"/>
			</choix>
			<bouton id='valid_<? echo $id ?>' titre='Valider' onClick="return window.confirm('Valider la planification de cette qdj ?')"/>
			<bouton id='suppr_<? echo $id ?>' titre='Supprimer' onClick="return window.confirm('!!!!!!Supprimer cette qdj ?!!!!!')"/>
		</formulaire>
<?
	}
?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
