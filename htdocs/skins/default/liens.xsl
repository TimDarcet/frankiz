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

<xsl:template match="module[@id='liens_contacts']">
	<ul id="contacts">
		<xsl:apply-templates select="lien" mode="liste"/>
	</ul>
</xsl:template>

<xsl:template match="module[@id='liens_ecole']">
	<ul id="ecole">
		<xsl:apply-templates select="lien" mode="liste"/>
	</ul>
</xsl:template>

<xsl:template match="module[@id='liens_navigation']">
	<ul id="menu">
		<li id="top"></li>
		<xsl:apply-templates select="lien" mode="liste"/>
		<li id="bottom"></li>
	</ul>
</xsl:template>

<xsl:template match="lien" mode="liste">
	<li>
		<xsl:if test="boolean(@id)">
			<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
		</xsl:if>
		<a><xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
			<span><xsl:value-of select="@titre" /></span>
		</a>
	</li>
</xsl:template>

</xsl:stylesheet>
