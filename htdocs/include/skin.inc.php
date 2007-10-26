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
	Fonction servant pour la gestion des skins.
	
	$Id$

*/

require_once "xml.inc.php";

/**
 * La liste des skins est definie dans skins/index.xml, et a la structure suivante:
 *      <default>«identifiant de la skin par default»</default>
 *	<skin id='id_de_la_skin'>
 *		<description>«sa description»</description>
 *		<css>«chemin d'accès à la feuille de style»</css>
 *	</skin>
 *      <skin>
 *        ...
 *      </skin>
 */
class Skin
{
	public $id;          // Identifiant unique pour la skin (lisible par un humain)
	public $description; // Description de la skin
	public $css_path;    // Chemin vers la feuille de style
	public $minimodules; // Liste des modules demandés par l'utilisateur.

	/**
	 * Charge la skin par defaut avec tous les minimodules
	 */
	public function __construct()
	{
		set_skin_default();
		set_minimodules_default();
	}
	
	// ----------------------------- Minimodules ---------------------------------
	public function set_minimodule_visible($module, $visible)
	{
		if ($visible)
			$this->minimodules[$module] = 1;
		else
			unset($this->minimodules[$module]);
	}

	public function est_minimodule_visible($module)
	{
		return isset($this->minimodules[$module]);
	}

	public function get_minimodules_list()
	{
		return array_keys($this->minimodules);
	}

	public function set_minimodules_default()
	{
		$list = FrankizMiniModule::get_minimodule_list();

		$this->minimodules = array();
		foreach ($list as $id => $desc)
			$this->set_minimodule_visible($id, true);
	}

	// ---------------------------------- Skin ------------------------------------
	/**
	 * Modifie la skin actuelle. Les parametres de la skin seront perdus, mais les minimodules
	 * actifs conservés.
	 *
	 * @param id l'identifiant de la skin ou la chaine vide pour recuperer la skin par defaut
	 * @return un booleen indiquant si la modification s'est faite correctement. En cas d'echec,
	 * l'ancienne skin est conservée.
	 */
	public function change_skin($id)
	{
		$xml_doc = new DOMDocument;
		$xml_doc->load(BASE_SKIN_INDEX);

		$xpath = new DOMXPath($xml_doc);
	
		if ($id == "")
			$id = xpath_evaluate($xpath, "/skins/default/text()");

		$node_list = $xpath->query("//skin[@id='$id']");
	
		if ($node_list->length == 0)
			return false;

		$node = $node_list->item(0);

		$this->id = $id;
		$this->description = xpath_evaluate_context($xpath, "description/text()", $node);
		$this->css_path = "skins/".xpath_evaluate_context($xpath, "css_path/text()", $node);
	}

	/**
	 * Lit la liste des skins disponibles (skins/index.xml)
	 * @return Un tableau indexé par les identifiants des skins donnant vers les descriptions.
	 */
	public static function get_skin_list()
	{
		$xml_doc = new DOMDocument;
		$xml_doc->load(BASE_SKIN_INDEX);

		$xpath = new DOMXPath($xml_doc);
		$node_list = $xpath->query("//skin");

		$skin_list = array();
		foreach ($node_list as $node)
		{
			$id = xpath_evaluate_context($xpath, "./@id", $node);
			$skin_list[$id] = xpath_evaluate_context($xpath, "./description/text()", $node);
		}

		return $skin_list;
	}


	/**
	 * Renvoie la skin par defaut.
	 */
	public static function set_skin_default()
	{
		return Skin::change_skin("");
	}


	// --------------------------------------------------------------------------------
	/**
	 * Transforme l'objet en une chaine de caractères pouvant ensuite être regenéré par
	 * unserialize.
	 */
	public function serialize()
	{
		return serialize(array('id'           => $this->id,
			               'minimodules'  => $this->minimodules));
	}

	/**
	 * Regénère un objet sauvegardé par serialize(). Fait les vérifications d'usage sur la
	 * chaine en paramètre, il est donc possible de donner du contenu utilisateur.
	 *
	 * Renvoie false en cas d'echec.
	 */
	public function unserialize($str)
	{
		$data = unserialize($str);
	
		if (!isset($data['id']) || !is_string($data['id']))
			return false;

		foreach ($data['modules'] as $key => $v)
		{
			if (!is_string($key) || $v != 1)
				return false;
		}
	
		if (!$this->change_skin($data['id']))
			return false;

		$skin->minimodules = $data['minimodules'];

		return $skin;	
	}
}

?>
