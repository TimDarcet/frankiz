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

if(est_authentifie(AUTH_MINIMUM)) {

	$DB_web->query("SELECT affiche_id,titre,url FROM affiches WHERE TO_DAYS(date)=TO_DAYS(NOW())");
	if ($DB_web->num_rows()!=0){
		echo "<module id=\"activites\" titre=\"Activités\">\n";

		while (list($id,$titre,$url)=$DB_web->next_row()) { ?>
			<annonce date="">
			<lien url="<?php echo $url?>"><image source="<?php echo DATA_DIR_URL.'affiches/'.$id?>" texte="Affiche" legende="<?php echo $titre?>"/></lien>
			</annonce>
	<?php }
	
		echo "</module>\n";
	}
}
