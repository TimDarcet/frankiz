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

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
			      xmlns="http://www.w3.org/1999/xhtml">

<xsl:template match="module[@id='anniversaires']">
	<xsl:if test="count(eleve) != 0">
		<div class="fkz_module_1"><div class="fkz_module_2">
		<div class="fkz_module_3"><div class="fkz_module_4">
		<div class="fkz_module_5"><div class="fkz_module_6">
		<div class="fkz_module" id="mod_anniversaires">
		<div class="fkz_anniversaire_titre"><span id="anniversaires_logo"><xsl:text> </xsl:text></span><span id="anniversaires">Joyeux anniversaire: </span></div>
		<div class="fkz_anniversaire">
			<xsl:for-each select="eleve">
				<xsl:choose>
					<xsl:when test="preceding-sibling::eleve[1]/@promo != @promo or position() = 1">
						<xsl:if test="position()!=1"><br/></xsl:if>
						<a>
							<xsl:attribute name="href">trombino.php?anniversaire&amp;promo=<xsl:value-of select="@promo" /></xsl:attribute>
							<xsl:value-of select="@promo" /> 
						</a>: 
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>, </xsl:text>
					</xsl:otherwise>
				</xsl:choose>
 				<xsl:value-of select="@prenom" />
 				<xsl:text> </xsl:text>
 				<xsl:value-of select="translate(@nom,'abcdefghijklmnopqrstuvwxyzéèàçêëù','ABCDEFGHIJKLMNOPQRSTUVWXYZÉÈÀÇÊËÙ')" />
			</xsl:for-each>
		</div>
		<br/>
		</div>
		</div></div></div></div></div></div>
	</xsl:if>
</xsl:template>

<xsl:template match="module[@id='fetes']">
	<xsl:if test="count(eleve) != 0">
		<div class="fkz_module_1"><div class="fkz_module_2">
		<div class="fkz_module_3"><div class="fkz_module_4">
		<div class="fkz_module_5"><div class="fkz_module_6">
		<div class="fkz_module" id="mod_fetes">
		<div class="fkz_titre"><span id="fetes_logo"><xsl:text> </xsl:text></span><span id="fetes">Bonne fête: </span></div>
		<div class="fkz_module_corps">
			<xsl:for-each select="eleve">
 				<xsl:value-of select="@prenom" />
 				<xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>
		</div>
		<br/> 
		</div>
		</div></div></div></div></div></div>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
