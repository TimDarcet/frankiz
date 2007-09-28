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

<xsl:template match="page[@id='vocabulaire']">
	<dl class="boite">
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span><xsl:value-of select="h1"/></span>	
		</dt>
		<dd class="contenu">
			<xsl:apply-templates select="liste" mode="vocabulaire"/>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
	<br/>
</xsl:template>

<xsl:template match="liste" mode="vocabulaire">
	<dl class="definition top">
		<dt class="objet"><xsl:value-of select="entete[@id='mot']/@titre"/></dt>
		<dd class="definition"><xsl:value-of select="entete[@id='description']/@titre"/></dd>
	</dl>
		<xsl:apply-templates select="element" mode="vocabulaire"/>
</xsl:template>

<xsl:template match="element" mode="vocabulaire">
	<dl>
		<xsl:attribute name="class">definition<xsl:text> </xsl:text><xsl:if test="(position() mod 2)=0">pair</xsl:if><xsl:if test="(position() mod 2)=1">impair</xsl:if></xsl:attribute>	
		<dt class="objet"><xsl:apply-templates select="colonne[@id='mot']" mode="vocabulaire"/></dt>
		<dd class="definition"><xsl:apply-templates select="colonne[@id='explication']" mode="vocabulaire"/></dd>	
	</dl>
</xsl:template>

<xsl:template match="colonne[@id='mot' and @id='explication']" mode="vocabulaire">
	<xsl:apply-templates/>
</xsl:template>

</xsl:stylesheet>
