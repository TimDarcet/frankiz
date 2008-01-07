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

<xsl:template match="page[@id='binets']">
	<dl class="boite">
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span><xsl:value-of select="@titre"/></span>	
		</dt>
		<dd class="contenu">
		<br/>
			<xsl:apply-templates select="binet"/>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
</xsl:template>

<xsl:template match="binet">
	<xsl:if test="preceding-sibling::binet[1]/@categorie != @categorie or position() = 1">
		<br/>
        	<dl class="binets top">
			<dt class="objet"></dt>
			<dd class="categorie"><xsl:value-of select="@categorie"/></dd>	
		</dl>
	</xsl:if>
	<dl>
		<xsl:attribute name="class">binets<xsl:text> </xsl:text><xsl:if test="(position() mod 2)=0">pair</xsl:if><xsl:if test="(position() mod 2)=1">impair</xsl:if></xsl:attribute>
		<dt class="icon">
			<a>
				<xsl:attribute name="href"><xsl:apply-templates select="url"/></xsl:attribute>
				<xsl:apply-templates select="image"/>
			</a>
		</dt>
		<dd class="description">
			<a>
				<xsl:attribute name="href"><xsl:apply-templates select="url"/></xsl:attribute>
				<strong><xsl:value-of select="@nom"/></strong>
			</a><br/>
			<xsl:apply-templates select="description"/>
		</dd>	
	</dl>
</xsl:template>

</xsl:stylesheet>