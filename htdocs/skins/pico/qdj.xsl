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
		<div class="fkz_titre"><xsl:value-of select="@titre"/></div>
		<div class="fkz_module">
		<xsl:choose>
		<xsl:when test="boolean(qdj[@action])">
			<div class="qdj_question"><xsl:value-of select="qdj/question"/></div><br/>
			<div class="fkz_qdj_rouje">
			<a>
			<xsl:attribute name="href"><xsl:value-of select="qdj/@action"/>1</xsl:attribute>
			<img src="skins/pico/pointrouje.gif" alt="rouje"/><br/>
			<xsl:value-of select="qdj/reponse[@id='1']"/>
			</a>
			</div>
			<div class="fkz_qdj_jone">
			<a>
			<xsl:attribute name="href"><xsl:value-of select="qdj/@action"/>2</xsl:attribute>
			<img src="skins/pico/pointjone.gif" alt="jône"/><br/>
			<xsl:value-of select="qdj/reponse[@id='2']"/>
			</a>
			</div>
		</xsl:when>
		<xsl:otherwise>
			<div class="fkz_qdj_question"><xsl:value-of select="qdj/question"/></div>
			<div class="fkz_page">
			<div class="fkz_qdj_rouje">
			<xsl:value-of select="qdj/reponse[@id='1']"/>
			<br/>
			<xsl:value-of select="qdj/reponse[@id='1']/@votes"/> soit <xsl:value-of select="round((qdj/reponse[@id='1']/@votes * 100) div sum(qdj/reponse/@votes))"/>%<br/>
			
			</div>
			<div class="fkz_qdj_jone">
			<xsl:value-of select="qdj/reponse[@id='2']"/>
			<br/>
			<xsl:value-of select="qdj/reponse[@id='2']/@votes"/> soit <xsl:value-of select="round((qdj/reponse[@id='2']/@votes * 100) div sum(qdj/reponse/@votes))"/>%<br/>			
			</div>
			</div>
		</xsl:otherwise>
		</xsl:choose>
		<br/>
			<div style="text-align: center"><br />
			<xsl:if test="count(qdj/dernier)">
				<div>Derniers à répondre :</div>
				<ul class="fkz_qdj_last">
				<xsl:for-each select="qdj/dernier[position()&lt;=6]">
					<li class="fkz_qdj_last"><xsl:value-of select="@ordre"/>. <xsl:value-of select="eleve/@surnom"/></li>
				</xsl:for-each>
				</ul>
			</xsl:if>
			</div>
		</div>
</xsl:template>

</xsl:stylesheet>