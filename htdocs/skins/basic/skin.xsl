<?xml version="1.0" encoding="ISO-8859-1" ?>
<!--
	Skin très simple utilisant des tables pour la disposition des éléments.
	Le but de cette skin est de ne pas se triturer les neurones pour faire
	une sortie html propre et skinnable quand on travail sur le code php.
	
	$Id$
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="html.xsl"/>
<xsl:include href="form.xsl"/>
<xsl:output method="html" encoding="ISO-8859-1"/>

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
		<xsl:apply-templates select="frankiz/module[@id='liste_css']"/>
	</head>

	<body style="margin: 0 0 0 0">
		<table cellspacing="0" cellpadding="0">
			<tr><td id="frankiz" colspan="2">
				Frankiz, le serveur des élèves
				
			</td></tr> <tr><td id="cadres">
				<table cellspacing="0" cellpadding="0">
						<xsl:apply-templates select="module"/>
				</table>
				
			</td><td id="contenu">
				<xsl:apply-templates select="contenu"/>
				
			</td></tr>
		</table>
	</body>

	</html>

</xsl:template>

<!-- les CSS complémentaires -->
<xsl:template match="module[@id='liste_css']">
<xsl:for-each select="element">
    <link rel="alternate stylesheet" type="text/css">
    <xsl:attribute name="href">
       <xsl:value-of select="@url"/>
    </xsl:attribute>
    <xsl:attribute name="title">
    	<xsl:value-of select="@nom"/>
    </xsl:attribute>
    </link>
    </xsl:for-each>
</xsl:template>

<!-- Définition des cadres et du contenu -->
<xsl:template match="/frankiz/page/module">
	<xsl:if test="(boolean(@visible) = false) or (@visible = 'true')">
		<tr><th class="cadre"><xsl:value-of select="@titre"/></th></tr>
		<tr><td class="cadre"><xsl:apply-templates/></td></tr>
	</xsl:if>
</xsl:template>

<xsl:template match="/frankiz/page/contenu">
	<xsl:apply-templates/>
</xsl:template>

<!-- Annonces (une annonce dans un module correspond à une activité) -->
<xsl:template match="liste/annonce">
	<table class="annonce" cellspacing="0" cellpadding="0">
		<tr><th class="annonce"><xsl:value-of select="@titre"/> (<xsl:value-of select="@date"/>)</th></tr>
		<tr><td class="annonce">
			<xsl:apply-templates/>
			<p class="signature"><xsl:value-of select="@auteur"/></p>
		</td></tr>
	</table><br/>
</xsl:template>

<xsl:template match="module/annonce">
	<xsl:apply-templates/>
</xsl:template>

<!-- statistiques -->
<xsl:template match="statistiques">
	État des serveurs :<br/>
	<xsl:for-each select="serveur">
		- <span><xsl:attribute name="class">serveur_<xsl:value-of select="@etat"/></xsl:attribute>
			<xsl:value-of select="@nom"/></span>
		<xsl:if test="boolean(@uptime)">: <xsl:value-of select="@uptime"/> jours</xsl:if>
		<br/>
	</xsl:for-each>
	Statistiques :<br/>
	<xsl:for-each select="service">
		- <a>
			<xsl:attribute name="href"><xsl:value-of select="@stat"/></xsl:attribute>
			<xsl:value-of select="@nom"/>
		</a>
		<br/>
	</xsl:for-each>
</xsl:template>

<!-- qdj (peut contenir plus de deux réponses) -->
<xsl:template match="module/qdj">
	<xsl:apply-templates select="question"/><br/>
	
	<xsl:choose>
		<!-- l'utilisateur n'a pas encore voté -->
		<xsl:when test="boolean(@action)">
			<xsl:for-each select="reponse">
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="../@action"/><xsl:value-of select="@id"/>
					</xsl:attribute>
					<xsl:apply-templates/>
				</a><br/>
			</xsl:for-each>
		</xsl:when>
		
		<!-- l'utilisateur à déjà voté (on affiche les résultats) -->
		<xsl:otherwise>
			<xsl:variable name="sum_votes" select="sum(reponse/@votes)"/>
			<xsl:choose>
				<xsl:when test="$sum_votes != 0">
					<xsl:for-each select="reponse">
						<xsl:apply-templates/>: <xsl:value-of select="round((@votes * 100) div $sum_votes)"/>%<br/>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise> <!-- petite subtilité si aucun vote n'a été effectué -->
					<xsl:for-each select="reponse"><xsl:apply-templates/>: 0%<br/></xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
	
	<xsl:for-each select="dernier">
		<xsl:value-of select="@ordre"/><xsl:text> </xsl:text><xsl:apply-templates/><br/>
	</xsl:for-each>
</xsl:template>


<!-- Eleves (pour les anniversaires) -->
<xsl:template match="eleve">
	<xsl:value-of select="@prenom"/><xsl:text> </xsl:text>
	<xsl:value-of select="@nom"/><xsl:text> (</xsl:text>
	<xsl:value-of select="@promo"/><xsl:text>)</xsl:text>
	<br/>
</xsl:template>

</xsl:stylesheet>