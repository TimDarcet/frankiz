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



<xsl:template match="module[@id='liens_contacts']">
 
  <div class="fkz_module" id='mod_contacts'>
  <div class="fkz_titre"><span id="contacts_logo"><xsl:text> </xsl:text></span><span id="contacts">Contribuer</span></div>
   <div class="fkz_module_corps">
    <ul class="fkz_contact">
    <xsl:for-each select="lien">
      <li class="fkz_contact">
      <a>
        <xsl:attribute name="href">
	  <xsl:value-of select="@url"/>
	</xsl:attribute>
	<xsl:value-of select="@titre"/>
      </a>
      </li>
    </xsl:for-each>
    </ul>
  <xsl:text> </xsl:text>
  </div>
  </div>
</xsl:template>

<xsl:template match="module[@id='liens_ecole']">
	<div class="fkz_module" id='mod_liens_ecole'>
		<div class="fkz_titre"><span id="liens_ecole_logo"><xsl:text> </xsl:text></span><span id="liens_ecole">Liens Utiles</span></div>
		<div class="fkz_module_corps">
			<ul class="fkz_liens">
				<xsl:for-each select="lien">
					<li class="fkz_liens">
						<a>
							<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
							<xsl:if test="boolean(@key)">
								<xsl:attribute name="accesskey"><xsl:value-of select="@key"/></xsl:attribute>
								<xsl:attribute name="title">Acc�s rapide: <xsl:value-of select="@key"/></xsl:attribute>
							</xsl:if>
							<xsl:value-of select="@titre" />
						</a>
					</li>
				</xsl:for-each>
			</ul>
		</div>
	</div>
</xsl:template>

<xsl:template match="module[@id='liens_navigation']">
	 <div class="fkz_module" id='mod_liens_nav'>
	 	<div class="fkz_titre"><span id="navigation_logo"><xsl:text> </xsl:text></span><span id="navigation">Navigation dans le site</span></div>
		<div class="fkz_module_corps">
			<ul class="fkz_liens_nav">
				<xsl:for-each select="lien">
					<xsl:if test="@id='deconnect'"><br/></xsl:if>
					<li class="fkz_liens_nav">
						<a>
							<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
							<xsl:if test="boolean(@key)">
								<xsl:attribute name="accesskey"><xsl:value-of select="@key"/></xsl:attribute>
								<xsl:attribute name="title">Acc�s rapide: <xsl:value-of select="@key"/></xsl:attribute>
							</xsl:if>
							<xsl:value-of select="@titre" />
						</a>
					</li>
				</xsl:for-each>
			</ul>
		</div>
	</div>
</xsl:template>

<xsl:template match="module[@id='liens_profil']">
	 <div class="fkz_module" id='mod_liens_nav'>
	 	<div class="fkz_titre">
			<span id="navigation_logo"><xsl:text> </xsl:text></span>
			<span id="navigation">Profil: <xsl:value-of select="$user_prenom"/> <xsl:text> </xsl:text> <xsl:value-of select="$user_nom"/></span>
		</div>
		<div class="fkz_module_corps">
			<div style="text-align:center;"><xsl:value-of select="$date"/> <xsl:text> </xsl:text> <xsl:value-of select="$heure"/><br/></div>
			<ul class="fkz_liens_nav">
				
				<xsl:for-each select="lien">
					<li class="fkz_liens_nav">
						<a>
							<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
							<xsl:if test="boolean(@key)">
								<xsl:attribute name="accesskey"><xsl:value-of select="@key"/></xsl:attribute>
								<xsl:attribute name="title">Acc�s rapide: <xsl:value-of select="@key"/></xsl:attribute>
							</xsl:if>
							<xsl:value-of select="@titre" />
						</a>
					</li>
				</xsl:for-each>
			</ul>
		</div>
	</div>
</xsl:template>

<xsl:template match="module[@id='liens_navigation']" mode="link">
	<xsl:for-each select="lien">
		<link rel="navigation">
			<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:value-of select="@titre" /></xsl:attribute>
		</link>
	</xsl:for-each>
</xsl:template>

<xsl:template match="module[@id='liens_perso']">
	 <div class="fkz_module" id='mod_liens_perso'>
	 	<div class="fkz_titre"><span id="perso_logo"><xsl:text> </xsl:text></span><span id="perso">Liens Perso</span></div>
		<div class="fkz_module_corps">
			<ul class="fkz_liens">
				<xsl:for-each select="lien">
					<li class="fkz_liens">
						<a>
							<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
							<xsl:if test="boolean(@key)">
								<xsl:attribute name="accesskey"><xsl:value-of select="@key"/></xsl:attribute>
								<xsl:attribute name="title">Acc�s rapide: <xsl:value-of select="@key"/></xsl:attribute>
							</xsl:if>
							<xsl:value-of select="@titre" />
						</a>
					</li>
				</xsl:for-each>
			</ul>
		</div>
	</div>
</xsl:template>

<xsl:template match="module[@id='liens_perso']" mode="link">
	<xsl:for-each select="lien">
		<link rel="bookmark">
			<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:value-of select="@titre" /></xsl:attribute>
		</link>
	</xsl:for-each>
</xsl:template>


</xsl:stylesheet>
