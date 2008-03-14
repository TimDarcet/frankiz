<?

// Recuperation d'une image
if (!empty($_GET['image']) && ($_GET['image'] == 'true')){
	require_once "include/global.inc.php";

	if (!verifie_permission('interne'))
		exit;

	if (!isset($_GET['original']) && (file_exists(BASE_PHOTOS.$_GET['promo'].'/'.$_GET['login'].'.jpg'))) {
		$file = BASE_PHOTOS.$_GET['promo']."/".$_GET['login'].".jpg";
	} else {
		$file = BASE_PHOTOS.$_GET['promo'].'/'.$_GET['login'].'_original.jpg';
	}

	$size = getimagesize($file);

	header("Content-type: {$size['mime']}");

	readfile($file);
	exit;
}

if (isset($_GET['tdb']) && isset($_GET['promo'])){
	$DB_trombino->query("SELECT login, nom, prenom FROM eleves WHERE promo = '{$_GET['promo']}' ORDER BY promo, nom, prenom ASC");
	echo "#\n";
	while (list($login,$nom,$prenom) = $DB_trombino->next_row())
		echo "$login:$nom:$prenom\n";
	echo "#\n";
	exit;
}
?>
