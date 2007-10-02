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

class MeteoMiniModule extends FrankizMiniModule
{
	private $meteo;

	public function __construct()
	{
		global $page;
		
		if (!$this->get_data())
			return;

		$page->assign("meteo", $this->meteo);
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
		$this->meteo['sunrise'] = $dom_xml_ext->query("/weather/loc/sunr/text()");
		$this->meteo['sunset'] = $dom_xml_ext->query("/weather/loc/suns/text()");
		$this->meteo['temperature'] = $dom_xml_ext->query("/weather/cc/tmp/text()");
		$this->meteo['ciel_icon'] = $dom_xml_ext->query("/weather/cc/icon/text()");
		$this->meteo['pression'] = $dom_xml_ext->query("/weather/cc/bar/r/text()");
		$this->meteo['vent'] = $dom_xml_ext->query("/weather/cc/wind/s/text()");
		$this->meteo['humidite'] = $dom_xml_ext->query("/weather/cc/hmid/text()");

		return true;
	}

	public static function check_auth()
	{
		return true;
	}
}
FrankizMiniModule::register_module("meteo", "MeteoMiniModule");
?>
