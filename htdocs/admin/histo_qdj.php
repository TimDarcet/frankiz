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
	
	$Id$
	
*/
	
require_once "../include/global.inc.php";

// Vérification des droits
demande_authentification(AUTH_MDP);
if(!verifie_permission('admin') && !verifie_permission('qdjmaster'))
	acces_interdit();



// Génération de la page
//===============
require_once BASE_LOCAL."/include/page_header.inc.php";

?>
<page id="Histo_qdj" titre="Frankiz : Historique des qdj">
<?php
foreach ($_POST AS $keys => $val){
	$temp = explode("_",$keys) ;

}

$date = date("Y-m-d", time());
?>
	<h4>Nous sommes le : <?php echo date('d/m/Y', time()); ?></h4>
	<h2>Historique</h2>
	<?php
	$DB_web->query("SELECT qdj_id,DATE_FORMAT(date,'%d/%m/%Y'),question,reponse1,reponse2,compte1,compte2 FROM qdj WHERE date!='0000-00-00' AND date<'$date'  ORDER BY date DESC");
	while(list($id,$date,$question,$reponse1,$reponse2,$compte1,$compte2) = $DB_web->next_row()){
		if(($compte1+$compte2)!=0){ 
			$p1 = round(100*$compte1/($compte1+$compte2));
			$p2 = round(100*$compte2/($compte1+$compte2));
		}else{
			$p1 = 0;
			$p2 = 0;
		}
		
?>
		<h4>QDJ du <?php echo $date; ?></h4>
			<?php echo $question; ?> ?<br/>
			- <?php echo $reponse1; ?> (<?php echo $compte1; ?> soit <?php echo $p1; ?>%)<br/>
			- <?php echo $reponse2; ?> (<?php echo $compte2; ?> soit <?php echo $p2; ?>%)<br/>
<?php 
	}

?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
