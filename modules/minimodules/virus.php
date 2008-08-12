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
	Pour faire peur aux gens qui ont des virus...
	
	$Id$
	
*/

class VirusMiniModule extends FrankizMiniModule
{
	public function init()
	{
		FrankizMiniModule::register('virus', AUTH_COOKIE);
	}
	
	public function run()
	{
		global $DB_admin;

		/* On cherche dans la base les vilains qui ont un status solved différent de 2,
		 * donc qui a priori sont infestés. La personne est identifiée par son ip, 
		 * de l'ip on remonte à la chambre puis au mec.*/
/*		$DB_admin->query("SELECT e.eleve_id, p.piece_id, i.ip, i.date, i.date+10-CURDATE(), i.solved, i.id, l.nom
		                    FROM prises AS p 
			       LEFT JOIN infections AS i ON p.ip = i.ip 
			       LEFT JOIN liste_virus AS l ON l.virus_id = i.virus_id
			       LEFT JOIN trombino.eleves as e ON e.piece_id = p.piece_id 
			           WHERE NOT( i.solved='2') AND i.ip='{$_SERVER['REMOTE_ADDR']}'");

		if($DB_admin->num_rows() == 0)
			return;
		
		/* On signale que l'utilisateur est prévenu... */
/*		$DB_admin->query("UPDATE infections SET solved='1' WHERE solved='0' AND ip='{$_SERVER['REMOTE_ADDR']}'");*/
			
//		list($eleve_id, $piece, $ip, $date, $rebours, $solved, $id, $nomv) = $DB_admin->next_row();
		
		$infections = array();
/*		do
		{
			$infections[] = array('date' => $date,
					      'nom_virus' => $nom_virus);
		}
		while (list($eleve_id, $piece, $ip, $date, $rebours, $solved, $id, $nomv) = $DB_admin->next_row());*/

		$this->assign('infections', $infections);
		$this->tpl = "minimodules/virus/virus.tpl";
	}

	public static function check_auth()
	{
		return est_authentifie(AUTH_COOKIE);
	}
}
FrankizMiniModule::register_module('virus', 'VirusMiniModule', "Annonces Virus (important)");

?>
