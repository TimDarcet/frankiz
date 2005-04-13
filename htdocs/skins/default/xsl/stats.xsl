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

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="module[@id='stats']">
	<xsl:if test="boolean(statistiques/serveur)">
		<dl id="stats" class="boite">
			<dt class="titre">
				<span class="droitehaut"><xsl:text> </xsl:text></span>
				<span>Etat des Serveurs</span>	
			</dt>
			<dd  class="contenu">
				<xsl:for-each select="statistiques/serveur">
					<ul class="serveurs">	
						<li class="nom">
							<xsl:value-of select="@nom"/>
						</li>
						<xsl:text> </xsl:text>
						<li>
							<xsl:attribute name="class">serveur_<xsl:value-of select="@etat"/></xsl:attribute>[<xsl:value-of select="@etat"/>]
						</li>
						<xsl:if test="boolean(@uptime)">: <xsl:value-of select="@uptime"/> jours</xsl:if>
					</ul>
				</xsl:for-each>
			</dd>
			<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
		</dl>
	</xsl:if>
	<dl id="service" class="boite">
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span>Statistiques :</span>	
		</dt>
		<dd class="contenu">
			<ul class="services contacts">
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
