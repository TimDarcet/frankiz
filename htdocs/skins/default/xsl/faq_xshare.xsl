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
	s
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="page[@id='xshare' or @id='faq']">
	<dl class="boite">
		<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span><xsl:value-of select="h1"/></span>
		</dt>
		<dd class="contenu">
		<div id="recherchearbre">
			<dl class="boite">
				<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
				<dt class="titre">
					<span class="droitehaut"><xsl:text> </xsl:text></span>
				</dt>
				<dd class="contenu">
					<xsl:apply-templates select="formulaire"/>
				</dd>
				<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
			</dl>
		</div>
			<xsl:apply-templates select="p"/>
			<div class="arbre">
				<xsl:apply-templates select="arbre"/>
			</div>
			
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
</xsl:template>

</xsl:stylesheet>
