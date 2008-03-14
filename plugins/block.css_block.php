<?

function smarty_block_css_block($params, $content, &$smarty, &$repeat)
{
	$class = $params['class'];

	if (!$repeat)
	{
		echo "<div class='{$class}_1'><div class='{$class}_2'><div class='{$class}_3'><div class='{$class}_4'><div class='{$class}_5'><div class='{$class}_6'><div class='{$class}'>\n";
		echo "$content";
		echo "</div></div></div></div></div></div></div>\n";
	}
}

?>
