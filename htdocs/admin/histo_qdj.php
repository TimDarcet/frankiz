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

//nb de qdj planifiées
$date = date("Y-m-d", time()-3025);
?>
	<p>Nous sommes le : <?= $date ?></p>
	<h2>Historique</h2>
	<?

	$date = date("Y-m-d", time()-3025 + 24*3600);
	$DB_web->query("SELECT qdj_id,date,question,reponse1,reponse2,compte1,compte2 FROM qdj WHERE date<'$date'  ORDER BY date DESC");
	while(list($id,$date,$question,$reponse1,$reponse2,$compte1,$compte2) = $DB_web->next_row()){

?>
		<module titre="QDJ du <?= date("d/m/Y",strtotime($date)) ?>">
			<qdj>
				<question><?php echo $question ?></question>
				<reponse id="1" votes="<?= $compte1?>"><?php echo $reponse1?></reponse>
				<reponse id="2" votes="<?= $compte2?>"><?php echo $reponse2?></reponse>
			</qdj>
		</module>
<? 
	}

?>
</page>

<?php
require_once BASE_LOCAL."/include/page_footer.inc.php";
?>
