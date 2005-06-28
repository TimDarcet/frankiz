<?xml version="1.0" encoding="UTF-8" ?>
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
		<span  class="meteo"><img alt="meteo" width="64" height="64">
			<xsl:choose>
				<xsl:when test="now/image!='-'">
					<xsl:attribute name="src">skins/xhtml/xsl/meteo/<xsl:value-of select="now/image"/>.gif</xsl:attribute>
				</xsl:when>
				<xsl:otherwise>
					<xsl:attribute name="src">skins/xhtml/xsl/meteo/na.gif</xsl:attribute>
				</xsl:otherwise>
			</xsl:choose>
		</img></span>
	<h2>Prévisions météo :</h2><br />
		<xsl:for-each select="jour">
			<xsl:if test="@date &lt;= '2'">
			<div class="meteo">
			<xsl:if test="@date = '0'"><h3>Aujourd'hui</h3></xsl:if>
			<xsl:if test="@date = '1'"><h3>Demain</h3></xsl:if>
			<xsl:if test="@date = '2'"><h3>Après demain</h3></xsl:if>
			<dl>
				<dt>La température : </dt>
				<dd>
					<xsl:value-of select="temperature_hi"/>°C pendant la journée et <xsl:value-of select="temperature_low"/>°C la nuit
				</dd>
				<dt>Etat du ciel le jour : </dt>
				<dd>
					<xsl:value-of select="cieljour"/>
					<span  class="meteo"><img alt="meteo" width="32" height="32">
						<xsl:choose>
							<xsl:when test="imagejour!='-'">
								<xsl:attribute name="src">skins/xhtml/xsl/meteo/<xsl:value-of select="imagejour"/>.gif</xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="src">skins/xhtml/xsl/meteo/na.gif</xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</img></span>
				</dd>
				<dt>Etat du ciel la nuit : </dt>
				<dd>
					<xsl:value-of select="cielnuit"/>
					<span  class="meteo"><img alt="meteo" width="32" height="32">
						<xsl:choose>
							<xsl:when test="imagenuit!='-'">
								<xsl:attribute name="src">skins/xhtml/xsl/meteo/<xsl:value-of select="imagenuit"/>.gif</xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="src">skins/xhtml/xsl/meteo/na.gif</xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</img></span>
				</dd>
			</dl>
			</div>
			</xsl:if>
		</xsl:for-each>
</xsl:template>

<xsl:template match="module[@id='meteo']">
	<div class="fkz_module_1"><div class="fkz_module_2">
	<div class="fkz_module_3"><div class="fkz_module_4">
	<div class="fkz_module_5"><div class="fkz_module_6">
	<div class="fkz_module" id='mod_meteo'>
		<div class="fkz_titre"><span id="meteo_logo"><xsl:text> </xsl:text></span><span id="meteo">La météo</span></div>
		<div class="fkz_module_corps">
			<span  class="meteo">
			<xsl:value-of select="meteo/now/temperature"/>°C<br />
			<a href="meteo.php">
			<img alt="meteo" width="64" height="64">
				<xsl:choose>
					<xsl:when test="meteo/now/image!='-'">
						<xsl:attribute name="src">skins/xhtml/xsl/meteo/<xsl:value-of select="meteo/now/image"/>.gif</xsl:attribute>
					</xsl:when>
					<xsl:otherwise>
						<xsl:attribute name="src">skins/xhtml/xsl/meteo/na.gif</xsl:attribute>
					</xsl:otherwise>
				</xsl:choose>
			</img>
			</a>
			</span>
		</div>
	</div>
	</div></div></div></div></div></div>
</xsl:template>

</xsl:stylesheet>
