<? 
/*
nom,prenom,phone,casert,jour,mois,annee,section,cie,surnom,promo,mail,sexe,login,type,droitmodifphoto
*/

// En-tetes
require "../../include/global.inc.php";



connecter_mysql_tol();

$query = "SELECT * FROM trombino ORDER BY login,promo";
$result = mysql_query($query);
while(list($nom,$prenom,$phone,$casert,$jour,$mois,$annee,$section,$cie,$surnom,$promo,$mail,$sexe,$login,$type,$trombino,$binets,$droitmodifphoto) = mysql_fetch_row($result)) { 
	$binets = "-";
	$query2 = "SELECT binet FROM membres WHERE login = \"$login\" AND promo = \"$promo\" ORDER BY binet";
	$result2 = mysql_query($query2) or die("PB PB PB\n");
	while($id = mysql_fetch_array($result2)) { 
		$binets .= "$id[0]-";
	}
	mysql_query("UPDATE trombino SET binets = \"$binets\" WHERE login = \"$login\" and promo = \"$promo\" LIMIT 1");
}


deconnecter_mysql_tol();
echo "OK Migration réussie";
?>