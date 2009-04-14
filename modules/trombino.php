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

#require_once BASE_LOCAL."/include/wiki.inc.php";
#require_once BASE_LOCAL."/include/session.inc.php";

define ('EXACT_MATCH', 			0);     // Correspondance exacte
define ('EXACT_MATCH_NO_QUOTES', 	1);	// Correspondance exacte, mais le texte de référence n'est pas inclus dans des guillemets
define ('NEAR_MATCH', 			2);	// Correspondance à base d'un LIKE
define ('PROMO_MATCH',			3);	// Considère 99 = 1999 et 03 = 2003
define ('NOT_NULL', 			4);	// Le champ est NOT NULL
define ('DAYOFYEAR_MATCH', 		5);	// (DATE & DATETIME uniquement) Le champ correspond au jour de l'année près.
define ('WEEK_MATCH', 			6);     // (DATE & DATETIME uniquement) Le champ correspond à une semaine près
define ('TRUE_MATCH', 			7);	// Tout le temps vrai

/**
 * Une classe permettant de générer une requête de recherche dans le TOL.
 */
class TrombinoRequest
{
	private $constraints;
	private $constraint_groups;
	
	/**
	 * Initialisation de la classe
	 */
	public function __construct()
	{
		$this->constraints = array();
		$this->constraint_groups = array();
	}

	// -------------------------------- Configuration de la requête ------------------------------------------

	/**
	 * Ajoute une contrainte sur la requete.
	 * @param $column la colonne de la base de donnee sur laquelle s'applique la contrainte
	 * @param $pattern le texte devant correspondre
	 * @param $match Si la correspondance doit etre exacte (EXACT_MATCH), ou si un LIKE suffit (NEAR_MATCH)
	 */
	public function add_constraint($column, $pattern, $match)
	{
		$this->constraints[] = array('column'  => $column,
		          		     'pattern' => $pattern,
				             'match'   => $match);
	}

	/**
	 * Supprime un groupe de contraintes
	 */
	public function reset_constraint_group($group)
	{
		unset($this->constraint_groups[$group]);
	}

	/**
	 * Ajoute une contrainte optionnelle.
	 * La contrainte ne devra pas être nécessairement satisfaite, à partir du moment ou au moins une contrainte
	 * du groupe l'est.
	 * @param $group L'identifiant du groupe. Si le groupe de contrainte n'existe pas encore, il sera crée.
	 * @param $column la colonne de la base de donnee sur laquelle s'applique la contrainte
	 * @param $pattern le texte devant correspondre
	 * @param $match Si la correspondance doit etre exacte (EXACT_MATCH), ou si un LIKE suffit (NEAR_MATCH)
	 * @param $value Si la contrainte est satisfaite, $value sera ajouté au score de la correspondance
	 */
	public function add_option_to_constraint_group($group, $column, $pattern, $match, $value)
	{
		$this->constraint_groups[$group][] = array('column'  => $column,
						           'pattern' => $pattern,
						           'match'   => $match,
						           'value'   => $value);
	}

	/**
	 * Ajoute une contrainte optionnelle, qui ne sera qu'à rajouter éventuellement du score à un résultat.
	 * @param $column la colonne de la base de donnee sur laquelle s'applique la contrainte
	 * @param $pattern le texte devant correspondre
	 * @param $match Si la correspondance doit etre exacte (EXACT_MATCH), ou si un LIKE suffit (NEAR_MATCH)
	 * @param $value Si la contrainte est satisfaite, $value sera ajouté au score de la correspondance
	 */
	public function add_option($column, $pattern, $match, $value)
	{
		// NOT IMPLEMENTED
	}

	// ------------------------------------ Génération de la requête -------------------------------------------

	/**
	 * Renvoie une chaine de caractère correspondant à la contrainte.
	 * @param $constraint Doit être un tableau avec des champs 'column', 'pattern' et 'match'.
	 */
	static private function format_constraint($constraint)
	{
		$column = $constraint['column'];
		$pattern = $constraint['pattern'];
		$match = $constraint['match'];

		switch ($match)
		{
		case EXACT_MATCH:
			return "$column = '$pattern'";
		case EXACT_MATCH_NO_QUOTES:
			return "$column = $pattern";
		case NEAR_MATCH:
			return "$column LIKE '%$pattern%'";
		case PROMO_MATCH:
			if (!is_numeric($pattern))
				return "FALSE";
			else if ($pattern < 30)
				return "$column = '20$pattern'";
			else if ($pattern < 100)
				return "$column = '19$pattern'";
			else
				return "$column = '$pattern'";
		case NOT_NULL:
			return "NOT ISNULL($column)";
		case DAYOFYEAR_MATCH:
			return "DAYOFYEAR($column) = DAYOFYEAR($pattern)";
		case WEEK_MATCH:
			return "(DAYOFYEAR($column) >= DAYOFYEAR($pattern) AND 
				 DAYOFYEAR($column) <= DAYOFYEAR($pattern + INTERVAL 7 DAY))";
		case TRUE_MATCH:
			return "TRUE";
		default:
			trigger_error("Invalid match_type");
			exit;
		}
	}

	/**
	 * Lance la requete. $DB_trombino contient ensuite le résultat.
	 */
	public function query($tol_admin)
	{
		global $DB_trombino, $DB_admin, $DB_xnet;
		
		//////
		// Création de la clause 'WHERE'
		//
		$format_callback = array('TrombinoRequest', 'format_constraint');
		
		$constraints_string = array_map($format_callback, $this->constraints);
		foreach ($this->constraint_groups as $constraint_array)
		{
			$string_array = array_map($format_callback, $constraint_array);
			$constraints_string[] = "(".implode(" OR ", $string_array).")";
		}
		$where_clause = implode(" AND ", $constraints_string);

		
		//////
		// Requête
		//
		$res = XDB::query("
			SELECT  eleves.eleve_id, eleves.nom, eleves.prenom, eleves.surnom, eleves.login, eleves.mail, eleves.date_nais,
				eleves.piece_id, pieces.tel, eleves.portable, eleves.commentaire, eleves.promo, eleves.cie, eleves.section_id, 
				sections.nom, prises.prise_id, eleves.nation
			  FROM	eleves
		     LEFT JOIN	sections ON eleves.section_id = sections.section_id
		     LEFT JOIN  pieces ON eleves.piece_id = pieces.piece_id
		     LEFT JOIN  membres ON eleves.eleve_id = membres.eleve_id
		     LEFT JOIN  binets ON membres.binet_id = binets.binet_id
		     LEFT JOIN  prises AS prises ON prises.piece_id = pieces.piece_id
		     LEFT JOIN  arpwatch_log AS arpwatch ON arpwatch.ip = prises.ip
			 WHERE  $where_clause
		      GROUP BY  eleves.eleve_id
		      ORDER BY	eleves.promo ASC,
				eleves.nom ASC,
				eleves.prenom ASC
			 LIMIT  100");
		$data = $res->fetchAllRow();
		//////
		// Génération des résultats sous formes d'arbres nommés
		//
		$resultats = array();
        foreach($data as $row){
        list($eleve_id, $nom, $prenom, $surnom, $login, $mail, $date_nais,
			    $piece_id, $tel, $port, $commentaire, $promo, $cie, $section_id, 
			    $section, $prise, $nation) = $row;
			//////
			// Si admin, génération de la liste des IPs
			//
			if ($tol_admin) 
			{
				$res_prises = XDB::query("SELECT  prise_id, p.ip 
				                    FROM  prises AS p 
						   WHERE  piece_id = {?}
						ORDER BY  prise_id", $piece_id);
				$data_prises = $res_prises->fetchAllRow();
				$prise_log = array();
				foreach ($data_prises as $row_prises)
                {
                    list($prise, $ip) = $row_prises;
					$res_xnet = XDB::query("SELECT  username, 
							         if( ((options & 0x1c0) >> 6) = 1, 'Windows 9x', 
								 if( ((options & 0x1c0) >> 6) = 2, 'Windows XP', 
								 if( ((options & 0x1c0) >> 6) = 3, 'Linux', 
								 if( ((options & 0x1c0) >> 6)= 4, 'MacOS', 
								 if( ((options & 0x1c0) >> 6), 'MacOS X', 'Inconnu'))))),
								 s.name 
						           FROM  clients 
						      LEFT JOIN  software AS s ON clients.version = s.version  
						          WHERE  lastip = {?}", $ip);

					list($dns, $os, $client) = $res_xnet->fetchOneRow();
				    $res_xnet->free();	
					if (empty($client)) 
						$client = 'Pas de client';
					if (empty($dns)) 
						$dns = "<aucune>";

					//////
					// Toutes les macs qui ont été associées à cette ip
					// On ne prend en compte que les macs qui correspondent à la période où la promo est sur le campus
					//
					$res_macs = XDB::query("SELECT  mac, ts, vendor 
					                    FROM  arpwatch_log 
						       LEFT JOIN  arpwatch_vendors ON mac LIKE CONCAT(debut_mac,':%') 
						           WHERE  ip = {?} and ts > {?}-04-15 
							GROUP BY  mac, ts 
							ORDER BY  ts", $ip, $promo+1);

					$mac_log = array();
                    $data_macs = $res_macs->fetchAllRow();
                    foreach($data_macs as $data_mac)
                    {
					list($mac, $ts, $vendor) = $data_mac;
						if (!empty($mac))
						{
							if (empty($vendor)) 
								$vendor = "<inconnu>";

							$mac_log[] = array('id'     	  => $mac,
									   'time'    	  => $ts,
									   'constructeur' => $vendor);
						}
					}
					
					$prise_log[] = array('ip'      => $ip,
							     'dns'     => $dns,
							     'client'  => $client,
							     'os'      => $os,
							     'mac_log' => $mac_log);
				}
			}

			//////
			// Génération de la liste des binets
			//
			$res_binets = XDB::query("SELECT  remarque, nom, membres.binet_id
					       FROM  membres
				          LEFT JOIN  binets USING(binet_id)
					      WHERE  eleve_id = {?}
					   ORDER BY  nom ASC", $eleve_id);

			$binets = array();
            $data_binets = $res_binets->fetchAllRow();
            foreach($data_binets as $data_binet)
            {
    			list($remarque, $binet_nom, $binet_id) = $data_binet;
				$binets[] = array('nom'      => $binet_nom,
						  'id'       => $binet_id,
						  'remarque' => $remarque);
			}
			//////
			// Génère les noms et prénoms poly.org en supprimant les accents
			//
			$nompolyorg = str_replace("&apos;", "", $nom);
			$nompolyorg = htmlentities(strtolower(utf8_decode($nompolyorg)));
			$nompolyorg = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $nompolyorg);
			$nompolyorg = str_replace(" ", "-", $nompolyorg);

			$prenompolyorg = str_replace( "&apos;" , "" , $prenom );
			$prenompolyorg = htmlentities(strtolower(utf8_decode($prenompolyorg)));
			$prenompolyorg = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $prenompolyorg);
			$prenompolyorg = str_replace( " " , "-" , $prenompolyorg );

			//////
			// Envoi des données à smarty
			$resultats[] = array('id' 		=> $eleve_id,
				   	     'nom'		=> $nom,		
				  	     'prenom' 		=> $prenom,
				       	     'nompolyorg'     	=> $nompolyorg,
				       	     'prenompolyorg'  	=> $prenompolyorg,
				             'surnom' 		=> $surnom,
				             'login' 		=> $login,
				             'mail' 		=> $mail ? $mail : "$login@poly.polytechnique.fr",
				             'nation'		=> $nation,
					     'date_nais' 	=> $date_nais,
				             'piece_id' 		=> $piece_id,
				             'tel' 		=> $tel,
				             'port'		=> $port,
					     'commentaire' 	=> $commentaire,
				             'promo' 		=> $promo,
				             'cie' 		=> $cie,
				             'section_id' 	=> $section_id,
				             'section' 		=> $section,
				             'prise_log'     	=> $tol_admin ? $prise_log : null,
				             'prise'	        => $tol_admin ? $prise : null,
					     'binets'		=> $binets);

		}

		return $resultats;
	}
}

class TrombinoModule extends PLModule
{
	function handlers()
	{
		return array('tol'		=> $this->make_hook('tol', 		AUTH_PUBLIC,	"interne"), 
			     'tol/binets'   	=> $this->make_hook('binets', 		AUTH_PUBLIC),
			     'tol/binets/logo'  => $this->make_hook('binets_logo',	AUTH_PUBLIC),
			     'tol/photo'	=> $this->make_hook('tol_photo',	AUTH_PUBLIC,	"interne"),
			     'tol/photo/img'	=> $this->make_hook('tol_photo_img',	AUTH_PUBLIC, 	"interne"));
				
	}

	private function get_nations()
	{
/*		global $DB_trombino;

		$res = XDB::query("SELECT  nation_id, name 
		                       FROM  nations
				      WHERE  existe");

		$nations = array();
		while (list($id, $name) = $DB_trombino->next_row())
			$nations[$id] = $name;

		return $nations;*/
        return XDB::iterator('SELECT nation_id, 
                                     name as nation_name 
                                FROM nations 
                               WHERE existe');
	}

	private function get_sections()
	{
/*		global $DB_trombino;

		$DB_trombino->query("SELECT  section_id, nom 
		                       FROM  sections
				      WHERE  existe");

		$nations = array();
		while (list($id, $name) = $DB_trombino->next_row())
			$nations[$id] = $name;

		return $nations;*/
        return XDB::iterator('SELECT section_id, 
                                     nom as section_nom 
                                FROM sections');
	}

	private function get_promos()
	{
/*		global $DB_trombino;

		$DB_trombino->query("SELECT  promo
		                       FROM  eleves
				      WHERE  promo != '0000'
				   GROUP BY  promo");

		$promos = array();
		while (list($promo) = $DB_trombino->next_row())
			$promos[$promo] = $promo;

		return $promos;*/
        return XDB::iterator('SELECT DISTINCT promo
                                FROM eleves
                               WHERE promo != \'0000\'
                            GROUP BY promo');
	}

	private function get_binets()
	{
/*		global $DB_trombino;

		$DB_trombino->query("SELECT  binet_id, nom
		                       FROM  binets
				   ORDER BY  nom ASC");

		$binets = array();
		while (list($id, $name) = $DB_trombino->next_row())
			$binets[$id] = $name;

		return $binets;*/
        return XDB::iterator('SELECT binet_id, 
                                     nom as binet_nom
                                FROM binets
                            ORDER BY binet_nom ASC');
	}

	function handler_tol(&$page)
	{
		global $DB_admin, $DB_web, $DB_trombino, $DB_xnet;

		//////
		// Permissions
		//
		$tol_admin = Platal::session()->checkPerms('admin') || 
    		   	     Platal::session()->checkPerms('windows') ||
		    	     Platal::session()->checkPerms('trombino') ||
		    	     Platal::session()->checkPerms('news') ||
		    	     Platal::session()->checkPerms('support');

		if (isset($_REQUEST['tol_admin']) && !$tol_admin)
			return PL_DO_AUTH;

		//////
		// Assignation des variables smarty
		//
		$page->changeTpl('trombino/tol.tpl');
		$page->assign('title', "Trombino");
		$page->assign('tol_admin', $tol_admin);
		$page->assign('page_raw', 1);
		$page->assign('nations', $this->get_nations());
		$page->assign('sections', $this->get_sections());
		$page->assign('binets', $this->get_binets());

/*		$promos = array();
		$promos['courantes'] = "Promos courantes";
		$promos['toutes'] = "Toutes les promos";
		$promos = $promos + $this->get_promos();*/
		$page->assign('promos', $this->get_promos());

		//////
		// On vérifie que nous avons bien une requete en cours.
		//
		if (!isset($_REQUEST['chercher']) &&
		    !isset($_REQUEST['section']) && 
		    !isset($_REQUEST['binet']) &&
		    !isset($_REQUEST['anniversaire']) && 
		    !isset($_REQUEST['promo']) && 
		    !isset($_REQUEST['anniversaire_week']) &&
		    empty($_REQUEST['q_search'])) 
			return;

		//////
		// Recuperation de la derniere promotion arrivee sur le campus
		//
		global $globals;
        $promo_tos = $globals->lastpromo;

		//////
		// Initialisation de la requete
		//
		$request = new TrombinoRequest;
		$request->add_option_to_constraint_group('valid_promo', 'eleves.piece_id', "", NOT_NULL, 1);


		//////
		// Requêtes des anniversaires
		// 
		if (isset($_REQUEST['anniversaire'])) 
			$request->add_constraint('eleves.date_nais', 'NOW()', DAYOFYEAR_MATCH);

		if (isset($_REQUEST['anniversaire_week']))
			$request->add_constraint('eleves.date_nais', 'NOW()', WEEK_MATCH);
		
		//////
		// Recherche tol rapide
		//
		if (isset($_REQUEST['cherchertol']))
		{
			$tokens = explode(" ", $_REQUEST['q_search']);

			$id = 0;
			foreach ($tokens as $token)
			{
				$id++;

				$request->add_option_to_constraint_group("valid_qsearch_$id", 'eleves.nom', 		$token, NEAR_MATCH, 1);
				$request->add_option_to_constraint_group("valid_qsearch_$id", 'eleves.prenom', 		$token, NEAR_MATCH, 1);
				$request->add_option_to_constraint_group("valid_qsearch_$id", 'eleves.surnom',		$token, NEAR_MATCH, 1);
				$request->add_option_to_constraint_group("valid_qsearch_$id", 'eleves.login',		$token, NEAR_MATCH, 1);
				$request->add_option_to_constraint_group("valid_qsearch_$id", 'eleves.piece_id',	$token, EXACT_MATCH, 1);
				$request->add_option_to_constraint_group("valid_qsearch_$id", 'pieces.tel',		$token, EXACT_MATCH, 1);
				$request->add_option_to_constraint_group("valid_qsearch_$id", 'eleves.promo',		$token, PROMO_MATCH, 1);

				$request->add_option_to_constraint_group('valid_promo',	      'eleves.promo',		$token, PROMO_MATCH, 1);
			}
		}

		//////
		// Prise en compte des champs standard ne nécessitant qu'un LIKE
		//
		$field_assoc = array('casert'	=>	'eleves.piece_id',
				     'phone'	=>	'pieces.tel',
				     'prise'	=>	'pieces.prise_id',
				     'ip'	=>	'pieces.ip',
				     'mac'	=>	'arpwatch.mac',
				     'nom'	=>	'eleves.nom',
				     'prenom'	=>	'eleves.prenom',
				     'surnom'	=>	'eleves.surnom',
				     'mail'	=>	'eleves.mail');


		foreach ($field_assoc as $field => $column)
		{
			if (!empty($_REQUEST[$field]))
				$request->add_constraint($column, $_REQUEST[$field], NEAR_MATCH);
		}

		//////
		// Champs plus complexes à gérer
		//
		if (!empty($_REQUEST['section']) && $_REQUEST['section'] != 'toutes')
		{
			$request->add_constraint('sections.section_id', $_REQUEST['section'], EXACT_MATCH);
			$request->reset_constraint_group('valid_promo');
			$request->add_option_to_constraint_group('valid_promo', 'eleves.promo', $promo_tos, EXACT_MATCH_NO_QUOTES);
			$request->add_option_to_constraint_group('valid_promo', 'eleves.promo', $promo_tos-1, EXACT_MATCH_NO_QUOTES);
		}
		
		if (!empty($_REQUEST['nation']) && $_REQUEST['nation'] != 'toutes')
			$request->add_constraint('eleves.nation', $_REQUEST['nation'], EXACT_MATCH);

		if (!empty($_REQUEST['binet']) && $_REQUEST['binet'] != 'tous')
			$request->add_constraint('binets.binet_id', $_REQUEST['binet'], EXACT_MATCH);

		if (!empty($_REQUEST['loginpoly'])) 
			$request->add_constraint('eleves.login', $_REQUEST['loginpoly'], EXACT_MATCH);

		if (!empty($_REQUEST['dns']))
		{
			$DB_xnet->query("SELECT  lastip
					   FROM  clients
					  WHERE  username LIKE '%{$_REQUEST['dns']}%'");
			
			while (list($ip) = $DB_xnet->next_row())
				$request->add_option_to_constraint_group('valid_dns', 'pieces.ip', $ip, EXACT_MATCH, 1);
		}

		if (isset($_GET['jeveuxvoirlesfillesdelecole']))
			$request->add_constraint('eleves.sexe', 1, EXACT_MATCH);
		
		//////
		// Attention! Le champ promo effaçant des conditions implicites sur la promo définies par d'autres champs, il est nécessaire de
		// le vérifier en dernier.
		//
		if (!empty($_REQUEST['promo']) && $_REQUEST['promo'] != 'courantes')
		{
			$request->reset_constraint_group('valid_promo');
			if ($_REQUEST['promo'] != 'toutes')
				$request->add_option_to_constraint_group('valid_promo', 'eleves.promo', $_REQUEST['promo'], EXACT_MATCH, 1);
		}

		//////
		// Envoi des résultats à Smarty
		//
		$results = $request->query($tol_admin);
		$page->assign('nbr_results', count($results));
		$page->assign('results', $results);
	}

	function handler_binets(&$page)
	{
		global $DB_trombino;

		$page->changeTpl('trombino/binets.tpl');
		$page->assign('title', "Binets");

		$DB_trombino->query("SELECT  binet_id, nom, description, http, folder,b.catego_id, categorie
 				       FROM  binets as b 
				  LEFT JOIN  binets_categorie as c USING(catego_id)
				      WHERE  NOT(http IS NULL AND folder='')
				   ORDER BY  b.catego_id ASC, b.nom ASC");
		
		$binets = array();
		$categories = array();
		while (list($id, $nom, $description, $http, $folder, $cat_id, $categorie) = $DB_trombino->next_row()) 
		{
			if ($folder != "")
			{
				if (FrankizSession::est_interne())
					$http = URL_BINETS."$folder/";
				else 
					$http = "binets/$folder/";
			}
		
			if (!isset($categories[$cat_id]))
			{
				$categories[$cat_id] = $categorie;
				$binets[$cat_id] = array();
			}

			$binets[$cat_id][] = array('id'          => $id,
					  	   'nom'         => $nom,
						   'description' => $description,
						   'http'        => $http,
						   'folder'      => $folder);
		}

		$page->assign('binets', $binets);
		$page->assign('categories', $categories);
	}
	
	function handler_tol_photo(&$page)
	{
		global $platal;
		
		$page->changeTpl('trombino/photo.tpl');
		$page->assign('title', "Photo");
		$page->assign('promo', $platal->argv[1]);
		$page->assign('login', $platal->argv[2]);
		$page->assign('original', ($platal->argv[3] == 'original'));
	}

	function handler_binets_logo(&$page)
	{
		global $DB_trombino, $platal;

		$binet_id = $platal->argv[1];
		
		$DB_trombino->query("SELECT image, format FROM binets WHERE binet_id='$binet_id'");
		list ($image, $format) = $DB_trombino->next_row() ;
	
		if (!$image)
			return PL_NOT_FOUND;

		header("Content-type: $format");
		echo $image;
		exit;
	}

	function handler_tol_photo_img(&$page)
	{
		global $DB_trombino, $platal;

		$promo = $platal->argv[1];
		$login = $platal->argv[2];
		$original = isset($platal->argv[3]) && $platal->argv[3] == 'original';

		if ($original)
			$file = BASE_PHOTOS."/{$promo}/{$login}_original.jpg";
		else
			$file = BASE_PHOTOS."/{$promo}/{$login}.jpg";
	
		if (!return_file($file))
			return PL_NOT_FOUND;
	}
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
