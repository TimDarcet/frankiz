<?php
require_once "../include/global.inc.php";
demande_authentification(AUTH_MINIMUM);

function xnet_stat_error ()
{
	header('Content-type: image/png');
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 300) . ' GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
	$im  = imagecreate (550, 100);
	$bgc = imagecolorallocatealpha ($im, 255, 255, 255, 255);
	$tc  = imagecolorallocatealpha ($im, 0, 0, 0, 0);
	imagefilledrectangle ($im, 0, 0, 150, 30, $bgc);
	imagestring ($im, 5, 10, 35, xnet_prepare_text("Une erreur est survenue lors de la g&eacute;n&eacute;ration de l'image."), $tc);
	imagestring ($im, 5, 10, 50, "Merci de bien vouloir contacter web@frankiz.polytechnique.fr", $tc);
	imagepng($im);
	imagedestroy($im);
}
function xnet_prepare_text ($str)
{
	return html_entity_decode($str, ENT_NOQUOTES, "ISO-8859-1");
}

// Récupération du type d'image
$type = 'daily';
if (isset($_REQUEST['weekly']))
	$type = 'weekly';
else if (isset($_REQUEST['monthly']))
	$type = 'monthly';
else if (isset($_REQUEST['yearly']))
	$type = 'yearly';

// Controle de l'existence de la base rrd
$rrd = BASE_DATA . 'xnet.rrd';
$image = BASE_CACHE . "xnet-$type.png";
if (!file_exists($rrd) || !is_readable($rrd))
{
	xnet_stat_error();
	exit();
}

// Dates utiles
$date_rrd = @filemtime(BASE_DATA.'xnet.rrd');
$delta = 300;

// Test de l'existence de l'image
if (!file_exists($image) || @filemtime($image) < $date_rrd)
{
	// Appel du rrdtool
	$rrdtool = "rrdtool graph $image --lazy --vertical-label 'clients' ";
	switch ($type)
	{
		case "weekly":
			$rrdtool .= "--start -8d   --title 'Statistiques xNet (une semaine)' ";
			break;
		case "monthly":
			$rrdtool .= "--start -36d  --title 'Statistiques xNet (un mois)' ";
			break;
		case "yearly":
			$rrdtool .= "--start -396d --title 'Statistiques xNet (un an)' ";
			break;
		default:
			$rrdtool .= "--slope-mode ";
			$rrdtool .= "--start -30h  --title 'Statistiques xNet (24 heures)' ";
			break;
	}
	$rrdtool .= "DEF:clients=$rrd:clients:AVERAGE ";
	$rrdtool .= "DEF:maxclients=$rrd:clients:MAX ";
	if ($type == "daily")
	{
		$rrdtool .= "VDEF:last=clients,LAST ";
		$rrdtool .= "GPRINT:last:'".xnet_prepare_text("Connect&eacute;s\\: %4.0lf%S clients      ' ");
		$rrdtool .= "AREA:clients#00ff00:'".xnet_prepare_text("clients connect&eacute;s")."' ";
	}
	else
	{
		$rrdtool .= "VDEF:max=clients,MAXIMUM ";
		$rrdtool .= "GPRINT:max:'Maximum\\:   %4.0lf%S clients      ' ";
		$rrdtool .= "AREA:clients#00ff00:'moyenne' ";
		$rrdtool .= "LINE1:maxclients#0000ff:maximum ";
	}

	$lines = array();
	$Y = date('Y');
	$m = date('m');
	$d = date('d');
	switch ($type)
	{
		case "weekly":
			$N = date('N');
			$lines[] = mktime(0, 0, 0, $m, $d - ($N - 1) - 7, $Y);
			$lines[] = mktime(0, 0, 0, $m, $d - ($N - 1), $Y);
			break;
		case "monthly":
			$lines[] = mktime(0, 0, 0, $m - 1, 1, $Y);
			$lines[] = mktime(0, 0, 0, $m, 1, $Y);
			break;
		case "yearly":
			$lines[] = mktime(0, 0, 0, 1, 1, $Y - 1);
			$lines[] = mktime(0, 0, 0, 1, 1, $Y);
			break;
		default:
			$lines[] = mktime(0, 0, 0, $m, $d - 1, $Y);
			$lines[] = mktime(0, 0, 0, $m, $d, $Y);
	}
	foreach($lines as $line)
	{
		$rrdtool .= "VRULE:$line#ff0000 ";
	}

	exec($rrdtool);
}

if (!file_exists($image) || !is_readable($image))
{
	xnet_stat_error();
	exit();
}

// Output de l'image
$date_image = @filemtime($image);
header('Content-Type: image/png');
header('Expires: ' . gmdate('D, d M Y H:i:s', ($date_image + $delta)) . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $date_image) . ' GMT');
header("Content-Disposition: inline; filename=xnet-$type.png");
readfile($image);
?>
