<? 
/*
nom,prenom,phone,casert,jour,mois,annee,section,cie,surnom,promo,mail,sexe,login,type,droitmodifphoto
*/

require_once BASE_LOCAL."/include/trombino.inc.php";

if(($_REQUEST['image'] == "true") && ($_REQUEST['image'] != "")){
	require_once("../include/global.inc.php");
	trombi_image($_REQUEST['login'],$_REQUEST['promo']);
}else{
trombi_id_binet();

	if(($_REQUEST['nom']!="") || ($_REQUEST['prenom']!="") || ($_REQUEST['phone']!="") || ($_REQUEST['casert']!="") || ($_REQUEST['jour']!="") || ($_REQUEST['mois']!="") || ($_REQUEST['annee']!="") || ($_REQUEST['section']!="") || ($_REQUEST['cie']!="") || ($_REQUEST['surnom']!="") || ($_REQUEST['promo']!="") || ($_REQUEST['mail']!="") || ($_REQUEST['sexe']!="") || ($_REQUEST['login']!="") || ($_REQUEST['type']!="") || ($_REQUEST['droitmodifphoto']!="") || ($_REQUEST['binet']!="")){

echo "<module id=\"trombino\" visible=\"true\">";
echo trombi_recherche($_REQUEST['nom'],$_REQUEST['prenom'],$_REQUEST['phone'],$_REQUEST['casert'],$_REQUEST['jour'],$_REQUEST['mois'],$_REQUEST['annee'],$_REQUEST['section'],$_REQUEST['cie'],$_REQUEST['surnom'],$_REQUEST['promo'],$_REQUEST['mail'],$_REQUEST['sexe'],$_REQUEST['login'],$_REQUEST['type'],$_REQUEST['droitmodifphoto'],$_REQUEST['binet']);

echo "</module>";
	}else{

?>
	<module id="trombino" visible="true">
		<formulaire id="trombino" action="trombino/">
			<champ titre="Nom" id="nom" valeur="" />
			<champ titre="Prénom" id="prenom" valeur="" />
			<champ titre="Surnom" id="surnom" valeur="" />
			<choix titre="Promo" id="promo" type="combo" valeur="">
					<option titre="Toutes" id="" />
					<option titre="2003" id="2003" />
					<option titre="2002" id="2002" />
					<option titre="2001" id="2001" />
					<option titre="2000" id="2000" />
					<option titre="1999" id="1999" />
					<option titre="1998" id="1998" />
			</choix>
			
			<choix titre="Section" id="section" type="combo" valeur="">
					<option titre="Toutes" id=""/>
					<option titre="Judo" id="judo"/>
					<option titre="Athle" id="athle"/>
					<option titre="Aviron" id="aviron"/>
					<option titre="Basket" id="basket"/>
					<option titre="Cadre" id="cadre"/>
					<option titre="Co" id="co"/>
					<option titre="Equitation" id="equitation"/>
					<option titre="Escalade" id="escalade"/>
					<option titre="Escrime" id="escrime"/>
					<option titre="Foot" id="foot"/>
					<option titre="Golf" id="golf"/>
					<option titre="Hand" id="hand"/>
					<option titre="Natation" id="natation"/>
					<option titre="Rugby" id="rugby"/>
					<option titre="Tennis" id="tennis"/>
					<option titre="Voile" id="voile"/>
					<option titre="Volley" id="volley"/>
			</choix>
				
			<choix titre="Sexe" id="sexe" type="combo" valeur="">
					<option titre="Tous" id="" />
					<option titre="Femme" id="1" />
					<option titre="Homme" id="0" />
			</choix>
	<?
			echo "<choix titre=\"Binet\" id=\"binet\" type=\"combo\" valeur=\"\">\n";
			echo "<option titre=\"Tous\" id=\"\"/>\n";
			
			$i=0;
			while ($i<count($trombi_id_binet)) {
				if($trombi_id_binet[$i] != ""){
				echo "<option titre=\"".$trombi_id_binet[$i];
				echo "\" id=\"-$i-\"/>\n";
				}
			$i++;
			}
			echo "</choix>";
	?>
			<champ titre="Login" id="login" valeur="" />
			<champ titre="Phone" id="phone" valeur="" />
			<champ titre="Casert" id="casert" valeur="" />
			<choix titre="Type" id="type" type="combo" valeur="">
				<option titre="Tous" id=""/>
				<option titre="Eleves" id="0"/>
				<option titre="Bar" id="1"/>
				<option titre="Cadres" id="2"/>
			</choix>
			<bouton titre="Effacer" id="reset" />
			<bouton titre="Chercher" id="chercher" />
		</formulaire>
	</module>
<?
}

}


?>