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

<xsl:template match="eleve">
	<div class="fkz_trombino_eleve">
	<p class="nom">
		<xsl:value-of select="@prenom" />
		<xsl:text> </xsl:text>
		<xsl:value-of select="translate(@nom,'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')" />
	</p>
	<p class="surnom">
		<xsl:value-of select="@surnom" />
	</p>
	<p class="promo">
		<xsl:value-of select="@promo" />
	</p>
	<p class="telephone">Tel: <xsl:value-of select="@tel"/>
	</p>
	<p class="casert">Casert: <xsl:value-of select="@casert"/>
	</p>
	<p class="section">Section: <xsl:value-of select="@section"/>
	</p>
    <xsl:if test="count(binet) != 0">
	<div class="binets">Binets:
		<ul>
		<xsl:for-each select="binet">
			<li><xsl:value-of select="@nom"/><xsl:text>  </xsl:text><em>(<xsl:value-of select="text()"/>)</em></li>
		</xsl:for-each>
		</ul>
	</div>
    </xsl:if>
	<p>
		<a>
			<xsl:attribute name="href"><xsl:text>trombino.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
			<img height="95" width="80">
			<xsl:attribute name="src"><xsl:text>trombino.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
			<xsl:attribute name="alt"><xsl:value-of select="@login"/> (<xsl:value-of select="@promo"/>)</xsl:attribute>
			</img>
		</a>
	</p>
	<xsl:apply-templates select="*[name()!='binet']"/>
	</div>
</xsl:template>

<xsl:template match="page[@id='trombino']">
	<div class="fkz_trombino">
		<xsl:apply-templates/>
	</div>
</xsl:template>

</xsl:stylesheet>
