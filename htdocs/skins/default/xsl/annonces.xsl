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

<xsl:template match="page[@id='annonces']" mode="complet">
	<xsl:if test="count(annonce)">
			<xsl:apply-templates select="annonce[@categorie='important']" mode="complet"/>
			<xsl:apply-templates select="annonce[@categorie='nouveau']" mode="complet"/>
			<xsl:apply-templates select="annonce[@categorie='vieux']" mode="complet"/>
			<xsl:apply-templates select="annonce[@categorie='reste']" mode="complet"/>  
	</xsl:if>
</xsl:template>


<xsl:template match="page[@id='annonces']" mode="sommaire">
<xsl:choose>
<xsl:when test="count(annonce)">	
	<dl class="boite">
		<xsl:attribute name="id"><xsl:value-of select="@id"/>_sommaire</xsl:attribute>
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span> Sommaire </span>	
		</dt>
		<dd class="contenu">
			<table class="sommaire" >
			<xsl:if test="last() != 0">
				<xsl:if test="count(annonce[@categorie='important']) != 0">
					<tr>
						<td colspan="2" class="center">
							<strong><span class="flag_important"><xsl:text> </xsl:text></span> Important</strong><br/>
						</td>
					</tr>
						<xsl:apply-templates select="annonce[@categorie='important']" mode="sommaire"/>
						<xsl:if test="count(annonce[@categorie='important']) mod 2 = 1">
							<td class="droite"><xsl:text> </xsl:text></td>
						</xsl:if>
				</xsl:if>
				<xsl:if test="count(annonce[@categorie='nouveau']) != 0">
					<tr>
						<td colspan="2" class="center">
							<strong><span class="flag_nouveau"><xsl:text> </xsl:text></span> Nouvelles Fraîches</strong><br/>
						</td>
					</tr>
					
						<xsl:apply-templates select="annonce[@categorie='nouveau']" mode="sommaire"/>
						<xsl:if test="count(annonce[@categorie='nouveau']) mod 2 = 1">
							<td class="droite"><xsl:text> </xsl:text></td>
						</xsl:if>
					
				</xsl:if>
				<xsl:if test="count(annonce[@categorie='vieux']) != 0">
					<tr>
						<td colspan="2" class="center">
							<strong><span class="flag_vieux"><xsl:text> </xsl:text></span> Demain c'est fini</strong><br/>
						</td>
					</tr>
					
						<xsl:apply-templates select="annonce[@categorie='vieux']" mode="sommaire"/>
						<xsl:if test="count(annonce[@categorie='vieux']) mod 2 = 1">
							<td class="droite"><xsl:text> </xsl:text></td>
						</xsl:if>
					
				</xsl:if>
				<xsl:if test="count(annonce[@categorie='reste']) != 0">
					<tr>
						<td colspan="2" class="center">
							<strong><span class="flag_reste"><xsl:text> </xsl:text></span> En attendant...</strong><br/>
						</td>
					</tr>
					
						<xsl:apply-templates select="annonce[@categorie='reste']" mode="sommaire"/>
						<xsl:if test="count(annonce[@categorie='reste']) mod 2 = 1">
							<td class="droite"><xsl:text> </xsl:text></td>
						</xsl:if>
					
				</xsl:if>
			</xsl:if>
			</table>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
</xsl:when>
<xsl:otherwise>
	<dl class="boite">
		<xsl:attribute name="id"><xsl:value-of select="@id"/>_sommaire</xsl:attribute>
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span> Bienvenue Sur Frankiz </span>	
		</dt>
		<dd class="contenu">
			<xsl:apply-templates/>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
</xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template match="annonce" mode="complet">
	<a><xsl:attribute name="name"><xsl:value-of select="concat('annonce_',@id)"/></xsl:attribute>
		<xsl:text> </xsl:text>
	</a>
	<xsl:if test="@visible!='non'">
	<dl class="boite">
		<dt class="titre">
			<xsl:choose>	
				<xsl:when test="count(lien[@id='annonces_lues'])">
					<span class="hidenews">
						<xsl:apply-templates select="lien[@id='annonces_lues']" mode="sansbr"/>
					</span> 
				</xsl:when>
				<xsl:otherwise>
					<span class="droitehaut"><xsl:text> </xsl:text></span>
				</xsl:otherwise>
			</xsl:choose>      
<!-- 			<span><xsl:attribute name="class"><xsl:value-of select="@categorie"/></xsl:attribute><xsl:text> </xsl:text></span> -->
			<span><xsl:value-of select="@titre"/></span>

		</dt>
		<dd class="contenu">
		<p class="image">
			<xsl:text> </xsl:text>
			<xsl:apply-templates select="image"/>
		</p>
		<p class="news">
			<xsl:text> </xsl:text>
			<xsl:apply-templates select="*[not(self::lien/@id='annonces_lues') and name()!='image']"/>
		</p>
		<p class="signature">
			<xsl:text> </xsl:text>
			<xsl:apply-templates select="eleve" mode="signature"/>
     		</p>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
	</xsl:if>
</xsl:template>

<xsl:template match="annonce" mode="sommaire">
	<xsl:choose>
		<xsl:when test="number(position() mod 2 ) = 1">
			<xsl:text disable-output-escaping="yes">&lt;tr&gt;</xsl:text>
			<td>
				<xsl:attribute name="class">
					<xsl:text>gauche</xsl:text>	
				</xsl:attribute>
				<a>
					<xsl:attribute name="href">
						<xsl:if test="@visible='non'">
							<xsl:text>?nonlu=</xsl:text><xsl:value-of select="@id"/>
						</xsl:if>
						<xsl:text>#a</xsl:text><xsl:value-of select="@id"/>
					</xsl:attribute>
					<xsl:value-of select="@titre"/>
				</a>
			</td>	
		</xsl:when>
		<xsl:otherwise>
			<td>
				<xsl:attribute name="class">
					<xsl:text>droite</xsl:text>	
				</xsl:attribute>
				<a>
					<xsl:attribute name="href">
						<xsl:if test="@visible='non'">
							<xsl:text>?nonlu=</xsl:text><xsl:value-of select="@id"/>
						</xsl:if>
						<xsl:text>#a</xsl:text><xsl:value-of select="@id"/>
					</xsl:attribute>
					<xsl:value-of select="@titre"/>
				</a>
			</td>
			<xsl:text disable-output-escaping="yes">&lt;/tr&gt;</xsl:text>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="lien" mode="sansbr">
	<a class="lien">
		<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
		<xsl:attribute name="title"><xsl:value-of select="@titre"/></xsl:attribute>
		<span><xsl:value-of select="@titre"/><xsl:apply-templates/></span>
	</a>
</xsl:template>

<xsl:template match="eleve" mode="signature">
	<xsl:choose>
		<xsl:when test="@surnom != ''">
				<xsl:value-of select="@surnom"/>
			</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="@prenom"/><xsl:text>  </xsl:text><xsl:value-of select="@nom"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>
