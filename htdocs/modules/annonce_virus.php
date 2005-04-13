 <?php
/*
	Copyright (C) 2004 Binet R�seau
	http://www.polytechnique.fr/eleves/binets/br/
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
/*
	Pour faire peur aux gens qui ont des virus...
	
	$Log$
	Revision 1.1  2005/04/13 13:58:16  dei
	Voil� qui devrait faire peur � certains
	Bas� sur le script de fruneau module sur la page principale + page d'admin...

	
	
*/

if(est_authentifie(AUTH_INTERNE)) {
	/*On cherche dans la base les vilains qui ont un status solved diff�rent de 2, donc qui a priori sont infest�s. La personne est identifi�e par son ip, de l'ip on remonte � la chambre puis au mec.*/
	$DB_admin->query("SELECT e.eleve_id,p.piece_id,i.ip,i.date,CURDATE(),i.solved,i.id,l.nom FROM prises as p LEFT JOIN infections as i ON p.ip=i.ip LEFT JOIN liste_virus as l ON l.port=i.port LEFT JOIN trombino.eleves as e ON e.piece_id=p.piece_id WHERE NOT( i.solved='2') AND i.ip='{$_SERVER['REMOTE_ADDR']}'");
	if($DB_admin->num_rows()!=0){
		echo "<module id=\"virus\" titre=\"Important !\">\n";
		echo "<warning>";
		if($DB_admin->num_rows()==1){
			echo "<h2>Ton ordinateur est actuellement infect� par un virus !</h2>";
		}else{
			echo "<h2>Ton ordinateur est actuellement infect� par des virus !</h2>";
		}
		echo "<p>Tu as chopp� :</p>";
		echo "<ui>";
		$temp_rebours=10;
		list($eleve_id,$piece,$ip,$date,$rebours,$solved,$id,$nomv)=$DB_admin->next_row();
		/*On initialise la date, c'est sale mais je sais pas faire autrement...*/
		$min_date=$date;
		do
		{
			/*Calcul du nombre de jours avant coupure du r�seau.*/
			$rebours=$date+10-$rebours;
			$temp_rebours=min($rebours,$temp_rebours);
			/*Date de la plus ancienne infection courrante*/
			if($date<$min_date)
				$min_date=$date;
			echo "<li>$nomv, depuis le ".preg_replace('/^(.{4})-(.{2})-(.{2})$/','$3-$2-$1', $date)."</li>";
		}
		while(list($eleve_id,$piece,$ip,$date,$rebours,$solved,$id,$nomv)=$DB_admin->next_row());
		echo "</ui>";
		$avert="<p>Depuis le ".preg_replace('/^(.{4})-(.{2})-(.{2})$/','$3-$2-$1', $min_date)." tu mets en danger le r�seau...";
		if ($temp_rebours>0){
			$avert=$avert." Afin d'�viter la propagation des virus, nous allons devoir te couper le r�seau dans $temp_rebours jours . Lorsque tu t'en sera d�barrass� signale le � un admin@windows.</p>";
		} else {
			$avert=$avert." Nous avons du te couper le r�seau... Lorsque tu te sera d�barrass� de ce virus signale le � un admin@windows.</p>";
		}
		echo "$avert";
		echo "</warning>";
		echo "</module>\n";
		/*On signale que l'utilisateur est pr�venu...*/
		$DB_admin->query("UPDATE infections SET solved='1' WHERE solved='0' AND ip='{$_SERVER['REMOTE_ADDR']}'");
	}
}

?>