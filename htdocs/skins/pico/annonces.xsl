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

<xsl:template match="page[@id='accueil']" mode="complet">
	<xsl:choose>
		<xsl:when test="count(annonce)">
			<xsl:if test="$trier_annonces='pas_tri'">
				<xsl:apply-templates select="annonce" mode="complet"/>
			</xsl:if>
			<xsl:if test="$trier_annonces='trie'"> 
				<xsl:apply-templates select="annonce[@categorie='important']" mode="complet"/>
				<xsl:apply-templates select="annonce[@categorie='nouveau']" mode="complet"/>
				<xsl:apply-templates select="annonce[@categorie='vieux']" mode="complet"/>
				<xsl:apply-templates select="annonce[@categorie='reste']" mode="complet"/>  
			</xsl:if>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>


<xsl:template match="page[@id='accueil']" mode="sommaire">
<xsl:if test="$sommaire='pas_tri'">
	<xsl:if test="count(annonce) != 0">
		<div class="fkz_annonces">
			<xsl:apply-templates select="annonce" mode="sommaire"/>
		</div>
		<br/>
	</xsl:if>
</xsl:if>
<xsl:if test="$sommaire='trie'">
	<xsl:if test="count(annonce) != 0">
		<div class="fkz_annonces">
		<xsl:if test="count(annonce[@categorie='important']) != 0">
			<div class="fkz_sommaire_titre">
			       <span class="fkz_annonces_important"><xsl:text> </xsl:text></span> Important
			</div>
			<xsl:apply-templates select="annonce[@categorie='important']" mode="sommaire"/>
		</xsl:if>
		<xsl:if test="count(annonce[@categorie='nouveau']) != 0">
			<div class="fkz_sommaire_titre"><span class="fkz_annonces_nouveau"><xsl:text> </xsl:text></span> Nouvelles Fraîches</div>
			<xsl:apply-templates select="annonce[@categorie='nouveau']" mode="sommaire"/>
		</xsl:if>
		<xsl:if test="count(annonce[@categorie='vieux']) != 0">
			<div class="fkz_sommaire_titre"><span class="fkz_annonces_vieux"><xsl:text> </xsl:text></span> Demain c'est fini</div>
			<xsl:apply-templates select="annonce[@categorie='vieux']" mode="sommaire"/>
		</xsl:if>
		<xsl:if test="count(annonce[@categorie='reste']) != 0">
			<div class="fkz_sommaire_titre"><span class="fkz_annonces_reste"><xsl:text> </xsl:text></span> En attendant...</div>
			<xsl:apply-templates select="annonce[@categorie='reste']" mode="sommaire"/>
		</xsl:if>
		</div>
		<br/>
	</xsl:if>
	
</xsl:if>
</xsl:template>

<xsl:template match="annonce" mode="complet">
<div class="fkz_annonces">
       <a> <xsl:attribute name="name">
       <xsl:value-of select="@id"/>
       </xsl:attribute><xsl:text> </xsl:text>
       </a>
       <div class="fkz_annonces_titre"><b>
       <span>
          <xsl:attribute name="class">fkz_annonces_<xsl:value-of select="@categorie"/>
	</xsl:attribute><xsl:text> </xsl:text></span>
	<span class="fkz_annonces_cat">(<xsl:value-of select="@categorie"/>)</span>
	
      <xsl:text> </xsl:text>
	<span class="fkz_annonces_titre"><xsl:value-of select="@titre"/></span>
      </b></div>
      <br/>
      <xsl:apply-templates select="html"/>
      <p class="fkz_signature">
      <xsl:choose>
      <xsl:when test="eleve/@surnom != ''">
		<xsl:value-of select="eleve/@surnom"/>
	</xsl:when>
      <xsl:otherwise>
      <xsl:value-of select="eleve/@prenom"/><xsl:text>  </xsl:text><xsl:value-of select="eleve/@nom"/>
      </xsl:otherwise>
      </xsl:choose>
      </p>
</div>
<br/>
</xsl:template>

<xsl:template match="annonce" mode="sommaire">
<div class="fkz_sommaire">
       <a> <xsl:attribute name="href">
       <xsl:text>index.php#</xsl:text> 
       <xsl:value-of select="@id"/>
       </xsl:attribute>
       	<xsl:value-of select="@titre"/>
	</a>
</div>
</xsl:template>

</xsl:stylesheet>
