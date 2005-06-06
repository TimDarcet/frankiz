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

	$Log$
	Revision 1.17  2005/06/06 17:36:26  pico
	il faut bien changer un peu le bonus pour que Ã§a reste un bonus...

	Revision 1.16  2005/05/24 17:05:36  pico
	Ajout d'un lien vers le classement
	
	Revision 1.15  2005/05/24 14:35:55  pico
	Le vote à la qdj met à jour le classement (reste à afficher le classement)
	
	Revision 1.14  2005/04/13 17:10:00  pico
	Passage de tous les fichiers en utf8.
	
	Revision 1.13  2005/01/06 23:31:31  pico
	La QDJ change à 0h00 (ce n'est plus la question du jour plus un petit peu)
	
	Revision 1.12  2004/12/16 12:52:57  pico
	Passage des paramètres lors d'un login
	
	Revision 1.11  2004/11/02 17:46:39  pico
	Modification de la gestion des caches de la qdj
	
	Revision 1.10  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.9  2004/10/15 22:56:42  schmurtz
	Finission de la gestion du cache qdj
	
	Revision 1.8  2004/09/15 23:20:29  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.7  2004/09/15 21:42:32  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

require_once BASE_LOCAL."/include/qdj.inc.php";

if(est_authentifie(AUTH_MINIMUM)) {

	// On cherche si l'utilisateur a déjà voté ou non
	$date_aujourdhui = date("Y-m-d", time());
	$DB_web->query("SELECT 0 FROM qdj_votes WHERE date='$date_aujourdhui' and eleve_id='".$_SESSION['user']->uid."' LIMIT 1");
	$a_vote = $DB_web->num_rows() != 0;

	// Gestion du vote
	if(isset($_REQUEST['qdj']) && $date_aujourdhui==$_REQUEST['qdj'] && !$a_vote && ($_REQUEST['vote']==1 || $_REQUEST['vote']==2)) {
		// On stocke le vote
		cache_supprimer("qdj_courante_question");
		cache_supprimer("qdj_courante_reponse");
		$DB_web->query("LOCK TABLE qdj_votes WRITE");
		$DB_web->query("SELECT @max:=IFNULL(MAX(ordre),0) FROM qdj_votes WHERE date='$date_aujourdhui'");
		list($position) = $DB_web->next_row();
		$position++;
		$DB_web->query("INSERT INTO qdj_votes SET date='$date_aujourdhui',eleve_id='".$_SESSION['user']->uid."',ordre=@max+1");
		$DB_web->query("UNLOCK TABLES");
		$DB_web->query("UPDATE qdj SET compte".$_REQUEST['vote']."=compte".$_REQUEST['vote']."+1 WHERE date='$date_aujourdhui'");
		
		// On gère le classement:
		$nbpoints = 0;
		$regle = 0;
		switch($position){
			case 1:	$nbpoints = 5;	$regle = 1;	break;
			case 2:	$nbpoints = 2;	$regle = 2;	break;
			case 3:	$nbpoints = 1;	$regle = 3;	break;
			case 13: $nbpoints = -13;	$regle = 4;	break; // Faut pas spoofer la passerelle !
			case 42:	$nbpoints = 4.2;	$regle = 5;	break;
			case 69:	$nbpoints = 6.9;	$regle = 6;	break;
			case 314:	$nbpoints = 3.14;	$regle = 7;	break;
			case (substr($_SESSION['ip'], 12, 3)): 	$nbpoints = 3;	$regle = 8;	break; // C'est bien d'avoir la bonne ip ;-)
			case 100+date("d",time())+date("m",time()): 	$nbpoints = 7;	$regle = 9;	break; // Permet de mettre un peu des points au réveil, vers midi...
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

