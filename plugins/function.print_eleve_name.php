<?

function smarty_function_print_eleve_name($params, &$smarty)
{
	$name = $params['eleve']['surnom'] ? 
		$params['eleve']['surnom'] :
		"{$params['eleve']['nom']} {$params['eleve']['prenom']}";

	if (isset($params['show_promo']))
		$name = "$name (X{$params['eleve']['promo']})";

	if (FrankizSession::verifie_permission("interne"))
		$name = "<a href='tol/chercher&loginpoly={$params['eleve']['loginpoly']}&promo={$params['eleve']['promo']}'>$name</a>";
	
	return $name;
}

?>
