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
	Affichage de la QDJ actuelle et gestion des votes.
	
	TODO traiter le cas ou le qdj master est à la bourre (garder l'ancienne qdj par exemple).

	$Id$
	
*/

require_once BASE_LOCAL."/include/qdj.inc.php";

if(est_authentifie(AUTH_MINIMUM)) {

	// On cherche si l'utilisateur a déjà voté ou non
	$date_aujourdhui = date("Y-m-d", time());
	$DB_web->query("SELECT 0 FROM qdj_votes WHERE date='$date_aujourdhui' and eleve_id='".$_SESSION['user']->uid."' AND ordre > 0 LIMIT 1");
	$a_vote = $DB_web->num_rows() != 0;

	// Gestion du vote
	if(isset($_REQUEST['qdj']) && $date_aujourdhui==$_REQUEST['qdj'] && !$a_vote && ($_REQUEST['vote']==1 || $_REQUEST['vote']==2)) {
		// On stocke le vote
		cache_supprimer("qdj_courante_question");
		cache_supprimer("qdj_courante_reponse");
		$DB_web->query("LOCK TABLE qdj_votes, qdj_points WRITE");
		$DB_web->query("SELECT @max:=IFNULL(MAX(ordre),0) FROM qdj_votes WHERE date='$date_aujourdhui'");
		list($position) = $DB_web->next_row();
		$position++;
		// On gère le classement:
		$nbpoints = 0;
		$regle = 0;
		switch($position){
			case 13: $nbpoints = -13;	$regle = 4;	break; // Faut pas spoofer la passerelle !
			case 100+date("d",time())+date("m",time()): 	$nbpoints = 7;	$regle = 9;	break; // Permet de mettre un peu des points au réveil, vers midi...
			case 1:	$nbpoints = 5;	$regle = 1;	break;
			case (substr($_SESSION['ip'], 12, 3)): 	$nbpoints = 3;	$regle = 8;	break; // C'est bien d'avoir la bonne ip ;-)
			case 2:	$nbpoints = 2;	$regle = 2;	break;
			case 3:	$nbpoints = 1;	$regle = 3;	break;
			case 42:	$nbpoints = 4.2;	$regle = 5;	break;
			case 69:	$nbpoints = 6.9;	$regle = 6;	break;
			case 314:	$nbpoints = 3.14;	$regle = 7;	break;
		}
		
			
		$DB_web->query("INSERT INTO qdj_votes SET date='$date_aujourdhui',eleve_id='".$_SESSION['user']->uid."',idRegle = '$regle', ordre=@max+1;");
		
		if($position == 1&&((date("m", time())%2==1&&date("d", time())==1)||date("Y-m-d",time())=="2006-01-05")){
			//die("ouais");
			//$position=12;
			$DB_web->query('TRUNCATE TABLE `qdj_points`;');
		}
		
		$DB_web->query("UNLOCK TABLES");
		$DB_web->query("UPDATE qdj SET compte".$_REQUEST['vote']."=compte".$_REQUEST['vote']."+1 WHERE date='$date_aujourdhui'");
		
		if($position == 3){  //on met des points à la personne dont la QDJ a été acceptée
		
			$DB_web->query("SELECT eleve_id FROM qdj WHERE date='$date_aujourdhui';");
			list($eleveId) = $DB_web->next_row();
			$DB_web->query("INSERT INTO qdj_votes SET date='$date_aujourdhui', eleve_id='$eleveId', ordre=0, idRegle=10;");
			$DB_web->query("SELECT 0 FROM qdj_points WHERE eleve_id='$eleveId';");
			if($DB_web->num_rows()!=0){
				$DB_web->query("UPDATE qdj_points SET total=total+7.1, nb10=nb10+1 WHERE eleve_id='$eleveId'");
			}else{
				$DB_web->query("INSERT INTO qdj_points SET total=7.1, nb10=1, eleve_id=$eleveId");
			}
		}
		
		if($nbpoints!=0){
			$DB_web->query("SELECT 0 FROM qdj_points WHERE eleve_id=".$_SESSION['user']->uid);
			if($DB_web->num_rows()!=0){
				$DB_web->query("UPDATE qdj_points SET total=total+$nbpoints, nb$regle=nb$regle+1 WHERE eleve_id=".$_SESSION['user']->uid);
			}else{
				$DB_web->query("INSERT INTO qdj_points SET total=$nbpoints, nb$regle=1, eleve_id=".$_SESSION['user']->uid);
			}
		}

		rediriger_vers("/");
	}

	// Affichage de la QDJ courante 
	qdj_affiche(false,$a_vote);		
}
?>

