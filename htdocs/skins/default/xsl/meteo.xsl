<?xml version="1.0" encoding="ISO-8859-1" ?>
<!-- $Id$ -->
<!--
<!-- 	Copyright (C) 2004 Binet Réseau -->
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

<xsl:template match="module[@id='meteo']">
	<dl id="meteo" class="boite">
          <dt class="titre">
            <span class="droitehaut"><xsl:text> </xsl:text></span><span><xsl:value-of select="@titre"/></span>
          </dt>
		<dd class="contenu">
				<strong><xsl:value-of select="meteo/now/temperature"/>°C</strong><br />
				<a href="meteo.php">
					<img alt="meteo" width="64" height="64">
						<xsl:attribute name="src">skins/default/images/meteo/<xsl:value-of select="meteo/now/image"/>.gif</xsl:attribute>
					</img>
				</a>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
</xsl:template>

<xsl:template match="page/meteo">
	<dl id="page_meteo" class="boite">
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span>Météo sur le Platâl aujourd'hui :</span>	
		</dt>
		<dd class="contenu">
			<dl class="meteo pair">
				<dt class="quand top">Aujourd'hui</dt>
				<dd class="prevision">
					Le soleil est présent de <xsl:value-of select="now/sunrise"/> à <xsl:value-of select="now/sunset"/><br />
					La température actuelle est de <xsl:value-of select="now/temperature"/>°C<br />
					La pression est de <xsl:value-of select="now/pression"/> millibar<br />
					Pour ce qui est du vent <xsl:value-of select="now/vent"/><br />
					Et l'humidité s'élève à <xsl:value-of select="now/humidite"/>%<br />
					L'état du ciel : <xsl:value-of select="now/ciel"/><br />
					<img alt="meteo" width="64" height="64">
					<xsl:attribute name="src">skins/default/xsl/meteo/<xsl:value-of select="now/image"/>.gif</xsl:attribute>
						</img>
				</dd>
			</dl>
			<br/>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
	<div class="hr"><hr/></div>
	<dl class="boite">
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span>Prévisions météo :</span>	
		</dt>
		<dd class="contenu">
			<xsl:for-each select="jour[@date=0 or @date=1 or @date=2]">
				<dl>
					<xsl:attribute name="class">meteo<xsl:text> </xsl:text><xsl:if test="(position() mod 2)=0">pair</xsl:if><xsl:if test="(position() mod 2)=1">impair</xsl:if></xsl:attribute>
					<dt class="quand top">
						<xsl:choose>
							<xsl:when test="@date=0">
								Ce soir
							</xsl:when>
							<xsl:when test="@date=1">
								Demain
							</xsl:when>
							<xsl:when test="@date=2">
								Après-Demain
							</xsl:when>
						</xsl:choose>
					</dt>
					<dd class="prevision">
						<span>La température : </span><xsl:if test="@date!=0"><xsl:value-of select="temperature_hi"/>°C pendant la journée et</xsl:if> <xsl:value-of select="temperature_low"/>°C la nuit<br />	
						<span>Etat du ciel le jour : </span><xsl:value-of select="cieljour"/>
						<img alt="meteo" width="32" height="32">
							<xsl:attribute name="src">skins/default/xsl/meteo/<xsl:value-of select="imagejour"/>.gif</xsl:attribute>
						</img><br />
						<span>Etat du ciel la nuit : </span><xsl:value-of select="cielnuit"/>
						<img alt="meteo" width="32" height="32">
							<xsl:attribute name="src">skins/default/xsl/meteo/<xsl:value-of select="imagenuit"/>.gif</xsl:attribute>
						</img><br />
					</dd>
				</dl>
				<br/>
			</xsl:for-each>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
</xsl:template>



</xsl:stylesheet>
