<?php
/*
	Copyright (C) 2006 Binet Réseau
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
	Partenariats BR/entreprises
	$Id: partenaires.php 1801 2006-12-07 10:26:46Z vinz2 $
*/

require_once "include/global.inc.php";
demande_authentification(AUTH_INTERNE);

// Partenaires
// <lien titre="" url=""/> 
$partenaires = array (
	'aoc'	=> array(
		'<lien titre="Apple On Campus" url="partenaires/appleoncampus.php"/> (ordinateurs)',
		"Un partenariat entre Apple et le BR permet aux personnels et élèves de bénéficier jusqu'à 12% de réduction sur le matériel Apple (à l'exception des iPod)"
	),
	'autocad'=> array(
		'<lien titre="Autodesk" url="http://students.autodesk.fr/"/> (modélisation 3D)',
		"Tous les étudiants peuvent bénéficier gratuitement des versions éducation de tous les logiciels de modélisation 3D d'Autodesk (en particulier le célèbre Autocad)."
	),
	'dell'	=> array(
		'<lien titre="Dell Premier" url="http://reseaux.polytechnique.fr/par/"/> (ordinateurs)',
		"Tous les personnels et élèves de l'Ecole bénéficient des conditions du marché public avec Dell, soit entre 30 et 40% de discussions sur plusieurs modèles professionnels de portables et de d'ordinateurs fixes."
	),
	'msdnaa'=> array(
		'<lien titre="Microsoft MSDNAA" url="ftp://enez/"/> (logiciels)',
		"Tous les produits Microsoft (à l'exception des jeux et d'Office) sont diponibles gratuitement pour les étuditants de l'Ecole présents sur le plateau. Les téléchargements se font depuis <lien titre=\"ftp://enez/\" url=\"ftp://enez/\"/>, et les clès se demandent dans son <lien titre=\"profil Frankiz\" url=\"profil/licences.php\"/>."
	),
);

// génération de la page
require "include/page_header.inc.php";
?>
<page id='vocabulaire' titre='Frankiz : Partenariats'>
<h1>Offres spéciales proposées aux élèves</h1>
<liste id="liste_voc" selectionnable="non">
	<entete id="entreprise" titre="Entreprise"/>
	<entete id="description" titre="Détails"/>
	<?php
		foreach ($partenaires as $id => $infos)
		{
			echo "\t\t<element id=\"$id\">\n" .
				"\t\t\t<colonne id=\"entreprise\">".$infos[0]."</colonne>\n" .
				"\t\t\t<colonne id=\"description\">".$infos[1]."</colonne>\n" .
				"\t\t</element>\n";
		}
	?>
</liste>
</page>
<?php
require_once "include/page_footer.inc.php";
?>
