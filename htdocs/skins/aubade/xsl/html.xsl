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

<!-- Images -->
<xsl:template match="image">
	<img class="image">
		<xsl:attribute name="src"><xsl:value-of select="@source"/></xsl:attribute>
		<xsl:attribute name="alt"><xsl:value-of select="@legende"/></xsl:attribute>
	</img><br/>
	<xsl:if test="boolean(@legende)"><span class="legende"><xsl:value-of select="@legende"/></span><br/></xsl:if>
</xsl:template>


<!-- Liens -->
<xsl:template match="lien">
	<a class="lien">
		<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
		<xsl:if test="boolean(@key)">
			<xsl:attribute name="accesskey"><xsl:value-of select="@key"/></xsl:attribute>
			<xsl:attribute name="title">Accès rapide: <xsl:value-of select="@key"/></xsl:attribute>
		</xsl:if>
		<xsl:if test="boolean(@id)">
			<xsl:attribute name="class"><xsl:value-of select="@id"/></xsl:attribute>
		</xsl:if>
		<xsl:value-of select="@titre"/>
			<xsl:apply-templates/>
	</a>
</xsl:template>


<!-- Listes -->
<xsl:template match="liste">
	<xsl:if test="boolean(@titre)">
		<h2><xsl:value-of select="@titre"/></h2>
	</xsl:if>
	<xsl:apply-templates select="commentaire"/>
	<xsl:variable name="nombre_colonnes" select="count(entete)"/>
	<table class="liste" cellspacing="0" cellpadding="0">
		<tr>
			<xsl:apply-templates select="entete"/>
		</tr>
		<xsl:apply-templates select="element"/>
		<tr><td class="boutons"><xsl:attribute name="colspan"><xsl:value-of select="$nombre_colonnes"/></xsl:attribute>
			<xsl:apply-templates select="bouton"/>
		</td></tr>
	</table>
</xsl:template>


<xsl:template match="liste[(@selectionnable='oui') or (@action!='')]">
	<form method="post">
	<xsl:apply-templates select="commentaire | warning"/>
	<xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
	<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
	<xsl:if test="boolean(@titre)">
		<h2><span><xsl:value-of select="@titre"/></span></h2>
	</xsl:if>
		<xsl:variable name="nombre_colonnes" select="count(entete)+ number(contains('oui',@selectionnable))"/>
		<table class="liste" cellspacing="0" cellpadding="0">
			<tr>
				<xsl:if test="@selectionnable='oui'"><td class="entete">&#160;</td></xsl:if>
				<xsl:apply-templates select="entete"/>
			</tr>
			<xsl:apply-templates select="element"/>
			<tr><td class="boutons"><xsl:attribute name="colspan"><xsl:value-of select="$nombre_colonnes"/></xsl:attribute>
				<xsl:apply-templates select="bouton"/>
			</td></tr>
		</table>
	</form>
</xsl:template>

<xsl:template match="liste/entete">
	<td class="entete">
		<xsl:choose><xsl:when test="boolean(@action)">
			<a><xsl:attribute name="href"><xsl:value-of select="@action"/></xsl:attribute>
				<xsl:value-of select="@titre"/>
			</a>
		</xsl:when><xsl:otherwise>
			<xsl:value-of select="@titre"/>
		</xsl:otherwise></xsl:choose>
	</td>
</xsl:template>

<xsl:template match="liste/element">
	<tr>
		<xsl:if test="../@selectionnable='oui'">
			<td class="element">
				<xsl:choose>
					<xsl:when test="not(boolean(@selectionnable))">
						<input type="checkbox">
							<xsl:attribute name="name">elements[<xsl:value-of select="@id"/>]</xsl:attribute>
						</input>
					</xsl:when>
					<xsl:when test="@selectionnable='oui'">
						<input type="checkbox">
							<xsl:attribute name="name">elements[<xsl:value-of select="@id"/>]</xsl:attribute>
						</input>
					</xsl:when>

				</xsl:choose>
			</td>
		</xsl:if>
		<xsl:for-each select="colonne">
			<td class="element"><xsl:apply-templates/></td>
		</xsl:for-each>
	</tr>
</xsl:template>


<!-- Formatage HTML -->
<xsl:template match="p">
	<p><xsl:apply-templates/></p>
</xsl:template>
<xsl:template match="br">
	<br/>
</xsl:template>
<xsl:template match="ul">
	<ul><xsl:apply-templates/></ul>
</xsl:template>
<xsl:template match="li">
	<li><xsl:apply-templates/></li>
</xsl:template>
<xsl:template match="p[@id='selected']">
	<img src="skins/pico/fleche.gif" alt="[selectionné]"/>
</xsl:template>

<xsl:template match="h1">
	<h1>
		<xsl:if test="boolean(@id)"><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute></xsl:if>
		<span><xsl:apply-templates/></span>
	</h1>
</xsl:template>
<xsl:template match="h2">
	<h2>
		<xsl:if test="boolean(@id)"><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute></xsl:if>
		<span><xsl:apply-templates/></span>
	</h2>
</xsl:template>
<xsl:template match="h3">
	<h3>
		<xsl:if test="boolean(@id)"><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute></xsl:if>
		<span><xsl:apply-templates/></span>
	</h3>
</xsl:template>
<xsl:template match="h4">
	<h4>
		<xsl:if test="boolean(@id)"><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute></xsl:if>
		<span><xsl:apply-templates/></span>
	</h4>
</xsl:template>
<xsl:template match="h5">
	<h5>
		<xsl:if test="boolean(@id)"><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute></xsl:if>
		<span><xsl:apply-templates/></span>
	</h5>
</xsl:template>
<xsl:template match="strong">
	<strong><xsl:apply-templates/></strong>
</xsl:template>
<xsl:template match="em">
	<em><xsl:apply-templates/></em>
</xsl:template>
<xsl:template match="code">
	<code><xsl:apply-templates/></code>
</xsl:template>
<xsl:template match="new_string">
	<span style="color: green"><xsl:apply-templates/></span>
</xsl:template>
<xsl:template match="old_string">
	<span style="color: red; text-decoration: line-through"><xsl:apply-templates/></span>
</xsl:template>
<xsl:template match="a">
	<a>
		<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		<xsl:attribute name="href"><xsl:value-of select="@href"/></xsl:attribute><xsl:apply-templates/>
	</a>
</xsl:template>
<xsl:template match="html"><!-- très moche car impossible à skinner, mais parfois indispensable -->
	<xsl:value-of disable-output-escaping="yes" select="text()"/>
</xsl:template>

</xsl:stylesheet>
