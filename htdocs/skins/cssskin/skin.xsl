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
<xsl:import href="../basic/skin.xsl"/>
<xsl:output method="xml" indent="yes" encoding="ISO-8859-1"
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>

<!-- D�finition d'une page web de frankiz -->
<xsl:template match="/frankiz/page">
	<html>
	<head>
		<title><xsl:value-of select="@titre"/></title>
		<!-- semble ne pas marcher avec certains navigateurs lorsque la base est relative -->
		<base>
			<xsl:attribute name="href">
				<xsl:value-of select="../@base"/>
			</xsl:attribute>
		</base>
		<link rel="stylesheet" type="text/css">
			<xsl:attribute name="href"><xsl:value-of select="../@css"/></xsl:attribute></link>
		<xsl:apply-templates select="frankiz/module[@id='liste_css']" mode="css"/>
	</head>

	<body>
		<div id="frankiz">Frankiz, le serveur des �l�ves</div>
		<div id="modules">
			<xsl:apply-templates select="/frankiz/module"/>
		</div>
		<div id="contenu">
			<xsl:apply-templates/>
		</div>
	</body>
	</html>
</xsl:template>

<!-- D�finition des cadres et du contenu -->
<xsl:template match="/frankiz/module">
	<xsl:if test="(boolean(@visible) = false) or (@visible = 'true')">
		<div class="module">
			<xsl:attribute name="id">module_<xsl:value-of select="@id"/></xsl:attribute>
			<div class="titre"><xsl:value-of select="@titre"/></div>
			<div class="contenu"><xsl:apply-templates/></div>
		</div>
	</xsl:if>
</xsl:template>

<!-- Annonces (une annonce dans un module correspond � une activit�) -->
<xsl:template match="page/annonce">
	<div class="annonce">
		<div class="titre"><xsl:value-of select="@titre"/> (<xsl:value-of select="@date"/>)</div>
		<div class="contenu">
			<xsl:apply-templates/>
			<p class="signature"><xsl:value-of select="@auteur"/></p>
		</div>
	</div>
</xsl:template>

<xsl:template match="module/annonce">
	<xsl:apply-templates/>
</xsl:template>

<!-- statistiques -->
<xsl:template match="statistiques">
	�tat des serveurs�:<br/>
	<xsl:for-each select="serveur">
		- <span><xsl:attribute name="class">serveur_<xsl:value-of select="@etat"/></xsl:attribute>
			<xsl:value-of select="@nom"/></span>
		<xsl:if test="boolean(@uptime)">: <xsl:value-of select="@uptime"/> jours</xsl:if>
		<br/>
	</xsl:for-each>
	Statistiques�:<br/>
	<xsl:for-each select="service">
		- <a>
			<xsl:attribute name="href"><xsl:value-of select="@stat"/></xsl:attribute>
			<xsl:value-of select="@nom"/>
		</a>
		<br/>
	</xsl:for-each>
</xsl:template>

<!-- qdj (peut contenir plus de deux r�ponses) -->
<xsl:template match="module/qdj">
	<xsl:apply-templates select="question"/><br/>
	
	<xsl:choose>
		<!-- l'utilisateur n'a pas encore vot� -->
		<xsl:when test="boolean(@action)">
			<xsl:for-each select="reponse">
				<div>
				<xsl:choose>
					<xsl:when test="@id mod 2">
						<xsl:attribute name="class">fkz_qdj_rouje
						</xsl:attribute>
					</xsl:when>
					<xsl:otherwise>
						<xsl:attribute name="class">fkz_qdj_jone
						</xsl:attribute>
					</xsl:otherwise>
				</xsl:choose>
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="../@action"/><xsl:value-of select="@id"/>
					</xsl:attribute>
					<xsl:apply-templates/>
				</a>
				</div>
			</xsl:for-each>
		</xsl:when>
		
		<!-- l'utilisateur � d�j� vot� (on affiche les r�sultats) -->
		<xsl:otherwise>
			<xsl:variable name="sum_votes" select="sum(reponse/@votes)"/>
			<xsl:choose>
				<xsl:when test="$sum_votes != 0">
					<xsl:for-each select="reponse">
						<xsl:apply-templates/>: <xsl:value-of select="round((@votes * 100) div $sum_votes)"/>%<br/>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise> <!-- petite subtilit� si aucun vote n'a �t� effectu� -->
					<xsl:for-each select="reponse"><xsl:apply-templates/>: 0%<br/></xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
	
	<xsl:for-each select="dernier">
		<xsl:value-of select="@ordre"/><xsl:text> </xsl:text><xsl:apply-templates/><br/>
	</xsl:for-each>
</xsl:template>

<!-- les formulaires -->
<xsl:template match="formulaire">
	<form class="formulaire" enctype="multipart/form-data" method="post">
			<xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<xsl:if test="boolean(@titre)"><h2><xsl:value-of select="@titre"/></h2></xsl:if>
		<xsl:apply-templates select="commentaire|warning"/>
		<xsl:for-each select="*[not (self::bouton or self::commentaire or self::warning)]">
			<div class="champ">
				<div class="champ_nom"><xsl:if test="boolean(@titre)"><xsl:value-of select="@titre"/>�:�</xsl:if></div>
				<div class="champ_valeur"><xsl:apply-templates select="."/></div>
			</div>
		</xsl:for-each>
		<xsl:apply-templates select="bouton"/>
	</form>
</xsl:template>

<xsl:template match="lien">
	<a class="lien"><xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
		<xsl:value-of select="@titre"/><xsl:apply-templates/>
	</a>
</xsl:template>

</xsl:stylesheet>