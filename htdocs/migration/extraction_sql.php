<? 
/*
nom,prenom,phone,casert,jour,mois,annee,section,cie,surnom,promo,mail,sexe,login,type,droitmodifphoto
*/

// En-tetes
require "../../include/global.inc.php";



connecter_mysql_tol();

$query = "SELECT * FROM trombino ORDER BY login,promo";
$result = mysql_query($query);
while(list($nom,$prenom,$phone,$casert,$jour,$mois,$annee,$section,$cie,$surnom,$promo,$mail,$sexe,$login,$type,$droitmodifphoto) = mysql_fetch_row($result)) { 
	$fichier_image = BASE_URL."/trombino/migration/genere_image_sql.php?login=$login&promo=$promo&type=image";
	echo $fichier_image."<br/>";
  	$handle = fopen ($fichier_image, "rb");
  	$file = fopen("../photos/".$promo."/".$login.".jpg", 'wb');
	while (!feof($handle)) {
  		fwrite($file,fread($handle, 8192));
	}
 	fclose ($handle);
	fclose($file);  
	
	$fichier_image = BASE_URL."/trombino/migration/genere_image_sql.php?login=$login&promo=$promo&type=original";
	echo $fichier_image."<br/>";
  	$handle2 = fopen ($fichier_image, "rb");
  	$file2 = fopen("../photos/".$promo."/".$login."_original.jpg", 'wb');
	while (!feof($handle2)) {
  		fwrite($file2,fread($handle2, 8192));
	}
 	fclose ($handle2);
	fclose($file2); 
}


deconnecter_mysql_tol();

?>