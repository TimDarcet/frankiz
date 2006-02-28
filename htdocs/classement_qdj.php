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
demande_authentification(AUTH_INTERNE);


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
}else{
	require "include/page_header.inc.php";
	?>
	<page id="classement_qdj" titre="Frankiz : Classement QDJ">
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
		
		<liste id="liste_qdj" selectionnable="non">
				<entete id="nom" titre="Nom"/>
				<entete id="detail" titre="Détail"/>
				<entete id="total" titre="Total (moyenne, écart type)"/>
		<?
		$DB_web->query("
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
					ORDER BY p.total DESC");
		$moy = 0;
		$ecartype = 0;
		while(list($eleve_id,$nom,$prenom,$surnom,$promo,$total,$nb1,$nb2,$nb3,$nb4,$nb5,$nb6,$nb7,$nb8,$nb9,$nb10) = $DB_web->next_row()) {
			$moy = ($nb1 + $nb2 + $nb3 + $nb4 + $nb5 + $nb6 + $nb7 + $nb8 + $nb9 + $nb10)/10;
			$ecartype =sqrt(($nb1^2 + $nb2^2 + $nb3^2 + $nb4^2 + $nb5^2 + $nb6^2 + $nb7^2 + $nb8^2 + $nb9^2 + $nb10^2)/10 - $moy^2);
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
	require "include/page_footer.inc.php";
	}
?>
