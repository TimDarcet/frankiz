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

<xsl:template match="page[@id='trombino']">
	<div class="fkz_trombino">
		<xsl:for-each select="eleve">
			<p>
				<xsl:value-of select="@prenom" />
				<xsl:text> </xsl:text>
				<xsl:value-of select="translate(@nom,'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')" />
			</p>
			<p>
				<xsl:value-of select="@surnom" />
			</p>
			<p>
				<xsl:value-of select="@promo" />
			</p>
			<p>Tel:
				<xsl:value-of select="@phone"/>  Casert:
			<xsl:value-of select="@casert"/>
			</p>
			<p>Section: <xsl:value-of select="@section"/>
			</p>
			<p>Binets:
				<xsl:for-each select="liste[@id='binets']/element">
					<xsl:value-of select="current()"/>, 
				</xsl:for-each>
			</p>
			<p>
				<a>
					<xsl:attribute name="href"><xsl:text>trombino/index.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
					<img height="95" width="80">
					<xsl:attribute name="src"><xsl:text>trombino/index.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
					<xsl:attribute name="alt"><xsl:value-of select="@login"/> (<xsl:value-of select="@promo"/>)</xsl:attribute>
					</img>
				</a>
			</p>
			<hr/>
		</xsl:for-each>
		<xsl:apply-templates/>
	</div>
</xsl:template>

</xsl:stylesheet>
