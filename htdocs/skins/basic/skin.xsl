<?xml version="1.0" encoding="UTF-8" ?>
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
<!--
	Skin très simple utilisant des tables pour la disposition des éléments.
	Le but de cette skin est de ne pas se triturer les neurones pour faire
	une sortie html propre et skinnable quand on travail sur le code php.
	
	$Id$
	
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="html.xsl"/>
<xsl:include href="form.xsl"/>
<xsl:output method="xml" indent="yes" encoding="utf-8"
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>

<xsl:template match="/frankiz"> 
	<xsl:apply-templates select="page"/>
</xsl:template>

<!-- Définition d'une page web de frankiz -->
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
		<link rel="stylesheet" type="text/css" href="skins/basic/style.css"/>
		<xsl:apply-templates select="/frankiz/module[@id='liste_css']" mode="css"/>
	</head>

	<body style="margin: 0">
		<table cellspacing="0" cellpadding="0">
			<tr><td id="frankiz" colspan="2">
				Frankiz, le serveur des élèves
				
			</td></tr><tr><td id="modules">
				<table cellspacing="0" cellpadding="0">
					<xsl:apply-templates select="/frankiz/module"/>
				</table>
				
			</td><td id="contenu">
				<xsl:apply-templates/>
				
			</td></tr>
		</table>
	</body>
	</html>

</xsl:template>

<!-- les CSS complémentaires -->
<xsl:template match="/frankiz/module[@id='liste_css']" mode="css">
	<xsl:for-each select="lien">
		<link rel="alternate stylesheet" type="text/css">
			<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:value-of select="@titre"/></xsl:attribute>
		</link>
    </xsl:for-each>
</xsl:template>

<!-- Définition des modules -->
<xsl:template match="/frankiz/module">
	<xsl:if test="(boolean(@visible) = false) or (@visible = 'true')">
		<tr><th><xsl:value-of select="@titre"/></th></tr>
		<tr><td><xsl:apply-templates/></td></tr>
	</xsl:if>
</xsl:template>

<!-- Annonces (une annonce dans un module correspond à une activité) -->
<xsl:template match="page/annonce">
	<table class="annonce" cellspacing="0" cellpadding="0">
		<tr><th><xsl:value-of select="@titre"/> (<xsl:value-of select="@date"/>)</th></tr>
		<tr><td>
			<center><xsl:apply-templates select="image"/></center>
		</td></tr>
		<tr><td>
			<xsl:apply-templates select="*[not (self::eleve or self::image)]"/>
			<p class="signature"><b><xsl:apply-templates select="eleve"/></b></p>
		</td></tr>
	</table><br />
</xsl:template>

<xsl:template match="cadre">
	<table class="annonce" cellspacing="0" cellpadding="0">
		<tr><th><xsl:value-of select="@titre"/></th></tr>
		<tr><td>
			<center><xsl:apply-templates select="image"/></center>
		</td></tr>
		<tr><td>
			<xsl:apply-templates/>
		</td></tr>
	</table><br />
</xsl:template>

<xsl:template match="module/annonce">
	<xsl:apply-templates/>
</xsl:template>

<!-- statistiques -->
<xsl:template match="statistiques">
	État des serveurs :<br />
	<xsl:for-each select="serveur">
		- <span><xsl:attribute name="class">serveur_<xsl:value-of select="@etat"/></xsl:attribute>
			<xsl:value-of select="@nom"/></span>
		<xsl:if test="boolean(@uptime)">: <xsl:value-of select="@uptime"/> jours</xsl:if>
		<br />
	</xsl:for-each>
	Statistiques :<br />
	<xsl:for-each select="service">
		- <a>
			<xsl:attribute name="href"><xsl:value-of select="@stat"/></xsl:attribute>
			<xsl:value-of select="@nom"/>
		</a>
		<br />
	</xsl:for-each>
</xsl:template>

<!-- qdj (peut contenir plus de deux réponses) -->
<xsl:template match="module/qdj">
	<xsl:apply-templates select="question"/><br />
	
	<xsl:choose>
		<!-- l'utilisateur n'a pas encore voté -->
		<xsl:when test="boolean(@action)">
			<xsl:for-each select="reponse">
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="../@action"/><xsl:value-of select="@id"/>
					</xsl:attribute>
					<xsl:apply-templates/>
				</a><br />
			</xsl:for-each>
		</xsl:when>
		
		<!-- l'utilisateur à déjà voté (on affiche les résultats) -->
		<xsl:otherwise>
			<xsl:variable name="sum_votes" select="sum(reponse/@votes)"/>
			<xsl:choose>
				<xsl:when test="$sum_votes != 0">
					<xsl:for-each select="reponse">
						<xsl:apply-templates/>: <xsl:value-of select="round((@votes * 100) div $sum_votes)"/>%<br />
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise> <!-- petite subtilité si aucun vote n'a été effectué -->
					<xsl:for-each select="reponse"><xsl:apply-templates/>: 0%<br /></xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
	
	<xsl:for-each select="dernier">
		<xsl:apply-templates select="eleve"/>
	</xsl:for-each>
</xsl:template>

<!-- Eleves pour les anniversaires/signatures/qdj -->
<xsl:template match="eleve">
	<xsl:choose><xsl:when test="@surnom != ''">
		<xsl:value-of select="@surnom"/>
	</xsl:when><xsl:otherwise>
		<xsl:value-of select="@prenom"/><xsl:text> </xsl:text><xsl:value-of select="@nom"/>
	</xsl:otherwise></xsl:choose>
	<xsl:if test="@promo != ''">
		<xsl:text> (</xsl:text><xsl:value-of select="@promo"/><xsl:text>)</xsl:text>
	</xsl:if>
	<br />
</xsl:template>

<!-- Eleves pour le trombino -->
<xsl:template match="page/eleve">
	<table class="trombino" cellspacing="0" cellpadding="0">
		<tr><td class="titre" colspan="2">
			<xsl:value-of select="@prenom"/><xsl:text> </xsl:text><xsl:value-of select="@nom"/>
			<xsl:text> (</xsl:text><xsl:value-of select="@promo"/><xsl:text>)</xsl:text>
		</td></tr>
		<tr><td>
			<img alt="photo" width="80" height="95">
				<xsl:attribute name="src">trombino.php?image=true&amp;login=<xsl:value-of select="@login"/>&amp;promo=<xsl:value-of select="@promo"/></xsl:attribute>
			</img>
		</td><td width="100%">
			Surnom : <xsl:value-of select="@surnom"/><br />
			Tel : <xsl:value-of select="@tel"/><br />
			Kazert : <xsl:value-of select="@casert"/><br />
			Mail : <xsl:value-of select="@mail"/><br />
			Section : <xsl:value-of select="@section"/> (<xsl:value-of select="@cie"/>e Cie)<br />
			Binets : <xsl:apply-templates select="binet"/><br />
		</td></tr>
	</table>
</xsl:template>

<xsl:template match="page/eleve/binet">
	<xsl:value-of select="@nom"/><xsl:text> (</xsl:text><xsl:value-of select="."/><xsl:text>) </xsl:text>
</xsl:template>

<!-- Meteo de l'X -->
<xsl:template match="page/meteo">
	<h2>Météo sur le Platâl aujourd'hui :</h2><br />
		Le soleil est présent de <xsl:value-of select="now/sunrise"/> à <xsl:value-of select="now/sunset"/><br />
		La température actuelle est de <xsl:value-of select="now/temperature"/>°C<br />
		La pression est de <xsl:value-of select="now/pression"/> millibar<br />
		Pour ce qui est du vent <xsl:value-of select="now/vent"/><br />
		Et l'humidité s'élève à <xsl:value-of select="now/humidite"/>%<br />
		L'état du ciel : <xsl:value-of select="now/ciel"/><br />
		<img alt="meteo" width="64" height="64">
			<xsl:attribute name="src">skins/basic/images/meteo/<xsl:value-of select="now/image"/>.png</xsl:attribute>
		</img>
	<h2>Prévisions météo :</h2><br />
		<xsl:for-each select="jour">
			<h3>Prévision à <xsl:value-of select="@date"/> jours </h3>
				La température : <xsl:value-of select="temperature_hi"/>°C pendant la journée et <xsl:value-of select="temperature_low"/>°C la nuit<br />	
				Etat du ciel le jour : <xsl:value-of select="cieljour"/>
				<img alt="meteo" width="32" height="32">
					<xsl:attribute name="src">skins/basic/images/meteo/<xsl:value-of select="imagejour"/>.png</xsl:attribute>
				</img><br />
				Etat du ciel la nuit : <xsl:value-of select="cielnuit"/>
				<img alt="meteo" width="32" height="32">
					<xsl:attribute name="src">skins/basic/images/meteo/<xsl:value-of select="imagenuit"/>.png</xsl:attribute>
				</img><br />
		</xsl:for-each>
</xsl:template>

<xsl:template match="module/meteo">
		<div align="center">
		<xsl:value-of select="now/temperature"/>°C<br />
		<img alt="meteo" width="64" height="64">
			<xsl:attribute name="src">skins/basic/images/meteo/<xsl:value-of select="now/image"/>.png</xsl:attribute>
		</img>
		</div>
</xsl:template>

<!-- Page des binets -->
<xsl:template match="page/binet">
	<xsl:if test="preceding-sibling::binet[1]/@categorie != @categorie or position() = 2">
		<!-- TODO comprendre pourquoi postion() = 2 et pas 1 :) -->
		<xsl:text disable-output-escaping="yes">&lt;table class="binets" cellpadding="0" cellspacing="0"&gt;</xsl:text>
		<tr><th colspan="2"><h2><xsl:value-of select="@categorie"/></h2></th></tr>
	</xsl:if>
	
	<tr><td width="120">
		<a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:apply-templates select="image"/>
		</a>
	</td><td width="100%">
		<h3><xsl:value-of select="@nom"/></h3>
		<p><xsl:value-of select="description"/></p>
	</td></tr>
	
	<xsl:if test="following-sibling::binet[1]/@categorie != @categorie or position() = last()-1">
		<!-- TODO comprendre pourquoi postion() = last()-1 et pas last() :) -->
		<xsl:text disable-output-escaping="yes">&lt;/table&gt;</xsl:text>
	</xsl:if>
</xsl:template>

<!-- Affichage agressif du module su -->
<xsl:template match="module[@id='su']">
	<div style="display:bloc; position:fixed; top:0; left:0; right:0; background:red; font-weight:bold">
		<xsl:apply-templates/>
	</div>
</xsl:template>

</xsl:stylesheet>
