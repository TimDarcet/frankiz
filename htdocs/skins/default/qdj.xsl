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
	
<xsl:template match="module[@id='qdj'] | module[@id='qdj_hier'] ">
	<dl class="boite">
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span><xsl:value-of select="@titre"/></span>	
		</dt>
		<dd class="contenu">
		<xsl:choose>
		<xsl:when test="boolean(qdj[@action])">
			<p class="news"><xsl:value-of select="qdj/question"/></p>
			<div class="fkz_qdj_rouje"><br/>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="qdj/@action"/>1</xsl:attribute>
					<xsl:value-of select="qdj/reponse[@id='1']"/>
				</a>
			</div>
			<div class="fkz_qdj_jone"><br/>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="qdj/@action"/>2</xsl:attribute>
					<xsl:value-of select="qdj/reponse[@id='2']"/>
				</a>
			</div>
			<br/><br/>
		</xsl:when>
		<xsl:otherwise>
			<p class="news"><xsl:value-of select="qdj/question"/></p>
			<div class="fkz_qdj_rouje_reponse">
				<xsl:value-of select="qdj/reponse[@id='1']"/>
				<br/>
				<xsl:value-of select="qdj/reponse[@id='1']/@votes"/> soit <xsl:value-of select="round((qdj/reponse[@id='1']/@votes * 100) div sum(qdj/reponse/@votes))"/>%<br/>
			</div>
			<div class="fkz_qdj_jone_reponse">
				<xsl:value-of select="qdj/reponse[@id='2']"/>
				<br/>
				<xsl:value-of select="qdj/reponse[@id='2']/@votes"/> soit <xsl:value-of select="round((qdj/reponse[@id='2']/@votes * 100) div sum(qdj/reponse/@votes))"/>%<br/>			
			</div>
		</xsl:otherwise>
		</xsl:choose>
		<br/>
		<br/>
			<xsl:if test="count(qdj/dernier)">
				<div>Derniers à répondre :</div>
				<ul class="none">
				<xsl:for-each select="qdj/dernier[position()&lt;=6]">
					<li class="fkz_qdj_last"><xsl:value-of select="@ordre"/>. <xsl:value-of select="eleve/@surnom"/></li>
				</xsl:for-each>
				</ul>
			</xsl:if>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
</xsl:template>

</xsl:stylesheet>