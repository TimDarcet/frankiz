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

<xsl:template match="module[@id='stats']">
	<xsl:if test="boolean(statistiques/serveur)">
		<dl class="boite">
			<dt class="titre">
				<span class="droitehaut"><xsl:text> </xsl:text></span>
				<span>Etat des Serveurs</span>	
			</dt>
			<dd  class="contenu">
				<ul class="serveurs">
					<xsl:for-each select="statistiques/serveur">
						<li>
							<xsl:value-of select="@nom"/>
							<xsl:text> </xsl:text>
							<span>
								<xsl:attribute name="class">serveur_<xsl:value-of select="@etat"/></xsl:attribute>[<xsl:value-of select="@etat"/>]
							</span>
							<xsl:if test="boolean(@uptime)">: <xsl:value-of select="@uptime"/> jours</xsl:if>
						</li>
					</xsl:for-each>
				</ul>
			</dd>
			<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
		</dl>
	</xsl:if>
	<dl class="boite">
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span>Statistiques�:</span>	
		</dt>
		<dd class="contenu">
			<ul class="services">
				<xsl:for-each select="statistiques/service">
					<li>
						<a>
							<xsl:attribute name="href"><xsl:value-of select="@stat"/></xsl:attribute>
							<xsl:value-of select="@nom"/>
						</a>
					</li>
				</xsl:for-each>
			</ul>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
</xsl:template>


</xsl:stylesheet>