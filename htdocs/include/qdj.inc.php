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
	Affichage de la QDJ. Le fait d'avoir un fichier inclus permet d'éviter d'avoir du code
	dupliquer dans qdj.php et qdj_hier.php. Ça évite aussi de devoir regrouper les deux modules
	dans le même fichier (et donc passer autre le principe 1 module = 1 fichier).
	
	TODO traiter le cas ou le qdj master est à la bourre (garder l'ancienne qdj par exemple).
	
	$Log$
	Revision 1.16  2005/01/06 23:31:31  pico
	La QDJ change à 0h00 (ce n'est plus la question du jour plus un petit peu)

	Revision 1.15  2004/12/16 00:22:53  pico
	Coreection cache qdj, sinon la qdh devient la qdah...
	
	Revision 1.14  2004/12/14 17:36:33  pico
	Correction action
	
	Revision 1.13  2004/11/02 17:46:39  pico
	Modification de la gestion des caches de la qdj
	
	Revision 1.12  2004/11/02 17:29:22  pico
	Ne crée plus de qdj qd il n'y en a pas.
	
	Revision 1.11  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.10  2004/09/20 20:33:47  schmurtz
	Mise en place d'un systeme de cache propre
	
	Revision 1.9  2004/09/17 15:28:20  schmurtz
	Utilisation de la balise <eleve> pour les derniers votants aÌ€ la qdj, les anniversaires, la signature des annoncesâ€¦
	
	Revision 1.8  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.7  2004/09/15 23:01:21  schmurtz
	Bug de la qdj : renvoie maintenant sur la page courante (et non index.php)
	
	Revision 1.6  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

function qdj_affiche($hier,$deja_vote) {
	global $DB_web;
	$date = date("Y-m-d", time() - ($hier ? 24*3600 : 0));
	$cache_id = "qdj_".($hier?"hier_":"courante_").($deja_vote?"reponse":"question");
	// Récupération des noms des derniers votants à la question en cours
	if(!cache_recuperer($cache_id,strtotime(date("Y-m-d 00:50:25", time())))) {
		$DB_web->query("SELECT question,reponse1,reponse2,compte1,compte2 FROM qdj WHERE date='$date' LIMIT 1");
		while(list($question,$reponse1,$reponse2,$compte1,$compte2) = $DB_web->next_row()){
?>
	
			<module id="<?php echo $hier ? 'qdj_hier' : 'qdj' ?>" titre="QDJ<?php if($hier) echo ' d\'hier' ?>">
				<qdj type="<?php echo $hier ? 'aujourdhui' : 'hier' ?>" id="<?php echo $date?>" <?php if(!$deja_vote && !$hier) echo " action=\"?qdj=$date&amp;vote=\""; ?>>
					<question><?php echo $question ?></question>
					<reponse id="1" votes="<?php echo $compte1?>"><?php echo $reponse1?></reponse>
					<reponse id="2" votes="<?php echo $compte2?>"><?php echo $reponse2?></reponse>
<?php
	
					// interrogation de la base de données
					$DB_web->query("SELECT ordre,nom,prenom,promo,surnom FROM qdj_votes LEFT JOIN trombino.eleves USING(eleve_id) WHERE date='$date' ORDER BY ordre DESC LIMIT 20");
					while(list($ordre,$nom,$prenom,$promo,$surnom) = $DB_web->next_row())
						echo "\t\t\t\t\t<dernier ordre=\"$ordre\"><eleve nom=\"$nom\" prenom=\"$prenom\" promo=\"$promo\" surnom=\"$surnom\"/></dernier>\n";
?>
				</qdj>
			</module>
<?php
		}
	cache_sauver($cache_id);
	}

}
?>