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



<xsl:template match="module[@id='liens_contacts']">
 <div class="fkz_titre"><span id="contacts_logo"><xsl:text> </xsl:text></span><span id="contacts">Contacts</span></div>
  <div class="fkz_module">
    <xsl:for-each select="lien">
      <div class="fkz_lien">
      <a>
        <xsl:attribute name="href">
	  <xsl:value-of select="@url"/>
	</xsl:attribute>
	<xsl:value-of select="@titre"/>
      </a>
      </div>
    </xsl:for-each>
  <xsl:text> </xsl:text>
  </div>
</xsl:template>

<xsl:template match="module[@id='liens_ecole']">
   <div class="fkz_titre"><span id="liens_ecole_logo"><xsl:text> </xsl:text></span><span id="liens_ecole">Liens Ecole</span></div>
   <div class="fkz_module">
    <xsl:for-each select="lien">
      <div class="fkz_lien">
      <a>
        <xsl:attribute name="href">
	  <xsl:value-of select="@url"/>
	</xsl:attribute>
	<xsl:value-of select="@titre"/>
      </a>
      </div>
    </xsl:for-each>
  <xsl:text> </xsl:text>
  </div>
</xsl:template>

<xsl:template match="module[@id='liens_navigation']">
    <div class="fkz_liens_nav">
    <xsl:for-each select="lien">
      <span>
      <a>
        <xsl:attribute name="href">
          <xsl:value-of select="@url"/>
	</xsl:attribute>
	<xsl:value-of select="@titre" />
      </a>
      </span>
      <xsl:if test="position() != last()">
        <xsl:text> | </xsl:text>
      </xsl:if>
    </xsl:for-each>
    </div>
</xsl:template>




</xsl:stylesheet>
