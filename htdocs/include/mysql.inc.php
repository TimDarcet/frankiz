<?php
/*
	$Id$
	
	Gestion des connexions aux bases de donn�es�:
	- une seule connexion par base (avec l'utilisation de variable globales)
	- destruction automatique des r�sultats
*/

class DB {
	var $link;
	var $result;
	var $host;
	var $base;
	var $user;
	
	/*
		Cr�ation d'une connexion � une base MySQL
	*/
	function DB($persistent,$host,$base,$user,$pass) {
		global $_ERREURS_PHPMYSQL;
		if($persistent)
			$this->link = mysql_pconnect($host,$user,$pass);
		else
			$this->link = mysql_connect($host,$user,$pass);
		
		if($this->link)
			mysql_select_db($base) || ajouter_erreur_mysql("USE $base");
		else
			ajouter_erreur_mysql("CONNECT $user@$host");
		
		$this->host = $host;
		$this->base = $base;
		$this->user = $user;
		$this->result = false;
	}
	
	function close() {
		mysql_close($this->link);
	}
	
	/*
		Ex�cution d'une requ�te
	*/
	function query($query) {
		if($this->result)
			mysql_free_result($this->result);
		$this->result = mysql_query($query,$this->link);
		
		if(is_bool($this->result) && $this->result)
			$this->result = false;
		
		if(!$this->result)
			ajouter_erreur_mysql($query);
	}
	
	/*
		R�cup�ration du r�sultat de la derni�re requ�te.
		(SELECT uniquement)
	*/
	function next_row() {
		return $this->result ? mysql_fetch_row($this->result) : false;
	}

	function num_rows() {
		return $this->result ? mysql_num_rows($this->result) : 0;
	}
	
	/*
		Informations sur les modifications effectu�es � la base lors de la derni�re requ�te
	*/
	function affected_rows() {
		return mysql_affected_rows($this->link);
	}

	function insert_id() {
		return mysql_insert_id($this->link);
	}
}
?>