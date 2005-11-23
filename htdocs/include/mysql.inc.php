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

	$Id$

*/


class DB {
	var $link;
	var $result;
	var $host;
	var $base;
	var $user;
	var $pass;
	var $saved_results;
	
	/*
		Création d'une connexion à une base MySQL
	*/
	function DB($host,$base,$user,$pass) {
		global $_ERREURS_PHPMYSQL;
		$this->link = null;
		$this->pass = $pass;
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
		// Connection lors de la première requete.
		if($this->link == null){
			$this->link = mysql_connect($this->host,$this->user,$this->pass,true);
			if(mysql_get_server_info($this->link)>"4.1")
				mysql_query("SET NAMES 'utf8'",$this->link);

			if(mysql_errno() == 0)
				mysql_select_db($this->base,$this->link) || ajouter_erreur_mysql("USE ".$this->base);
			else {
				ajouter_erreur_mysql("CONNECT ".$this->user."@".$this->host);
			}
		}
		
		if($this->result)
			mysql_free_result($this->result);
		
		ajouter_debug_log("Requète SQL \"$query\"");
		$this->result = mysql_query($query,$this->link);
		
		if(mysql_errno() != 0 || is_bool($this->result) && $this->result == false)
			// FIXME : mysql_errno() renvoi anormalement toujours 0
			ajouter_erreur_mysql($query);

		if(is_bool($this->result) && $this->result)
			$this->result = false;
		
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
