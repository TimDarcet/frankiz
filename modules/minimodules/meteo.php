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
	Permet de donner la météo sur Paris (cf. meteo_func.inc.php)
		
	$Id$

*/
require_once BASE_FRANKIZ."htdocs/include/xml.inc.php";

class MeteoMiniModule extends FrankizMiniModule
{
	private $meteo;

	public function __construct()
	{
		global $page;
		
		if (!$this->get_data())
			return;

		$this->assign("meteo", $this->meteo);
		$this->tpl = "minimodules/meteo/meteo.tpl";
		$this->titre = "Météo";
	}

	private function get_data()
	{
		// Récupération de la météo
		$proxy = "kuzh.polytechnique.fr";
		$port = 8080;

		$fp = fsockopen($proxy, $port);
		fputs($fp, "GET ".WEATHER_DOT_COM." HTTP/1.0\r\nHost: $proxy\r\n\r\n");
		$xml = "";
		while(!feof($fp)){
			$xml .= fgets($fp, 4000);
		}
		$xml = strstr($xml,"<?xml");	// TODO corriger ce gros hack, vérifier aussi que la requète
						// http à réussie
	
		// traduction de la météo dans notre format
		if (!strstr($xml,"<weather"))
			return false;

		$dom_xml = new DOMDocument ();
		$dom_xml->loadXML($xml);

		$dom_xml_ext = new DOMXPath($dom_xml);
		$this->meteo['sunrise'] = xpath_evaluate($dom_xml_ext, "/weather/loc/sunr/text()");
		$this->meteo['sunset'] = xpath_evaluate($dom_xml_ext, "/weather/loc/suns/text()");
		$this->meteo['temperature'] = xpath_evaluate($dom_xml_ext, "/weather/cc/tmp/text()");
		$this->meteo['ciel_icon'] = xpath_evaluate($dom_xml_ext, "/weather/cc/icon/text()");
		$this->meteo['pression'] = xpath_evaluate($dom_xml_ext, "/weather/cc/bar/r/text()");
		$this->meteo['vent'] = xpath_evaluate($dom_xml_ext, "/weather/cc/wind/s/text()");
		$this->meteo['humidite'] = xpath_evaluate($dom_xml_ext, "/weather/cc/hmid/text()");

		return true;
	}

	public static function check_auth()
	{
		return true;
	}
}
FrankizMiniModule::register_module("meteo", "MeteoMiniModule", "Météo actuelle");
?>
