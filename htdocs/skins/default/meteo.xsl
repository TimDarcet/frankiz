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

<xsl:template match="module/meteo">
	<dl>
		<dt><img class="droitehaut" src="skins/default/images/cadre-hautd.gif" alt="" />
			<span>Météo</span>	
		</dt>
		<dd>
			<p class="image">
				<xsl:value-of select="now/temperature"/>°C<br />
				<img alt="meteo" width="64" height="64">
					<xsl:attribute name="src">skins/pico/images/meteo/<xsl:value-of select="now/image"/>.png</xsl:attribute>
				</img>
			</p>
		</dd>
		<dd class="bas"><img class="droitebas" src="skins/default/images/cadre-basd.gif" alt="" /></dd>
	</dl>
</xsl:template>

<xsl:template match="page/meteo">
	<dl>
		<dt><img class="droitehaut" src="skins/default/images/cadre-hautd.gif" alt="" />
			<span>Météo sur le Platâl aujourd'hui :</span>	
		</dt>
		<dd>
			Le soleil est présent de <xsl:value-of select="now/sunrise"/> à <xsl:value-of select="now/sunset"/><br />
			La température actuelle est de <xsl:value-of select="now/temperature"/>°C<br />
			La pression est de <xsl:value-of select="now/pression"/> millibar<br />
			Pour ce qui est du vent <xsl:value-of select="now/vent"/><br />
			Et l'humidité s'élève à <xsl:value-of select="now/humidite"/>%<br />
			L'état du ciel : <xsl:value-of select="now/ciel"/><br />
			<img alt="meteo" width="64" height="64">
				<xsl:attribute name="src">skins/pico/images/meteo/<xsl:value-of select="now/image"/>.png</xsl:attribute>
			</img>
		</dd>
		<dd class="bas"><img class="droitebas" src="images/cadre-basd.gif" alt="" /></dd>
	</dl>
	<div class="hr"><hr/></div>
	<dl>
		<dt><img class="droitehaut" src="skins/default/images/cadre-hautd.gif" alt="" />
			<span>Prévisions météo :</span>	
		</dt>
		<dd>
			<xsl:for-each select="jour">
				<h3>Prévision à <xsl:value-of select="@date"/> jours </h3>
					La température : <xsl:value-of select="temperature_hi"/>°C pendant la journée et <xsl:value-of select="temperature_low"/>°C la nuit<br />	
					Etat du ciel le jour : <xsl:value-of select="cieljour"/>
					<img alt="meteo" width="32" height="32">
						<xsl:attribute name="src">skins/pico/images/meteo/<xsl:value-of select="imagejour"/>.png</xsl:attribute>
					</img><br />
					Etat du ciel la nuit : <xsl:value-of select="cielnuit"/>
					<img alt="meteo" width="32" height="32">
						<xsl:attribute name="src">skins/pico/images/meteo/<xsl:value-of select="imagenuit"/>.png</xsl:attribute>
					</img><br />
			</xsl:for-each>
		</dd>
		<dd class="bas"><img class="droitebas" src="images/cadre-basd.gif" alt="" /></dd>
	</dl>
</xsl:template>



</xsl:stylesheet>