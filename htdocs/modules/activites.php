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
	Script de création de la partie activités contenant des images type "affiche".
	
	$Log$
	Revision 1.17  2005/01/17 22:51:47  pico
	Liens vers les activités + réorganisation

	Revision 1.16  2005/01/17 08:16:01  pico
	Passage de l'ouverture du bob en gras
	
	Revision 1.15  2005/01/12 23:20:30  pico
	Activités extérieures, et triées par heure...
	
	Revision 1.14  2005/01/02 10:50:25  pico
	Passage de certaines pages en visibles de l'intérieur (non loggué)
	
	Revision 1.13  2004/12/13 08:50:48  pico
	Correction mineure
	
	Revision 1.12  2004/11/27 18:23:53  pico
	Ajout de l'annonce: 'le bob est ouvert' dans les activités + page de gestion du bob
	
	Revision 1.11  2004/11/25 10:40:08  pico
	Correction activités (sinon l'image était tjs écrite en tant que 0 et ct pas glop du coup)
	
	Revision 1.10  2004/10/29 16:30:56  kikx
	Ca evite que les activité apparaissent si il n'y a rein dedans ...
	
	Revision 1.9  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.8  2004/10/07 22:52:20  kikx
	Correction de la page des activites (modules + proposition + administration)
		rajout de variables globales : DATA_DIR_LOCAL
						DATA_DIR_URL
	
	Comme ca si ca change, on est safe :)
	
	Revision 1.7  2004/09/17 22:49:29  kikx
	Rajout de ce qui faut pour pouvoir faire des telechargeement de fichiers via des formulaires (ie des champs 'file' des champ 'hidden') de plus maintenant le formulaire sont en enctype="multipart/form-data" car sinon il parait que ca marche pas !
	
	Revision 1.6  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.5  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/


// Etat du bôb
$DB_web->query("SELECT valeur FROM parametres WHERE nom='bob'");
list($valeur) = $DB_web->next_row();

$DB_web->query("SELECT affiche_id,titre,url,date,exterieur FROM affiches WHERE TO_DAYS(date)=TO_DAYS(NOW()) ORDER BY date");
	
if ($DB_web->num_rows()!=0 || $valeur=='1'){
	echo "<module id=\"activites\" titre=\"Activités\">\n";
	if(est_authentifie(AUTH_INTERNE) && $valeur == 1) echo "<annonce titre=\"Le BôB est ouvert\"/>";
	while (list($id,$titre,$url,$date,$exterieur)=$DB_web->next_row()) { 
		if(!$exterieur && !est_authentifie(AUTH_INTERNE)) continue;
	?>
		<annonce date="<? echo $date ?>">
		<lien url="<?php echo $url?>"><image source="<?php echo DATA_DIR_URL.'affiches/'.$id?>" texte="Affiche" legende="<?php echo $titre?>"/></lien>
		</annonce>
<?php }
	echo "</module>\n";
}
