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

<xsl:template match="module[@id='tours_kawa']">
  <xsl:if test="count(element) !=0">
  <div class="fkz_titre">Tour Kawa</div>
  <xsl:for-each select="element">
    <xsl:if test="@nom = '1'">
      <xsl:text>Aujourd'hui: </xsl:text>
      <xsl:value-of select="current()"/>
      <br/>
    </xsl:if>
    <xsl:if test="@nom = '2'">
      <xsl:text>Demain: </xsl:text>
      <xsl:value-of select="current()"/>    
      <br/>
    </xsl:if>  
  </xsl:for-each>
  </xsl:if>
</xsl:template>

</xsl:stylesheet>
