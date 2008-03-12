<?

function smarty_modifier_date_format_humain($string)
{
	$day_now = floor(time() / (24*3600));
	$day_target = floor(strtotime($string) / (24*3600));

	switch ($day_target - $day_now)
	{
	case '0':
		return "Aujourd'hui";
	case '1':
		return "Demain";
	case '2':
		return "Après-demain";
	case '7':
		return "Dans une semaine";
	case '-1':
		return "Hier";
	case '-2':
		return "Avant-hier";
	default:
		if ($day_target - $day_now > 0)
			return "Dans ".($day_target - $day_now)." jours";
		else
			return "Il y a ".($day_now - $day_target)." jours";
	}
}

?>
