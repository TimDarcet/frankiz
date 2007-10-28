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
	$Id: classement_qdj.php 1991 2007-10-28 00:35:44Z elscouta $
	
*/

class QDJModule extends PLModule
{
	public function handlers()
	{
		return array("qdj" => $this->make_hook("qdj", AUTH_COOKIE));
	}

	public function handler_qdj(&$page)
	{
		global $DB_web;

		$page->assign('title', "Classement QDJ");
		$page->changeTpl('qdj/qdj.php');
		
		// ------------------------------------ Calcul des intervalles -----------------------------------
		$DB_web->query("SELECT  UNIX_TIMESTAMP(MIN(date)) AS dateMin, UNIX_TIMESTAMP(MAX(date)) AS dateMax
				  FROM  qdj_votes
				 WHERE  idRegle > 0");
		list ($dateMin, $dateMax) = $DB_web->next_row();
		$anneeMin = date("Y",$dateMin);
		$anneeMax = date("Y",$dateMax);
		$moisMin = date("n",$dateMin);
		$moisMax = date("n",$dateMax);
		$moisMin = floor(($moisMin -1) / 2) * 2 + 1;
		$moisMax = (floor(($moisMax + 1) / 2) - 1) * 2;

		$nbrIntervals = floor( ( 12 * ($anneeMax - $anneeMin) + $moisMax - $moisMin) /2 ) + 1;
		
		$annee = 0;
		$mois = 0;
		for ($i = 0; $i <= $nbrIntervals; $i++)
		{
			$annee = $anneeMin + floor(($moisMin + 2 * $i) / 12);
			$mois = ($moisMin + 2 * $i) % 12;
			$datesDebut[$i] = mktime(0,0,0,$mois,1,$annee);
		}
		
		$periodes = array();
		for ($i = 0; $i < $nbrIntervals; $i++)
		{
			$periodes[] = array("debut" => $datesDebut[$i],
					    "fin"   => $datesDebut[$i+1]);
		}
		$page->assign("qdj_periodes", $periodes);
		
		if (isset($_POST['periode']))
		{
			$periode = $_POST['periode'];
			if (is_numeric($periode)) 
				$periode = intval($periode);
		} else {
			$periode = "actuelle";
		}
		

		// ------------------------------------- Requetes SQLs -----------------------------------------------
		$debutRequete = "
					SELECT
						t.eleve_id, t.nom, t.prenom, t.surnom, t.login, t.promo,
						p.total, p.nb1, p.nb2, p.nb3, p.nb4, p.nb5, p.nb6, p.nb7, p.nb8, p.nb9, p.nb10
					FROM	
		(SELECT eleve_id,
		   SUM( _vote1*5 + _vote2*2 + _vote3 - _vote4*13 + _vote5*4.2 + _vote6*6.9 + _vote7*3.14 + _vote8*3 + _vote9*7 + _vote10*7.1) as total,
		   SUM(_vote1) as nb1,
		   SUM(_vote2) as nb2,
		   SUM(_vote3) as nb3,
		   SUM(_vote4) as nb4,
		   SUM(_vote5) as nb5,
		   SUM(_vote6) as nb6,
		   SUM(_vote7) as nb7,
		   SUM(_vote8) as nb8,
		   SUM(_vote9) as nb9,
		   SUM(_vote10) as nb10
		   FROM (
		      SELECT eleve_id,
		      if(idRegle = 1, count(*), 0) as _vote1,
		      if(idRegle = 2, count(*), 0) as _vote2,
		      if(idRegle = 3, count(*), 0) as _vote3,
		      if(idRegle = 4, count(*), 0) as _vote4,
		      if(idRegle = 5, count(*), 0) as _vote5,
		      if(idRegle = 6, count(*), 0) as _vote6,
		      if(idRegle = 7, count(*), 0) as _vote7,
		      if(idRegle = 8, count(*), 0) as _vote8,
		      if(idRegle = 9, count(*), 0) as _vote9,
		      if(idRegle = 10, count(*), 0) as _vote10
		      FROM qdj_votes 
		      WHERE idRegle >0 ";
		$finRequete = "  GROUP BY idRegle, eleve_id
		 ) AS aux1
		  GROUP BY eleve_id) as p
		  LEFT JOIN
						trombino.eleves AS t USING(eleve_id)
					WHERE
						t.eleve_id != (SELECT
											te.eleve_id
										FROM
											frankiz2.compte_frankiz
										LEFT JOIN
											trombino.eleves AS te USING(eleve_id)
										WHERE
											perms LIKE '%qdjmaster,%'
										ORDER BY te.promo DESC
										LIMIT 0,1)
					ORDER BY p.total DESC";
		
		if (is_int($periode) && $periode >= 0 && $periode < $nbrIntervals) 
		{
			$requete = "$debutRequete 
			           AND UNIX_TIMESTAMP(date) >= {$datesDebut[$periode]} 
			           AND UNIX_TIMESTAMP(date) < {$datesDebut[$periode+1]} 
				   $finRequete";
		} 
		elseif ($periode == "tout")
		{
			$requete = $debutRequete.$finRequete;
		}
		
		$DB_web->query($requete);
		$moy = 0;
		$ecartype = 0;
		
		// -------------------------------- On Transfere tout a Smarty -------------------------------------------
		$voteurs = array();
		while (list($eleve_id,$nom,$prenom,$surnom,$loginpoly,$promo,$total,$nb1,$nb2,$nb3,$nb4,$nb5,$nb6,$nb7,$nb8,$nb9,$nb10) = $DB_web->next_row()) 
		{
			$moy = ($nb1 + $nb2 + $nb3 + $nb4 + $nb5 + $nb6 + $nb7 + $nb8 + $nb9 + $nb10) / 10;
			$ecartype = sqrt((pow($nb1, 2) + pow($nb2, 2) + pow($nb3, 2) + pow($nb4, 2) + pow($nb5, 2) + 
					  pow($nb6, 2) + pow($nb7, 2) + pow($nb8, 2) + pow($nb9, 2) + pow($nb10,2))/10 - pow($moy, 2));
			$ecartype = round($ecartype, 2);
			$voteurs[] = array("moyenne"   => $moy,
					   "ecarttype" => $ecartype,
					   "nb1"       => $nb1,
					   "nb2"       => $nb2,
					   "nb3"       => $nb3,
					   "nb4"       => $nb4,
					   "nb5"       => $nb5,
					   "nb6"       => $nb6,
					   "nb7"       => $nb7,
					   "nb8"       => $nb8,
					   "nb9"       => $nb9,
					   "nb10"      => $nb10,
					   "total"     => $total,
					   "eleve"     => array("nom"       => $nom,
					                        "prenom"    => $prenom,
						                "promo"     => $promo,
					  	                "surnom"    => $surnom,
							        "loginpoly" => $loginpoly));
		}
		$page->assign("qdj_voteurs", $voteurs);
	}
}

?>
