<?xml version="1.0" encoding="ISO-8859-1" ?>
<!-- $Id$ -->
<!--
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
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<!-- Meteo de l'X -->


<xsl:template match="page/meteo">
	<h2>Météo sur le Platâl aujourd'hui :</h2><br />
		Le soleil est présent de <xsl:value-of select="now/sunrise"/> à <xsl:value-of select="now/sunset"/><br />
		La température actuelle est de <xsl:value-of select="now/temperature"/>°C<br />
		La pression est de <xsl:value-of select="now/pression"/> millibar<br />
		Pour ce qui est du vent <xsl:value-of select="now/vent"/><br />
		Et l'humidité s'élève à <xsl:value-of select="now/humidite"/>%<br />
		L'état du ciel : <xsl:value-of select="now/ciel"/><br />
		<img alt="meteo" width="64" height="64">
			<xsl:attribute name="src">skins/pico/images/meteo/<xsl:value-of select="now/image"/>.png</xsl:attribute>
		</img>
	<h2>Prévisions météo :</h2><br />
		<xsl:for-each select="jour">
			<xsl:if test="@date = '0'"><h3>Aujourd'hui</h3></xsl:if>
			<xsl:if test="@date = '1'"><h3>Demain</h3></xsl:if>
			<xsl:if test="@date = '2'"><h3>Après demain</h3></xsl:if>
			<xsl:if test="@date &gt; '2'"><h3>Dans <xsl:value-of select="@date"/> jours </h3></xsl:if>
				La température : <xsl:value-of select="temperature_hi"/>°C pendant la journée et <xsl:value-of select="temperature_low"/>°C la nuit<br />	
				Etat du ciel le jour : <xsl:value-of select="cieljour"/>
				<img alt="meteo" width="32" height="32">
					<xsl:attribute name="src">skins/pico/xsl/meteo/<xsl:value-of select="imagejour"/>.png</xsl:attribute>
				</img><br />
				Etat du ciel la nuit : <xsl:value-of select="cielnuit"/>
				<img alt="meteo" width="32" height="32">
					<xsl:attribute name="src">skins/pico/xsl/meteo/<xsl:value-of select="imagenuit"/>.png</xsl:attribute>
				</img><br />
		</xsl:for-each>
</xsl:template>

<xsl:template match="module[@id='meteo']">
	<div class="fkz_module"  id='mod_meteo'>
		<div class="fkz_titre"><span id="meteo_logo"><xsl:text> </xsl:text></span><span id="meteo">La météo</span></div>
		<div class="fkz_module_corps">
			<xsl:value-of select="meteo/now/temperature"/>°C<br />
			<img alt="meteo" width="64" height="64">
				<xsl:attribute name="src">skins/pico/xsl/meteo/<xsl:value-of select="meteo/now/image"/>.png</xsl:attribute>
			</img>
		</div>
	</div>
</xsl:template>

</xsl:stylesheet>