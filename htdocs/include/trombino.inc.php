<? 
/*
Recherche d'un élève dans le trombi
*/


function trombi_recherche($nom,$prenom,$phone,$casert,
			$jour,$mois,$annee,$section,
			$cie,$surnom,$promo,$mail,
			$sexe,$login,$type,$droitmodifphoto,$binet){
	$resultat = "";
	$champs_dispo = array(
			"nom","prenom","phone","casert","jour","mois","annee",
			"section","cie","surnom","promo","mail","sexe","login",
			"type","droitmodifphoto"
			);
		


	connecter_mysql_tol();

	$query = "SELECT trombino.* FROM trombino ";
	$query .= "WHERE ";
	reset($champs_dispo);
	$prems = 1; 
	while (list(, $valeur) = each ($champs_dispo)) {
		if(${$valeur} != ""){
			if($prems != 1) $query .= " and ";
			$query .= "trombino.$valeur = \"".${$valeur}."\"";
			$prems = 0;
		}
	}
	if($binet != ""){
		if($prems != 1) $query .= " and ";
		$query .= "binets LIKE \"%$binet%\"";
	}
	$query .= " ORDER BY nom";
	$result = mysql_query($query);
	while(list($nom,$prenom,$phone,$casert,$jour,$mois,$annee,$section,$cie,$surnom,$promo,$mail,$sexe,$login,$type,$droitmodifphoto,$binets) = mysql_fetch_row($result)) { 
		$resultat .= "<eleve nom='$nom' prenom='$prenom' promo='$promo' login='$login' surnom='$surnom' tel='$phone' mail='$mail' casert='$casert' naissance='$jour/$mois/$annee' section='$section' cie='$cie'>";
		if($binets != "")
			$resultat .= trombi_binet($binets);
		$resultat .= "</eleve>";
	}
	
	
	deconnecter_mysql_tol();
	return $resultat;
}

function trombi_image($login,$promo){
	header('content-type: image/jpeg');
	readfile(BASE_PHOTOS."$promo/$login.jpg");
}

function trombi_binet($chaine){
	global $trombi_id_binet;
	$retour = "";
	$chaine = ereg_replace("^\-","", $chaine);
	$chaine = ereg_replace("\-$","", $chaine);
	$id = split("-", $chaine);
	foreach($id as $value){
		$retour .= "<binet>".$trombi_id_binet[$value]."</binet>\n";
	}
	return $retour;
}

function trombi_id_binet(){
	global $trombi_id_binet;
	connecter_mysql_tol();

	$query = "SELECT id,nom FROM binets ORDER BY id";
	
	$result = mysql_query($query);
	while(list($id,$nom) = mysql_fetch_row($result)) { 
	$trombi_id_binet[$id] = $nom;
	}
	
	
	deconnecter_mysql_tol();
	return $resultat;
}