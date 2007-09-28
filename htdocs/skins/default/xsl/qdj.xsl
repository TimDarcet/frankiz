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

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
			      xmlns="http://www.w3.org/1999/xhtml">
	
<xsl:template match="module[@id='qdj'] | module[@id='qdj_hier'] ">
	<dl class="boite">
		<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span><xsl:value-of select="@titre"/></span>	
		</dt>
		<dd class="contenu">
			<p class="news"><xsl:value-of select="qdj/question"/></p>
		<xsl:choose>
		<xsl:when test="boolean(qdj[@action])">
<!--			<ul class="qdj">
				<li>
					<span class="rouje"><xsl:text> </xsl:text></span><br/>
					<a>		
						<xsl:attribute name="href"><xsl:value-of select="qdj/@action"/>1</xsl:attribute>
						<xsl:value-of select="qdj/reponse[@id='1']"/>
					</a>
				</li>
				<li>
					<span class="jone"><xsl:text> </xsl:text></span><br/>
					<a>
						<xsl:attribute name="href"><xsl:value-of select="qdj/@action"/>2</xsl:attribute>
						<xsl:value-of select="qdj/reponse[@id='2']"/>
					</a>
				</li>
			</ul>-->
			<table class="qdj">
				<tr>
					<td>
					<span class="rouje"><xsl:text> </xsl:text></span><br/>
					<a>		
						<xsl:attribute name="href"><xsl:value-of select="qdj/@action"/>1</xsl:attribute>
						<xsl:value-of select="qdj/reponse[@id='1']"/>
					</a>
					</td>

					<td>
					<span class="jone"><xsl:text> </xsl:text></span><br/>
					<a>
						<xsl:attribute name="href"><xsl:value-of select="qdj/@action"/>2</xsl:attribute>
						<xsl:value-of select="qdj/reponse[@id='2']"/>
					</a>
					</td>
				</tr>
			</table>
		</xsl:when>
		<xsl:otherwise>
<!--			<ul class="reponse_qdj">
				<li>
					<div class="col">
						<span class="blanc"><xsl:attribute name="style">height:<xsl:value-of select="round(100-((qdj/reponse[@id='1']/@votes * 100) div sum(qdj/reponse/@votes)))" />%;</xsl:attribute><xsl:text> </xsl:text></span>
						<span class="rouje"><xsl:attribute name="style">height:<xsl:value-of select="round((qdj/reponse[@id='1']/@votes * 100) div sum(qdj/reponse/@votes))" />%;</xsl:attribute><xsl:text> </xsl:text></span><br/>
					</div>
					<div class="reponse">
						<strong><xsl:value-of select="qdj/reponse[@id='1']" /></strong>
						<br/>
						<xsl:value-of select="qdj/reponse[@id='1']/@votes"/> soit <xsl:value-of select="round((qdj/reponse[@id='1']/@votes * 100) div sum(qdj/reponse/@votes))"/>%<br/>
					</div>
				</li>
				<li>
					<div class="col">
						<span class="blanc"><xsl:attribute name="style">height:<xsl:value-of select="round(100-((qdj/reponse[@id='2']/@votes * 100) div sum(qdj/reponse/@votes)))" />%;</xsl:attribute><xsl:text> </xsl:text></span>
						<span class="jone"><xsl:attribute name="style">height:<xsl:value-of select="round((qdj/reponse[@id='2']/@votes * 100) div sum(qdj/reponse/@votes))" />%;</xsl:attribute><xsl:text> </xsl:text></span><br/>
					</div>
					<div class="reponse">
						<strong><xsl:value-of select="qdj/reponse[@id='2']" /></strong>
						<br/>
						<xsl:value-of select="qdj/reponse[@id='2']/@votes"/> soit <xsl:value-of select="round((qdj/reponse[@id='2']/@votes * 100) div sum(qdj/reponse/@votes))"/>%<br/>
					</div>
				</li>
			</ul>-->
			<table class="reponse_qdj">
				<tr>
					<td>
					<div class="col">
						<span class="blanc"><xsl:attribute name="style">height:<xsl:value-of select="round(100-((qdj/reponse[@id='1']/@votes * 100) div sum(qdj/reponse/@votes)))" />%;</xsl:attribute><xsl:text> </xsl:text></span>
						<span class="rouje"><xsl:attribute name="style">height:<xsl:value-of select="round((qdj/reponse[@id='1']/@votes * 100) div sum(qdj/reponse/@votes))" />%;</xsl:attribute><xsl:text> </xsl:text></span><br/>
					</div>
					<div class="reponse">
						<strong><xsl:value-of select="qdj/reponse[@id='1']" /></strong>
						<br/>
						<xsl:value-of select="qdj/reponse[@id='1']/@votes"/> soit <xsl:value-of select="round((qdj/reponse[@id='1']/@votes * 100) div sum(qdj/reponse/@votes))"/>%<br/>
					</div>
					</td>
	
					<td>
					<div class="col">
						<span class="blanc"><xsl:attribute name="style">height:<xsl:value-of select="round(100-((qdj/reponse[@id='2']/@votes * 100) div sum(qdj/reponse/@votes)))" />%;</xsl:attribute><xsl:text> </xsl:text></span>
						<span class="jone"><xsl:attribute name="style">height:<xsl:value-of select="round((qdj/reponse[@id='2']/@votes * 100) div sum(qdj/reponse/@votes))" />%;</xsl:attribute><xsl:text> </xsl:text></span><br/>
					</div>
					<div class="reponse">
						<strong><xsl:value-of select="qdj/reponse[@id='2']" /></strong>
						<br/>
						<xsl:value-of select="qdj/reponse[@id='2']/@votes"/> soit <xsl:value-of select="round((qdj/reponse[@id='2']/@votes * 100) div sum(qdj/reponse/@votes))"/>%<br/>
					</div>
					</td>
				</tr>
			</table>
		</xsl:otherwise>
		</xsl:choose>
		<br/>
		<xsl:if test="count(qdj/dernier)">
			<div>Derniers à répondre :</div>
			<ul class="liste">
			<xsl:for-each select="qdj/dernier[position()&lt;=5]">
				<li class="fkz_qdj_last"><xsl:value-of select="@ordre"/>. <xsl:apply-templates select="eleve" mode="signature"/></li>
			</xsl:for-each>
			</ul>
			
		</xsl:if>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
</xsl:template>

</xsl:stylesheet>
