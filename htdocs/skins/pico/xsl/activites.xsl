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
		<div class="fkz_module" id='mod_activites'>
			<div class="fkz_titre"><span id="affiches_logo"><xsl:text> </xsl:text></span><span id="affiches">Activités</span></div>
			<div class="fkz_module_corps">
				<xsl:apply-templates select="annonce" mode="activites"/>
			</div>
		</div>
	</xsl:if>
</xsl:template>

<xsl:template match="annonce" mode="activites">
		<div style="text-align:center">
			<b>
				<span><xsl:value-of select="@titre"/></span>
			</b>
		</div>
		<div style="text-align:center">
			<xsl:if test="@date!=''">A <xsl:value-of select='substring(@date,12,5)'/><br/></xsl:if>
			<xsl:apply-templates select="*[name()!='eleve']"/>
			<xsl:if test="count(eleve)">
				<p class="fkz_signature">
					<xsl:choose>
						<xsl:when test="eleve/@surnom != ''">
							<xsl:value-of select="eleve/@surnom"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="eleve/@prenom"/><xsl:text>  </xsl:text><xsl:value-of select="eleve/@nom"/>
						</xsl:otherwise>
					</xsl:choose>
					(<xsl:value-of select="eleve/@promo"/>)
				</p>
			</xsl:if>
			<xsl:text> </xsl:text> <!-- Pas de div vide -->
		</div>
	<br/>
</xsl:template>

</xsl:stylesheet>
