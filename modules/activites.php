<?php
class ActivitesModule extends PLModule
{
	function handlers()
	{
		return array("activites" => $this->make_hook("activites", AUTH_PUBLIC));
	}
	
	function handler_activites(&$page)
	{
		global $DB_web;
	
		$page->assign('title', 'Frankiz : activités de la semaine');
		$page->changeTpl("activites/activites.tpl");
		
		// Etat Bob
		$page->assign('bob_ouvert', getEtatBob());

		// Etat Kes
		$DB_web->query("SELECT valeur FROM parametres WHERE nom='kes'");
		list($valeur_kes) = $DB_web->next_row();
		$page->assign('kes_ouverte', $valeur_kes);

		// Autres activités
		if (!FrankizSession::est_interne()) 
			$exterieur_rule = "AND  exterieur = '1'";
		else
			$exterieur_rule = "";
		
		$DB_web->query("SELECT  affiche_id, titre, url, DATE(date), date, description 
		                  FROM  affiches 
				 WHERE  DATEDIFF(date, NOW()) < 7
				   AND  DATEDIFF(date, NOW()) >= 0
				        $exterieur_rule
			      ORDER BY  date");

		while (list($id, $titre, $url, $date, $datetime, $texte) = $DB_web->next_row()) 
		{
			$activites[$date][] = array('id' => $id,
					            'titre' => $titre,
						    'url' => $url,
						    'date' => $date,
						    'texte' => $texte);
		}
		$page->assign('activites', $activites);
	}
}

