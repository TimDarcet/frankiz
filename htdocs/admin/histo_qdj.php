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
	Page qui permet aux admins de voir l'historique des qdj
	
	$Log$
	Revision 1.9  2005/04/13 17:09:58  pico
	Passage de tous les fichiers en utf8.

	Revision 1.8  2005/02/04 17:36:35  pico
	ce sera ptèt mieux comme ça
	
	Revision 1.7  2005/02/04 17:32:26  pico
	Correction bug division par zero
	
	Revision 1.6  2005/01/06 23:31:31  pico
	La QDJ change à 0h00 (ce n'est plus la question du jour plus un petit peu)
	
	Revision 1.5  2004/12/17 20:40:48  pico
	Mise en forme
	
	Revision 1.4  2004/12/17 20:28:02  pico
	Plus joli
	
	Revision 1.3  2004/12/17 20:11:54  pico
	Un peu moins précis..
	
	Revision 1.2  2004/12/17 20:08:38  pico
	Affichage plus condensé
	
	Revision 1.1  2004/12/17 19:55:44  pico
	Ajout d'une page pour voir l'historique des qdj
	

	
*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_FORT);
if(!verifie_permission('admin') && !verifie_permission('qdjmaster'))
	acces_interdit();



// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="Histo_qdj" titre="Frankiz : Historique des qdj">
<?
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

}

$date = date("Y-m-d", time());
?>
	<h4>Nous sommes le : <?= date("d/m/Y",strtotime($date)) ?></h4>
	<h2>Historique</h2>
	<?
	$DB_web->query("SELECT qdj_id,date,question,reponse1,reponse2,compte1,compte2 FROM qdj WHERE date!='0000-00-00' AND date<'$date'  ORDER BY date DESC");
	while(list($id,$date,$question,$reponse1,$reponse2,$compte1,$compte2) = $DB_web->next_row()){
		if(($compte1+$compte2)!=0){ 
			$p1 = round(100*$compte1/($compte1+$compte2));
			$p2 = round(100*$compte2/($compte1+$compte2));
		}else{
			$p1 = 0;
			$p2 = 0;
		}
		
?>
		<h4>QDJ du <?= date("d/m/Y",strtotime($date)) ?></h4>
			<?= "$question ?" ?><br/>
			<?= "- $reponse1 ($compte1 soit $p1%)"?><br/>
			<?= "- $reponse2 ($compte2 soit $p2%)"?><br/>
<? 
	}

?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
