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

<xsl:template match="module[@id='anniversaires']">
	<xsl:if test="count(eleve) != 0">
        <div class="fkz_module" id="mod_anniversaires">
		<div class="fkz_anniversaire_titre"><span id="anniversaires_logo"><xsl:text> </xsl:text></span><span id="anniversaires">Joyeux anniversaire: </span></div>
		<div class="fkz_anniversaire">
			<xsl:for-each select="eleve">
				<xsl:if test="preceding-sibling::eleve[1]/@promo != @promo or position() = 1">
					<xsl:if test="position()!=1"><br/></xsl:if>
					<a>
						<xsl:attribute name="href">trombino.php?anniversaire&amp;promo=<xsl:value-of select="@promo" /></xsl:attribute>
						<xsl:value-of select="@promo" /> 
					</a>: 
				</xsl:if>
 				<xsl:value-of select="@prenom" />
 				<xsl:text> </xsl:text>
 				<xsl:value-of select="translate(@nom,'abcdefghijklmnopqrstuvwxyzéèàçêëù','ABCDEFGHIJKLMNOPQRSTUVWXYZÉÈÀÇÊËÙ')" />
				<xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
			</xsl:for-each>
		</div>
		<br/> 
		</div>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
