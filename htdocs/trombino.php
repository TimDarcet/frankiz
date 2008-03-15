<?

// Recuperation d'une image

if (isset($_GET['tdb']) && isset($_GET['promo'])){
	$DB_trombino->query("SELECT login, nom, prenom FROM eleves WHERE promo = '{$_GET['promo']}' ORDER BY promo, nom, prenom ASC");
	echo "#\n";
	while (list($login,$nom,$prenom) = $DB_trombino->next_row())
		echo "$login:$nom:$prenom\n";
	echo "#\n";
	exit;
}
?>
