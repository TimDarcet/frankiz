<?xml version="1.0" encoding="UTF-8" ?>
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
<!--
	Balises de formatage.
	
	$Id$
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<!-- Images -->
<xsl:template match="image">
	<p class="image">
		<img>
		<xsl:attribute name="src"><xsl:value-of select="@source"/></xsl:attribute>
		<xsl:attribute name="alt"><xsl:value-of select="@legende"/></xsl:attribute>
		<xsl:if test="boolean(@height)"><xsl:attribute name="height"><xsl:value-of select="@height"/></xsl:attribute></xsl:if>
		<xsl:if test="boolean(@width)"><xsl:attribute name="width"><xsl:value-of select="@width"/></xsl:attribute></xsl:if>
		</img>
	</p>
	<xsl:if test="boolean(@legende)">
		<span class="legende"><xsl:value-of select="@legende"/></span><br />
	</xsl:if>
</xsl:template>


<!-- Liens -->
<xsl:template match="lien">
	<a class="lien">
		<xsl:if test="boolean(@id)">
			<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
		</xsl:if>
		<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
		<xsl:attribute name="title"><xsl:value-of select="@titre"/></xsl:attribute>
		<span><xsl:value-of select="@titre"/><xsl:apply-templates/></span>
	</a>
</xsl:template>


<!-- Listes -->
<xsl:template match="liste">
	<xsl:apply-templates select="commentaire"/>
	<form method="post" accept-charset="UTF-8"><xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<xsl:variable name="nombre_colonnes" select="count(entete)+count(@selectionnable)"/>
		<table class="liste" cellspacing="0" cellpadding="0">
			<thead>
				<xsl:if test="boolean(@titre)">
					<tr class="titre">
						<td>
							<xsl:attribute name="colspan"><xsl:value-of select="$nombre_colonnes"/></xsl:attribute>
							<xsl:value-of select="@titre"/>
						</td>
					</tr>
				</xsl:if>
				<tr>
					<xsl:if test="@selectionnable='oui'">
						<td class="entete">&#160;</td>
					</xsl:if>
					<xsl:apply-templates select="entete"/>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td class="boutons">
						<xsl:attribute name="colspan"><xsl:value-of select="$nombre_colonnes"/></xsl:attribute>
						<xsl:apply-templates select="bouton"/>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<xsl:apply-templates select="element"/>
			</tbody>
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
		<xsl:attribute name="class"><xsl:if test="(position() mod 2)=0">pair</xsl:if><xsl:if test="(position() mod 2)=1">impair</xsl:if></xsl:attribute>
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
	<p class="texte"><xsl:apply-templates/></p>
</xsl:template>
<xsl:template match="ul">
	<ul><xsl:apply-templates/></ul>
</xsl:template>
<xsl:template match="li">
	<li><xsl:apply-templates/></li>
</xsl:template>
<xsl:template match="br">
	<br/>
</xsl:template>
<xsl:template match="h1">
	<h1><xsl:apply-templates/></h1>
</xsl:template>
<xsl:template match="h2">
	<h2><xsl:apply-templates/></h2>
</xsl:template>
<xsl:template match="h3">
	<h3><xsl:apply-templates/></h3>
</xsl:template>
<xsl:template match="h4">
	<h4><xsl:apply-templates/></h4>
</xsl:template>
<xsl:template match="h5">
	<h5><xsl:apply-templates/></h5>
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
<xsl:template match="a">
	<a><xsl:attribute name="href"><xsl:value-of select="@href"/></xsl:attribute><xsl:apply-templates/></a>
</xsl:template>
<xsl:template match="new_string">
	<span style="color: green"><xsl:apply-templates/></span>
</xsl:template>
<xsl:template match="old_string">
	<span style="color: red; text-decoration: line-through"><xsl:apply-templates/></span>
</xsl:template>
<xsl:template match="html"><!-- très moche car impossible à skinner, mais parfois indispensable -->
	<xsl:value-of disable-output-escaping="yes" select="text()"/>
</xsl:template>

</xsl:stylesheet>
