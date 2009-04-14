<?php
/***************************************************************************
 *  Copyright (C) 2008 Binet Réseau                                       *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                     *
 *                                                                         *
 *  This program is free software; you can redistribute it and/or modify   *
 *  it under the terms of the GNU General Public License as published by   *
 *  the Free Software Foundation; either version 2 of the License, or      *
 *  (at your option) any later version.                                    *
 *                                                                         *
 *  This program is distributed in the hope that it will be useful,        *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 *  GNU General Public License for more details.                           *
 *                                                                         *
 *  You should have received a copy of the GNU General Public License      *
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/

class AnnoncesModule extends PLModule
{
	public function handlers() 
	{
		return array("annonces" => $this->make_hook("annonces", AUTH_PUBLIC));
	}

	private function get_categorie($en_haut, $stamp, $perime) {
		if ($en_haut==1) 
			return "important";
		elseif ($stamp > date("Y-m-d H:i:s",time()-12*3600)) 
			return "nouveau";
		elseif ($perime < date("Y-m-d H:i:s",time()+24*3600))
			return "vieux";
		else 
			return "reste";
	}

	function handler_annonces(&$page)
	{
		global $DB_web;

		// Pour marquer les annonces comme lues ou non
		if (isset($_REQUEST['lu']))
		{
			$DB_web->query("REPLACE annonces_lues
					    SET	annonce_id = '{$_REQUEST['lu']}',
						eleve_id   = '{$_SESSION['uid']}'");
		}
		
	
		if (isset($_REQUEST['nonlu'])) 
		{
			$DB_web->query("DELETE FROM annonces_lues
					      WHERE annonce_id = '{$_REQUEST['nonlu']}' AND
						    eleve_id ='{$_SESSION['uid']}");
		}
	
		if (isset($_SESSION['uid']))
		{
			$est_annonce_non_lue = "ISNULL(annonces_lues.annonce_id)";
			$left_join_annonces_lues = "LEFT JOIN annonces_lues 
			                                   ON annonces_lues.annonce_id = annonces.annonce_id
			                                  AND annonces_lues.eleve_id = '{$_SESSION['uid']}'";
		}
		else
		{
			$est_annonce_non_lue = "1";
			$left_join_annonces_lues = "";
		}
		

		$res=XDB::query("
			SELECT	annonces.annonce_id,
				DATE_FORMAT(stamp, '%d/%m/%Y'),
				stamp, perime, titre, contenu, important, exterieur,
				nom, prenom, surnom, promo, login,
				IFNULL(mail, CONCAT(login, '@poly.polytechnique.fr')) AS mail,
			  	$est_annonce_non_lue
			  FROM  annonces
		     LEFT JOIN  eleves USING(eleve_id)
		  	 	$left_join_annonces_lues
			 WHERE	perime > NOW()
  		      ORDER BY	perime DESC");
		$annonces_liste = $res->fetchAllRow();

		$annonces = array('vieux'     => array('desc' => "Demain, c'est fini", 'annonces' => array()),
				  'nouveau'   => array('desc' => "Nouvelles fraiches", 'annonces' => array()),
				  'important' => array('desc' => "Important", 'annonces' => array()),
				  'reste'     => array('desc' => "En attendant", 'annonces' => array()));

		foreach ($annonces_liste as $annonce)
		{
		list($id, $date, $stamp, $perime, $titre, $contenu, $en_haut, $exterieur,
			    $nom, $prenom, $surnom, $promo, $login, $mail, $visible) = $annonce;
		
			if (!$exterieur && !verifie_permission('interne')){
				continue;
			}
			
			$categorie = $this->get_categorie($en_haut, $stamp, $perime);
			$annonces[$categorie]['annonces'][$id] = array('id'     => $id,
								       'titre'  => $titre,
					       		               'date'   => $date,
							               'img'    => file_exists(DATA_DIR_LOCAL.'annonces/'.$id),
							               'eleve'  => array('nom'    	=> $nom,
					   			     			 'prenom' 	=> $prenom,
								       			 'promo'  	=> $promo,
								      			 'surnom' 	=> $surnom,
								       			 'mail'   	=> $mail,
								       			 'loginpoly'  	=> $login),
							   	       'contenu' => $contenu,
							   	       'visible' => $visible);
		}
		
		$page->assign('title', "Annonces");
		$page->assign('page_raw', 1);
		$page->assign('annonces', $annonces);
		$page->changeTpl('annonces/annonces.tpl');
	}
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
