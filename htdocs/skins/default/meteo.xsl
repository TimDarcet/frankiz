<?xml version="1.0" encoding="ISO-8859-1" ?>
<!-- $Id$ -->
<!--
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
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<!-- Meteo de l'X -->

<xsl:template match="module/meteo">
	<dl class="cadrecote">
		<dt class="top"><xsl:text> </xsl:text></dt>
		<dd class="milieu">
			<p class="titre">M�t�o</p>
			<p class="image">
				<strong><xsl:value-of select="now/temperature"/>�C</strong><br />
				<img alt="meteo" width="64" height="64">
					<xsl:attribute name="src">skins/default/images/meteo/<xsl:value-of select="now/image"/>.gif</xsl:attribute>
				</img>
			</p>
		</dd>
		<dd class="bas"><xsl:text> </xsl:text></dd>
	</dl>
</xsl:template>

<xsl:template match="page/meteo">
	<dl class="boite">
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span>M�t�o sur le Plat�l aujourd'hui�:</span>	
		</dt>
		<dd class="contenu">
			<dl class="meteo pair">
				<dt class="quand">Aujourd'hui</dt>
				<dd class="prevision">
					Le soleil est pr�sent de <xsl:value-of select="now/sunrise"/> � <xsl:value-of select="now/sunset"/><br />
					La temp�rature actuelle est de <xsl:value-of select="now/temperature"/>�C<br />
					La pression est de <xsl:value-of select="now/pression"/> millibar<br />
					Pour ce qui est du vent <xsl:value-of select="now/vent"/><br />
					Et l'humidit� s'�l�ve � <xsl:value-of select="now/humidite"/>%<br />
					L'�tat du ciel : <xsl:value-of select="now/ciel"/><br />
					<img alt="meteo" width="64" height="64">
					<xsl:attribute name="src">skins/default/images/meteo/<xsl:value-of select="now/image"/>.gif</xsl:attribute>
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
			<span>Pr�visions m�t�o :</span>	
		</dt>
		<dd class="contenu">
			<xsl:for-each select="jour">
				<dl>
					<xsl:attribute name="class">meteo<xsl:text> </xsl:text><xsl:if test="(position() mod 2)=0">pair</xsl:if><xsl:if test="(position() mod 2)=1">impair</xsl:if></xsl:attribute>
					<dt class="quand"> Pr�vision � <xsl:value-of select="@date"/> jours  </dt>
					<dd class="prevision">
						<span>La temp�rature : </span><xsl:value-of select="temperature_hi"/>�C pendant la journ�e et <xsl:value-of select="temperature_low"/>�C la nuit<br />	
						<span>Etat du ciel le jour : </span><xsl:value-of select="cieljour"/>
						<img alt="meteo" width="32" height="32">
							<xsl:attribute name="src">skins/default/images/meteo/<xsl:value-of select="imagejour"/>.gif</xsl:attribute>
						</img><br />
						<span>Etat du ciel la nuit : </span><xsl:value-of select="cielnuit"/>
						<img alt="meteo" width="32" height="32">
							<xsl:attribute name="src">skins/default/images/meteo/<xsl:value-of select="imagenuit"/>.gif</xsl:attribute>
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