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

<xsl:template match="module[@id='activites']">
	<xsl:if test="count(element) !=0">
		<div style="text-align: center">
			<div class="fkz_module" id='mod_activites'>
				<div class="fkz_titre">Activit�s</div>
				<div class="fkz_module_corps">
					<xsl:for-each select="annonce">
						<xsl:if test="@titre = 'brc'">
							<xsl:if test="current()=0">
								<b><xsl:text>Ce soir, au BRC</xsl:text></b><br/>
							</xsl:if>
							<i><xsl:value-of select="titre"/>
							<xsl:text> </xsl:text>�<xsl:text> </xsl:text>
							<xsl:value-of select="heure"/></i><br/>
						</xsl:if>
						<xsl:apply-templates/>
						<br/>
						<br/>
					</xsl:for-each>
				</div>
			</div>
		</div>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
