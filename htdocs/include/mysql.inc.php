<?php
/*
	Copyright (C) 2004 Binet Réseau
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
	Gestion des connexions aux bases de données :
	- une seule connexion par base (avec l'utilisation de variable globales)
	- destruction automatique des résultats

	$Log$
	Revision 1.12  2004/11/08 17:01:22  schmurtz
	oups

	Revision 1.11  2004/11/08 16:59:26  schmurtz
	Retour en arriere : il faut faire comme ca tant que l'on est pas passe a un mysql > 4.2.0
	
	Revision 1.10  2004/11/05 07:55:59  pico
	Gwz a maintenant php > 4.2
	Le code donnait des warning, car on reselectionne 2 fois la même base dans le cas de xnet.
	
	Revision 1.9  2004/10/21 22:19:37  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.8  2004/10/20 23:11:51  schmurtz
	Suppression de la limitation de deux appels imbriques a mysql.
	
	Revision 1.7  2004/09/15 23:19:31  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
	Revision 1.6  2004/09/15 21:42:08  schmurtz
	Commentaires et ajout de la variable cvs "Log"
	
*/

class DB {
	var $link;
	var $result;
	var $host;
	var $base;
	var $user;
	var $saved_results;
	
	/*
		Création d'une connexion à une base MySQL
	*/
	function DB($host,$base,$user,$pass) {
		global $_ERREURS_PHPMYSQL;
		$this->link = mysql_connect($host,$user,$pass/*,true*/);	// PHP 4.2.0 seulement
		
		if(mysql_errno() == 0)
			mysql_select_db($base) || ajouter_erreur_mysql("USE $base");
		else
			ajouter_erreur_mysql("CONNECT $user@$host");
		
		$this->host = $host;
		$this->base = $base;
		$this->user = $user;
		$this->result = false;
		$this->saved_results = array();
	}
	
	function close() {
		mysql_close($this->link);
	}
	
	/*
		Exécution d'une requète
	*/
	function query($query) {
		if($this->result)
			mysql_free_result($this->result);
		
		mysql_select_db($this->base); // TODO à changer, très moche (maj PHP > 4.2.0 par exemple)
		ajouter_requete_mysql($query);
		$this->result = mysql_query($query,$this->link);
		
		if(is_bool($this->result) && $this->result)
			$this->result = false;
		
		if(mysql_errno() != 0)
			ajouter_erreur_mysql($query);
	}
	
	/*
		Récupération du résultat de la dernière requète.
		(SELECT uniquement)
	*/
	function next_row() {
		return $this->result ? mysql_fetch_row($this->result) : false;
	}

	function num_rows() {
		return $this->result ? mysql_num_rows($this->result) : 0;
	}
	
	/*
		Informations sur les modifications effectuées à la base lors de la dernière requète
	*/
	function affected_rows() {
		return mysql_affected_rows($this->link);
	}

	function insert_id() {
		return mysql_insert_id($this->link);
	}
	
	/*
		Sauvegarde du résultat d'une requète
	*/
	function push_result() {
		$this->saved_results[count($this->saved_results)] = $this->result;
		$this->result = false;
	}

	function pop_result() {
		$last = count($this->saved_results)-1;
		$this->result = $this->saved_results[$last];
		unset($this->saved_results[$last]);
	}
}
?>