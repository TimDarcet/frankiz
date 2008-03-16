<?

function smarty_function_print_dir_aux($dirpath)
{
	$dir = opendir($dirpath);

	while (($fich = readdir($dir)) !== FALSE)
	{
		if ($fich == "." || $fich == "..")
			continue;

		$pathfich = "$dirpath/$fich";

		if (is_dir($pathfich))
		{
			echo "<li class='noeud_ouvert'>$fich</li>\n";
			echo "<ul>\n";
			smarty_function_print_dir_aux($pathfich);
			echo "</ul>\n";
		}
		else
			echo "<li class='feuille'>$fich</li>\n";
	}
}

function smarty_function_print_dir($params, &$smarty)
{
	echo "<ul>\n";
	if (isset($params['dir']))
		smarty_function_print_dir_aux($params['dir']);
	echo "</ul>\n";
}

?>
