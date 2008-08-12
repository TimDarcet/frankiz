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

class QdjMiniModule extends FrankizMiniModule
{
	/**
	 * Quelle intérêt de mettre deux variables dans le constructeur me demanderais-vous, alors
	 * qu'il n'y a que deux possibilités de modules.
	 * C'est très simple. Les utilisateurs normaux ont (0, true) et (-1, false), les devels peuvent aussi
	 * avoir (1, true), ce qui leur permet de voter à la qdj un jour à l'avance.
	 *
	 * @param jour Offset du jour dont on doit afficher la qdj (0 = aujourd'hui, -1 = hier, etc...)
	 * @param peut_voter Est ce que la qdj est ouverte aux votes. Si faux, affiche les resultats.
	 */
	public function run(){

	}
	public function init()
	{
		FrankizMiniModule::register('qdj', AUTH_PUBLIC);
	}
	public function construct($jour, $peut_voter)
	{
		global $DB_web;

/*		$DB_web->query("LOCK TABLES qdj_votes WRITE, qdj_points WRITE, qdj WRITE, trombino.eleves READ");*/
		$date_qdj = date("Y-m-d", time() + $jour * 24 * 3600);

		if ($peut_voter)
			$peut_voter = !$this->a_vote($date_qdj);

		if ($peut_voter)
			$peut_voter = !$this->handle_votes($date_qdj);

		if ($this->get_qdj($date_qdj))
		{
			if ($peut_voter)
				$this->tpl = "minimodules/qdj/qdj_question.tpl";
			else
				$this->tpl = "minimodules/qdj/qdj_reponse.tpl";
		
			$this->titre = "QDJ";
		}

//		$DB_web->query("UNLOCK TABLES");
	}

	public static function check_auth()
	{
		return est_authentifie(AUTH_COOKIE);
	}

	
	/**
	 * Renvoie si l'utilisateur a déja voté
	 */
	private function a_vote($date_qdj)
	{
		global $DB_web;

/*		$DB_web->query("SELECT 0 FROM qdj_votes 
		                 WHERE date='$date_qdj' and eleve_id='{$_SESSION['uid']}' AND ordre > 0
				 LIMIT 1");
		return $DB_web->num_rows() != 0;*/
	}
	
	/**
	 * Regarde si la personne est en train de voter, et si oui, effectue le vote.
	 * @param jour Offset du jour pour la qdj
	 * @return true si la personne a voté, false sinon
	 */
	private function handle_votes($date_qdj)
	{
		global $DB_web;
		
		if (!isset($_REQUEST["qdj"]) || $_REQUEST["qdj"] != $date_qdj)
			return false;
			
		if ($_REQUEST["vote"] != "1" && $_REQUEST["vote"] != "2")
			return false;

/*		$DB_web->query("SELECT @max := IFNULL(MAX(ordre),0) 
		                  FROM qdj_votes 
				 WHERE date='$date_qdj'");*/
		list($position) = $DB_web->next_row();
		$position++;

		switch ($position)
		{
			case 1:		$this->ajouter_points(5, 1);	break;
			case 2:		$this->ajouter_points(2, 2);	break;
			case 3:		$this->ajouter_points(1, 3);	break;
			case 13:	$this->ajouter_points(-13, 4); 	break;
			case 42:	$this->ajouter_points(4.2, 5);	break;
			case 69:	$this->ajouter_points(6.9, 6);	break;
			case 314:	$this->ajouter_points(3.14, 7);	break;
			case (substr($_SESSION['ip'], 12, 3)): 	
					$this->ajouter_points(3, 8);	break; // C'est bien d'avoir la bonne ip ;-)
			case 100 +date("d",time())+date("m",time()): 	
					$this->ajouter_points(7, 9); 	break;
		}
		
			
/*		$DB_web->query("INSERT INTO qdj_votes 
					SET date = '$date_qdj', 
					    eleve_id = '{$_SESSION['uid']}',
					    idRegle = '$regle', 
					    ordre = @max+1");
*/		
		if ($position == 1 && (date("m", time()) % 2 == 1) && (date("d", time()) == 1)) {
			// A déplacer dès que possible dans un truc cron.
//			$DB_web->query('TRUNCATE TABLE `qdj_points`');
		}
		
/*		$DB_web->query("UPDATE qdj 
		                   SET compte{$_REQUEST['vote']} = compte{$_REQUEST['vote']}+1 
		                 WHERE date='$date_qdj'");
		
		if ($position == 15)
		{
			// On met des points à la personne dont la QDJ a été acceptée
			$DB_web->query("SELECT eleve_id
			                  FROM qdj 
					 WHERE date='$date_qdj'");
			list($eleveId) = $DB_web->next_row();

			$DB_web->query("INSERT INTO qdj_votes 
			                        SET date = '$date_qdj', 
					            eleve_id = '$eleveId', 
						    ordre = 0, 
						    idRegle = 10");
			$DB_web->query("SELECT 0 
			                  FROM qdj_points 
					 WHERE eleve_id='$eleveId'");

			ajouter_points($eleveId, 10, 7.1);
		}
*/
		return true;
	}
		
	/**
	 * Assigne les différentes données de la qdj à la page en cours.
	 * @return false si aucune qdj n'est présente pour la journée demandée.
	 */
	private function get_qdj($date_qdj)
	{
		global $DB_web;

/*		$DB_web->query("SELECT question, reponse1, reponse2, compte1, compte2 
		                  FROM qdj 
				 WHERE date='$date_qdj' LIMIT 1");
		if (!list($question, $reponse1, $reponse2, $compte1, $compte2) = $DB_web->next_row())
			return false;
	
		$DB_web->query("SELECT ordre, nom, prenom, promo, surnom 
		                  FROM qdj_votes 
		             LEFT JOIN trombino.eleves USING(eleve_id)
			         WHERE date='$date_qdj' AND ordre > 0 
			      ORDER BY ordre DESC 
			         LIMIT 20");
		
		$votants = array();
		while (list($ordre, $nom, $prenom, $promo, $surnom) = $DB_web->next_row())
	  	{
			$votants[] = array('ordre' => $ordre,
					   'eleve' => array('nom'    => $nom,
					   		    'prenom' => $prenom,
							    'promo'  => $promo,
							    'surnom' => $surnom));
		}
*/
		$this->assign('question', $question);
		$this->assign('date', $date_qdj);
		$this->assign('reponse1', $reponse1);
		$this->assign('reponse2', $reponse2);
		$this->assign('compte1', $compte1);
		$this->assign('compte2', $compte2);
		$this->assign('votants', $votants);


		return true;
	}

	/**
	 * Ajoute des points à l'utilisateur en question
	 */
	private function ajouter_points($points, $regle, $uid = null)
	{
		global $DB_web;

		if ($uid === null)
			$uid = $_SESSION['uid'];
/*		
		$DB_web->query("SELECT 0 FROM qdj_points WHERE eleve_id = $uid");
		if ($DB_web->num_rows() != 0)
			$DB_web->query("UPDATE qdj_points 
			                   SET total = total + $points, 
					       nb$regle = nb$regle+1 
					 WHERE eleve_id = $uid");
		else
			$DB_web->query("INSERT INTO qdj_points 
			                        SET total = $points, 
						    nb$regle = 1, 
						    eleve_id = $uid");*/
	}	
}
//FrankizMiniModule::register_module('qdj', "QdjMiniModule", "QDJ du jour", array(0, true));
//FrankizMiniModule::register_module('qdj_hier', "QdjMiniModule", "QDJ de la veille", array(-1, false));

?>
