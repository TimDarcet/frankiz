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
	Gestion des connexions aux bases de donn�es�:
	- une seule connexion par base (avec l'utilisation de variable globales)
	- destruction automatique des r�sultats

	$Log$
	Revision 1.20  2005/02/08 21:57:56  pico
	Correction bug #62

	Revision 1.19  2004/12/10 17:55:24  kikx
	Je sais pas trop ...
	
	Revision 1.18  2004/12/06 00:01:42  kikx
	Passage de la skin par d�faut en parametre du site et non pas stock� en dur
	
	Revision 1.17  2004/11/27 16:27:33  pico
	�tablit la connexion � la bdd qu'� la premi�re requete.
	Utile si on n'a pas besoin de la connexion (genre xnet ou trombino)
	
	Revision 1.16  2004/11/17 22:18:45  schmurtz
	Demi correction d'un bug empechant l'affichage des erreurs MySQL
	
	Revision 1.15  2004/11/16 14:54:12  schmurtz
	Affichage des erreurs "Parse Error"
	permet de loguer des infos autre que les commandes SQL (pour debugage)
	
	Revision 1.14  2004/11/08 18:27:34  pico
	l� aussi �a devrait marcher, � tester
	
	Revision 1.13  2004/11/08 18:20:03  pico
	Devrait mieux marcher comme �a
	
	Revision 1.12  2004/11/08 17:01:22  schmurtz
	oups
	
	Revision 1.11  2004/11/08 16:59:26  schmurtz
	Retour en arriere : il faut faire comme ca tant que l'on est pas passe a un mysql > 4.2.0
	
	Revision 1.10  2004/11/05 07:55:59  pico
	Gwz a maintenant php > 4.2
	Le code donnait des warning, car on reselectionne 2 fois la m�me base dans le cas de xnet.
	
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
	var $pass;
	var $saved_results;
	
	/*
		Cr�ation d'une connexion � une base MySQL
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
		Ex�cution d'une requ�te
	*/
	function query($query) {
		// Connection lors de la premi�re requete.
		if($this->link == null){
			$this->link = mysql_connect($this->host,$this->user,$this->pass,true);
			if(mysql_errno() == 0)
				mysql_select_db($this->base,$this->link) || ajouter_erreur_mysql("USE ".$this->base);
			else {
				ajouter_erreur_mysql("CONNECT ".$this->user."@".$this->host);
			}
		}
		
		if($this->result)
			mysql_free_result($this->result);
		
		ajouter_debug_log("Requ�te SQL \"$query\"");
		$this->result = mysql_query($query,$this->link);
		
		if(mysql_errno() != 0 || is_bool($this->result) && $this->result == false)
			// FIXME : mysql_errno() renvoi anormalement toujours 0
			ajouter_erreur_mysql($query);

		if(is_bool($this->result) && $this->result)
			$this->result = false;
		
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
	
	/*
		Sauvegarde du r�sultat d'une requ�te
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
