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
  <xsl:if test="last() != 0">
	<xsl:apply-templates select="annonce[@categorie='important']" mode="complet"/>
	<xsl:apply-templates select="annonce[@categorie='nouveau']" mode="complet"/>
	<xsl:apply-templates select="annonce[@categorie='vieux']" mode="complet"/>
	<xsl:apply-templates select="annonce[@categorie='reste']" mode="complet"/>  
  </xsl:if>
</xsl:template>


<xsl:template match="page[@id='accueil']" mode="sommaire">
	<dl>
		<dt><img class="droitehaut" src="skins/default/images/cadre-hautd.gif" alt="" />
			<span> Sommaire </span>	
		</dt>
		<dd>
	<xsl:if test="last() != 0">
		<xsl:if test="count(annonce[@categorie='important']) != 0">
			<div class="fkz_sommaire_titre">
			       <span class="fkz_annonces_important"/> Important
			</div>
			<xsl:apply-templates select="annonce[@categorie='important']" mode="sommaire"/>
		</xsl:if>
		<xsl:if test="count(annonce[@categorie='nouveau']) != 0">
			<div class="fkz_sommaire_titre"><span class="fkz_annonces_nouveau"/> Nouvelles Fraîches</div>
			<xsl:apply-templates select="annonce[@categorie='nouveau']" mode="sommaire"/>
		</xsl:if>
		<xsl:if test="count(annonce[@categorie='vieux']) != 0">
			<div class="fkz_sommaire_titre"><span class="fkz_annonces_vieux"/> Demain c'est fini</div>
			<xsl:apply-templates select="annonce[@categorie='vieux']" mode="sommaire"/>
		</xsl:if>
		<xsl:if test="count(annonce[@categorie='reste']) != 0">
			<div class="fkz_sommaire_titre"><span class="fkz_annonces_reste"/> En attendant...</div>
			<xsl:apply-templates select="annonce[@categorie='reste']" mode="sommaire"/>
		</xsl:if>
	</xsl:if>
		</dd>
		<dd class="bas"><img class="droitebas" src="skins/default/images/cadre-basd.gif" alt="" /></dd>
	</dl>
</xsl:template>

<xsl:template match="annonce" mode="complet">
	<dl>
		<dt>
			<img class="droitehaut" src="skins/default/images/cadre-hautd.gif" alt="" />
			<a><xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute></a>      
			<span><xsl:attribute name="class">fkz_annonces_<xsl:value-of select="@categorie"/></xsl:attribute><xsl:text> </xsl:text></span>
			<span><xsl:value-of select="@titre"/></span> 
		</dt>
		<dd>
		<p class="image">
			<xsl:apply-templates select="image"/>
		</p>
		<p class="news">
		      <xsl:apply-templates select="html"/>
		</p>
		<p class="signature">
			<xsl:choose>
			<xsl:when test="eleve/@surnom != ''">
					<xsl:value-of select="eleve/@surnom"/>
				</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="eleve/@prenom"/><xsl:text>  </xsl:text><xsl:value-of select="eleve/@nom"/>
			</xsl:otherwise>
			</xsl:choose>
     		</p>
		</dd>
		<dd class="bas"><img class="droitebas" src="skins/default/images/cadre-basd.gif" alt="" /></dd>
	</dl>
</xsl:template>

<xsl:template match="annonce" mode="sommaire">
<div class="fkz_sommaire">
       <a> <xsl:attribute name="href">
       <xsl:text>annonces.php#</xsl:text> 
       <xsl:value-of select="@id"/>
       </xsl:attribute>
       	<xsl:value-of select="@titre"/>
	</a>
</div>
</xsl:template>

</xsl:stylesheet>
