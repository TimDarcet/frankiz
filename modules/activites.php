<?php
class ActivitesModule extends PLModule
{
	function handlers()
	{
		return array("activites" => $this->make_hook("activites", AUTH_PUBLIC));
	}
	
	function handler_activites(&$page)
	{
		global $DB_web;
	
		$page->assign('title', 'Frankiz : activités de la semaine');

echo "<page id='activites'>\n";

// Etat du bôb
$valeurBob = getEtatBob();
// Etat de la Kes
$DB_web->query("SELECT valeur FROM parametres WHERE nom='kes'");
list($valeurKes) = $DB_web->next_row();


	
if(verifie_permission('interne')){ 
	echo ($valeurBob == 1)?"<annonce titre=\"Le BôB est ouvert\"/>":"<annonce><em>Le BôB est fermé</em></annonce>";
	echo ($valeurKes == 1)?"<annonce titre=\"La Kès est ouverte\"/>":"<annonce><em>La Kès est fermée</em></annonce>";
}

$date_legend = array("Aujourd'hui","Demain","Après-demain","Dans 3 jours","Dans 4 jours","Dans 5 jours","Dans une semaine");
$exterieur = "";
if(!verifie_permission('interne')) $exterieur=" AND exterieur='1' ";

for($i= 0; $i<7;$i++){
	$DB_web->query("SELECT affiche_id,titre,url,date,description FROM affiches WHERE TO_DAYS(date)=TO_DAYS(NOW() + INTERVAL $i DAY) $exterieur ORDER BY date");
	if ($DB_web->num_rows()!=0){
		echo "<h3>{$date_legend[$i]}</h3>";
		while (list($id,$titre,$url,$date,$texte)=$DB_web->next_row()) { 
		?>
			<annonce date="<?php echo $date ?>">
			<lien url="<?php echo $url?>"><image source="<?php echo DATA_DIR_URL.'affiches/'.$id?>" texte="Affiche" legende="<?php echo $titre?>"/></lien>
			<?php echo wikiVersXML($texte); ?>
			</annonce>
		<?php
		}
	}
}

echo "</page>\n";

	}
}

