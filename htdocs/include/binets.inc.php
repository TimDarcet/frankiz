<?PHP

//
// Connexion à la base de données
// -----------

$hote = "frankiz";
$user = "web";
$pass = "kokouije?.";
$db   = "frankiz2_tmp";

function connecter_binets() {
  global $hote,$user,$pass,$db,$db_binets;
  $db_binets = mysql_connect($hote, $user, $pass);
  mysql_select_db($db,$db_binets);
}

function deconnecter_binets() {
  mysql_close();
}

//
// Affichage d'un binet
// -----------
function affiche_binet($id) {

  global $db_binets, $cat;

  $query = "SELECT login,date,nom,descript,http FROM binets WHERE id=$id ORDER BY nom";
  $binet = mysql_fetch_assoc(mysql_query($query,$db_binets));
  
?>
    <binet id="<?PHP echo $id ?>" catego="<?PHP echo $cat['catego']; ?>" nom="<?PHP echo $binet['nom']; ?>">
      <login><?PHP echo $binet['login'] ?></login>
      <date><?PHP echo $binet['date'] ?></date>
      <descript><?PHP echo $binet['descript'] ?></descript>
      <url><?PHP echo $binet['http'] ?></url>
    </binet>
<?
}

?>
