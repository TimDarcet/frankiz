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

<xsl:template match="module[@id='activites']">
    <xsl:if test="count(annonce) !=0">
	<dl id="activités" class="cadrecote">
		<dt class="top"><xsl:text> </xsl:text></dt>
		<dd class="milieu">
			<p class="titre">Activités</p>
				<xsl:for-each select="annonce">
					<div class="activité center">
						<xsl:if test="@date!=''">A <xsl:value-of select='substring(@date,12,5)'/><br/></xsl:if>
						<xsl:apply-templates select="*[name()!='eleve']"/>
						<xsl:if test="count(eleve)">
							<p class="fkz_signature">
								<xsl:apply-templates select="eleve" mode="signature"/>
							</p>
						</xsl:if>
						<xsl:text> </xsl:text> <!-- Pas de div vide -->
					</div>
				</xsl:for-each>
		</dd>
		<dd class="bas"><xsl:text> </xsl:text></dd>
	</dl>
  </xsl:if>
</xsl:template>

</xsl:stylesheet>
