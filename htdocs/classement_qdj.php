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
	$Id$
	
*/
require_once "include/global.inc.php";
demande_permission('interne');

class QDJModule extends PLModule
{
	function run()
	{
		global $DB_web;

		$this->assign('title', "Frankiz : Classement QDJ");
?>
	<page id="classement_qdj">
		<h2>Le Classement QDJ</h2>
		<commentaire>
			Le classement QDJ est remis à jour tous les deux mois (1er mars, 1er mai, ...).<br />
			A l'issue de ces deux mois, le premier du classement se voit décerner une coupe, le dernier une cuillère en bois et celui qui a le diagramme le plus "homogène" un coup au Bôb avec les deux autres.<br />
			 <br />
			 Les règles du classement QDJ sont simples. Suivant le moment de la journée où vous votez, vous obtenez plus ou moins de points. Le but étant d'en accumuler le maximum :)<br />
			 Il y a 10 façons de gagner ou de perdre des points:
			<ul><li>Voter premier rapporte 5 points</li>
			<li>Voter second rapporte 2 points</li>
			<li>Voter troisième rapporte 1 points</li>
			<li>Voter 42 rapporte 4.2 points</li>
			<li>Voter 69 rapporte 6.9 points</li>
			<li>Voter 314 rapporte 3.14 points</li>
			<li>Voter avec la même position que les derniers chiffres de l'ip fait gagner 3 points (c'est bien de savoir lire l'infoBR :) )</li>
			<li>Voter treizième vous fait perdre 13 points, (C'est mal d'essayer de prendre l'ip de la passerelle !)</li>
			<li>Règle bonus qui rapporte 7 points au réveil !</li>
			<li>Enfin, proposer une "bonne" QDJ, c'est-à-dire que le QDJMaster acceptera de passer, rapporte 7.1 points le jour où elle passe (utilisez votre cerveau pour battre ceux qui utilisent des scripts)</li></ul>
			 Amusez-vous bien et surtout, lisez la QDJ avant de voter.
		
		</commentaire>
		
		<?php
		$DB_web->query("SELECT UNIX_TIMESTAMP(MIN(date)) as dateMin, UNIX_TIMESTAMP(MAX(date)) as dateMax
				FROM qdj_votes WHERE idRegle > 0;");
		list($dateMin, $dateMax)=$DB_web->next_row();
		$anneeMin = date("Y",$dateMin);
		$anneeMax = date("Y",$dateMax);
		$moisMin = date("n",$dateMin);
		$moisMax = date("n",$dateMax);
		$moisMin = floor(($moisMin -1) / 2) * 2 + 1;
		$moisMax = (floor(($moisMax + 1) / 2) - 1) * 2;  //bidouille pour recuperer le mois avant le groupe en cours...
		$nbrIntervals = floor( ( 12 * ($anneeMax - $anneeMin) + $moisMax - $moisMin) /2 ) + 1;
		$datesDebut = array();
		$datesDebutAffichage = array();
		$annee = 0;
		$mois = 0;
		for ($i=0; $i<=$nbrIntervals; $i++){
			$annee = $anneeMin + floor(($moisMin + 2 * $i) / 12);
			$mois = ($moisMin + 2 * $i) %12;
			$datesDebut[$i] = mktime(0,0,0,$mois,1,$annee);
			$datesDebutAffichage[$i] = date("d-m-Y",$datesDebut[$i]);
		}
		if (isset($_POST['periode'])){
			$periode = $_POST['periode'];
			if (is_numeric($periode)) $periode=intval($periode);
		}else{
			$periode = "actuelle";
		}
		echo "<formulaire id='form' titre='Choix de la période' action='classement_qdj.php'>\n";
		echo "<choix titre='Quelle période afficher ?' id='periode' type='combo' valeur='$periode'>\n";
		echo "<option titre='La période actuelle' id='actuelle' />\n";
		echo "<option titre='Tous les scores' id='tout' />\n";
		for($i=0; $i<$nbrIntervals; $i++){
			echo "<option titre='Du {$datesDebutAffichage[$i]} au {$datesDebutAffichage[$i+1]}' id='$i' />\n";
		}
		echo '</choix>';
		echo "<bouton id='afficher' titre='afficher' />\n";
		echo '</formulaire>';
?>
		<liste id="liste_qdj" selectionnable="non">
				<entete id="nom" titre="Nom"/>
				<entete id="detail" titre="Détail"/>
				<entete id="total" titre="Total (moyenne, écart type)"/>
<?php
		$requete = "
					SELECT
						t.eleve_id, t.nom, t.prenom, t.surnom, t.promo,
						p.total, p.nb1, p.nb2, p.nb3, p.nb4, p.nb5, p.nb6, p.nb7, p.nb8, p.nb9, p.nb10
					FROM
						qdj_points AS p
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
		$debutRequete = "
		SELECT
						t.eleve_id, t.nom, t.prenom, t.surnom, t.promo,
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
		if (is_int($periode) && $periode >= 0 && $periode < $nbrIntervals) {
			$requete = $debutRequete . " AND UNIX_TIMESTAMP(date) >= {$datesDebut[$periode]} AND UNIX_TIMESTAMP(date) < {$datesDebut[$periode+1]}"
					.$finRequete;
		} elseif ($periode == "tout"){
			$requete = $debutRequete.$finRequete;
		}
		$DB_web->query($requete);
		$moy = 0;
		$ecartype = 0;
		while(list($eleve_id,$nom,$prenom,$surnom,$promo,$total,$nb1,$nb2,$nb3,$nb4,$nb5,$nb6,$nb7,$nb8,$nb9,$nb10) = $DB_web->next_row()) {
			$moy = ($nb1 + $nb2 + $nb3 + $nb4 + $nb5 + $nb6 + $nb7 + $nb8 + $nb9 + $nb10)/10;
			$ecartype =sqrt((pow($nb1,2) + pow($nb2,2) + pow($nb3,2) + pow($nb4,2) + pow($nb5,2) + pow($nb6,2) + pow($nb7,2) + pow($nb8,2) + pow($nb9,2) + pow($nb10,2))/10 - pow($moy,2));
			$ecartype = round($ecartype, 2);
			echo "\t\t<element id=\"$eleve_id\">\n";
				echo "\t\t\t<colonne id=\"nom\">".($surnom!="" ?$surnom:$nom." ".$prenom)." (X$promo)</colonne>\n";
				echo "\t\t\t<colonne id=\"detail\"><image source=\"classement_qdj.php?graph&amp;nb1=$nb1&amp;nb2=$nb2&amp;nb3=$nb3&amp;nb4=$nb4&amp;nb5=$nb5&amp;nb6=$nb6&amp;nb7=$nb7&amp;nb8=$nb8&amp;nb9=$nb9&amp;nb10=$nb10\" texte=\"image\"/></colonne>\n";
				echo "\t\t\t<colonne id=\"total\">$total ($moy, $ecartype)</colonne>\n";
			echo "\t\t</element>\n";
		}
		?>
		</liste>
	
	</page>
<?php
	}
}

if(isset($_REQUEST["graph"])){
	header ("Content-type: image/png");
	$nom=array(" ","1er","2e","3e","13","42","69","pi","ip","bonus","qdj");
	for($i=1; $i<11; $i++){
		$nb[$nom[$i]] = (isset($_REQUEST["nb$i"])?$_REQUEST["nb$i"]:0);
	}
	// on calcule le nombre de pages vues sur l'année
	$max_nb = max($nb);
	// on définit la largeur et la hauteur de notre image
	$largeur = 300;
	$hauteur = 82;
	//on crée une ressource pour notre image qui aura comme largeur $largeur et $hauteur comme hauteur (on place également un or die si la création se passait mal afin d'avoir un petit message d'alerte)
	$im = @ImageCreate ($largeur, $hauteur) or die ("Erreur lors de la création de l'image");
	$blanc = ImageColorAllocate ($im, 255, 255, 255);  
	$noir = ImageColorAllocate ($im, 0, 0, 0);  
	$bleu_fonce = ImageColorAllocate ($im, 75, 130, 195);
	$bleu_clair = ImageColorAllocate ($im, 95, 160, 240);
	// on dessine un trait horizontal pour représenter l'axe du temps     
	ImageLine ($im, 10, $hauteur-20, $largeur, $hauteur-20, $noir);
	// on affiche le numéro des règles
	$i=0;
	foreach ($nb as $nom => $nombre) {
		$i++;
		if(strlen($nom) >3){
			$decalage = (strlen($nom) - 3) * 4;
		}else{
			$decalage = 0;
		}
			
		ImageString ($im, 2, $i*$largeur/(count($nb)+1) - $decalage, $hauteur-18, $nom, $noir);
		//$i++;
//		ImageString ($im, 2, $i*$largeur/(count($nb)+1), $hauteur-18, '('.$nombre.')', $noir);
	}
	$i=0;
	foreach ($nb as $nom => $nombre) {
			$i++;
			// on calcule la hauteur du baton
			$hauteurImageRectangle = ceil((($nombre*($hauteur-32))/$max_nb));
			ImageFilledRectangle ($im, $i*$largeur/(count($nb)+1), $hauteur-$hauteurImageRectangle-20, $i*$largeur/(count($nb)+1)+14, $hauteur-21, $noir);
			ImageFilledRectangle ($im, $i*$largeur/(count($nb)+1)+2, $hauteur-$hauteurImageRectangle+2-20, $i*$largeur/(count($nb)+1)+12, $hauteur-21-1, $bleu_fonce);
			ImageFilledRectangle ($im, $i*$largeur/(count($nb)+1)+6, $hauteur-$hauteurImageRectangle+2-20, $i*$largeur/(count($nb)+1)+8, $hauteur-21-1, $bleu_clair);
			if($nombre <10){
				ImageString($im, 2, $i*$largeur/(count($nb)+1)+5, $hauteur-$hauteurImageRectangle-20-12, $nombre, $noir);
			}
			elseif($nombre<100){
				ImageString($im, 2, $i*$largeur/(count($nb)+1)+2, $hauteur-$hauteurImageRectangle-20-12, $nombre, $noir);
			}
			else{
				ImageString($im, 2, $i*$largeur/(count($nb)+1)-1, $hauteur-$hauteurImageRectangle-20-12, $nombre, $noir);
			}
			//$i++;
	}
	// on dessine le tout
	imagecolortransparent ($im,$blanc);
	Imagepng ($im);
}
else
{
	require "include/page_header.inc.php";
	$smarty = new QDJModule;
	require "include/page_footer.inc.php";
}
?>
