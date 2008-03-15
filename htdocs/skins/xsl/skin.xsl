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
<xsl:output method="xml" indent="yes" encoding="utf-8"
	    doctype-public="-//W3C//DTD XHTML 1.1//EN"
	    doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"/>
	
<xsl:param name="sommaire"/>
<xsl:param name="trier_annonces"/>
<xsl:param name="user_nom"/>
<xsl:param name="user_prenom"/>
<xsl:param name="date"/>
<xsl:param name="heure"/>

<!-- a modifier -->
<xsl:include href="html.xsl"/>
<xsl:include href="form.xsl"/>
<xsl:include href="arbre.xsl"/>
<xsl:include href="admin.xsl"/>

<xsl:include href="skins.xsl"/>
<xsl:include href="liens.xsl"/>
<xsl:include href="tours_kawa.xsl"/>
<xsl:include href="stats.xsl"/>
<xsl:include href="meteo.xsl"/>

<xsl:template match="/">
	<xsl:apply-templates select="frankiz/page[@id='annonces']" mode="sommaire"/>
	<xsl:apply-templates select="frankiz/page[@id='annonces']" mode="complet"/>
	<xsl:apply-templates select="frankiz/page[@id='trombino']"/>
	<xsl:apply-templates select="frankiz/page[@id!='annonces' and @id!='trombino']"/>
</xsl:template>


<xsl:template match="/frankiz/page">
	<div class="fkz_page_corps"><xsl:apply-templates/></div>
</xsl:template>

<xsl:template match="cadre">
	<xsl:if test="@titre!=''">
		<h2>
			<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:value-of select="@titre"/>
		</h2>
	</xsl:if>
	<xsl:apply-templates/>
</xsl:template>



<!-- Page des binets -->

<xsl:template match="page/binet">
	<xsl:if test="preceding-sibling::binet[1]/@categorie != @categorie or position() = 2">
		<h2><xsl:value-of select="@categorie"/></h2>
	</xsl:if>
	<h3>
		<xsl:choose>
			<xsl:when test="count(url)"><a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:value-of select="@nom"/>
			</a>
			</xsl:when>
			<xsl:otherwise><xsl:value-of select="@nom"/></xsl:otherwise>
		</xsl:choose>
	</h3>
		<xsl:apply-templates select="image"/>
		<p><xsl:value-of select="description"/></p>
</xsl:template>


<xsl:template match="module[@id='rss']">
	<div class="fkz_module_1"><div class="fkz_module_2">
	<div class="fkz_module_3"><div class="fkz_module_4">
	<div class="fkz_module_5"><div class="fkz_module_6">
	 <div class="fkz_module" id='mod_rss'>
		<div class="fkz_titre">
			<a>
				<xsl:attribute name="href"><xsl:value-of select="lien[position()=1]/@url"/></xsl:attribute>
				<xsl:value-of select="lien[position()=1]/@titre" />
			</a>
			<xsl:value-of select="lien[position()=1]"/>
		</div>
		<div class="fkz_module_corps">
			<dl class="fkz_rss">
				<xsl:for-each select="lien[position()>1]">
					<dt class="fkz_rss">
						<a>
							<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
							<xsl:value-of select="@titre" />
						</a>
					</dt>
					<dd>
						<xsl:value-of select="current()" />
					</dd>
				</xsl:for-each>
			</dl>
		</div>
	</div>
	</div></div></div></div></div></div>
</xsl:template>

<xsl:template match="module">
	<xsl:if test="false = starts-with(@visible,'false')">
		<div class="fkz_module_1"><div class="fkz_module_2">
		<div class="fkz_module_3"><div class="fkz_module_4">
		<div class="fkz_module_5"><div class="fkz_module_6">
		<div class="fkz_module"><xsl:attribute name="id">mod_<xsl:value-of select="@id"/></xsl:attribute>
			<div class="fkz_titre">
				<span><xsl:attribute name="id"><xsl:value-of select="@id"/>_logo</xsl:attribute><xsl:text> </xsl:text></span>
				<span><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute><xsl:value-of select="@titre"/></span>
			</div>
			<div class="fkz_module_corps">
				<xsl:apply-templates/>
			</div>
		</div>
		</div></div></div></div></div></div>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
