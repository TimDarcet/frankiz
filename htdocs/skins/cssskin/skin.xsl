<?xml version="1.0" encoding="ISO-8859-1" ?>
<!-- $Id$ -->


<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="../basic/skin.xsl"/>
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
		<link rel="stylesheet" type="text/css">
			<xsl:attribute name="href"><xsl:value-of select="../@css"/></xsl:attribute></link>
		<xsl:apply-templates select="frankiz/module[@id='liste_css']" mode="css"/>
	</head>

	<body>
		<div id="frankiz">Frankiz, le serveur des élèves</div>
		<div id="modules">
			<xsl:apply-templates select="/frankiz/module"/>
		</div>
		<div id="contenu">
			<xsl:apply-templates/>
		</div>
	</body>

	</html>

</xsl:template>

<!-- les CSS complémentaires -->
<xsl:template match="module[@id='liste_css']" mode="css">
	<xsl:for-each select="lien">
		<link rel="alternate stylesheet" type="text/css">
			<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:value-of select="@titre"/></xsl:attribute>
		</link>
    </xsl:for-each>
</xsl:template>

<!-- Définition des cadres et du contenu -->
<xsl:template match="/frankiz/module">
	<xsl:if test="(boolean(@visible) = false) or (@visible = 'true')">
		<div class="module">
			<xsl:attribute name="id">module_<xsl:value-of select="@id"/></xsl:attribute>
			<div class="titre"><xsl:value-of select="@titre"/></div>
			<div class="contenu"><xsl:apply-templates/></div>
		</div>
	</xsl:if>
</xsl:template>

<!-- Annonces (une annonce dans un module correspond à une activité) -->
<xsl:template match="contenu/annonce">
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