<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" indent="yes" encoding="ISO-8859-1"
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>
	
<xsl:param name="sommaire"/>
<xsl:param name="trier_annonces"/>

<!-- a modifier -->
<xsl:include href="html.xsl"/>
<xsl:include href="form.xsl"/>

<xsl:include href="annonces.xsl"/>
<xsl:include href="skins.xsl"/>
<xsl:include href="liens.xsl"/>
<xsl:include href="qdj.xsl"/>
<xsl:include href="anniversaires.xsl"/>
<xsl:include href="activites.xsl"/>
<xsl:include href="tours_kawa.xsl"/>
<xsl:include href="trombino.xsl"/>
<xsl:include href="stats.xsl"/>

<xsl:template match="/">
  <html>
  <head>
      <title>Frankiz II</title>
    <base>
    <xsl:attribute name="href">
       <xsl:value-of select="frankiz/@base"/>
    </xsl:attribute>
    </base>
    <link rel="stylesheet" type="text/css">
    <xsl:attribute name="href">
       <xsl:value-of select="frankiz/@css"/>
    </xsl:attribute>
    </link>
    <xsl:apply-templates select="frankiz/module[@id='liste_css']" mode="css"/>
  </head>
  <body>
	<a href="index.php"><img src="skins/pico/frankiz.png" alt="Frankiz, le site Web des élèves"/></a>

  <xsl:apply-templates select="frankiz/module[@id='liens_navigation']"/>
      <div class="fkz_page">
      <div class="fkz_gauche">
        <xsl:apply-templates select="frankiz/module[@id='tours_kawa']"/>
        <xsl:apply-templates select="frankiz/module[@id='activites']"/>
        <xsl:apply-templates select="frankiz/module[@id='liens_contacts']"/>
	<xsl:apply-templates select="frankiz/module[@id='liens_ecole']"/>
	<xsl:apply-templates select="frankiz/module[@id='stats']"/>
      </div>
      <div class="fkz_centre">
        <xsl:apply-templates select="frankiz/module[@id='anniversaires']"/>
        <br/>
        <xsl:apply-templates select="frankiz/page[@id='annonces']" mode="sommaire"/>
        <xsl:apply-templates select="frankiz/page[@id='annonces']" mode="complet"/>
	<xsl:apply-templates select="frankiz/page[@id='trombino']"/>
	<xsl:apply-templates select="frankiz/page[@id!='annonces' and @id!='trombino']"/>
      </div>
      <div class="fkz_droite">
      <xsl:apply-templates select="frankiz/module[@id='qdj']"/>
      <xsl:apply-templates select="frankiz/module[@id='qdj_hier']"/>
      </div>
      </div>
  </body>
  </html>
</xsl:template>


<xsl:template match="/frankiz/page[@id!='annonces' and @id!='trombino']">
	<div class="fkz_annonces">
		<xsl:apply-templates/>
	</div>
</xsl:template>

<xsl:template match="cadre">
	<h2><xsl:value-of select="@titre"/></h2>
	<div style="text-align: center"><xsl:apply-templates select="image"/></div>
	<xsl:apply-templates select="html"/>
</xsl:template>



<!-- Arbres -->
<xsl:template match="arbre">
	<xsl:if test="boolean(@titre)"><h2><xsl:value-of select="@titre"/></h2></xsl:if>
	<ul><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:apply-templates select="noeud"/>
	</ul>
</xsl:template>

<xsl:template match="noeud">
	<li>
		<xsl:choose><xsl:when test="count(noeud|feuille)">
			<xsl:attribute name="class">noeud_ouvert</xsl:attribute>
		</xsl:when><xsl:otherwise>
			<xsl:attribute name="class">noeud_ferme</xsl:attribute>
		</xsl:otherwise></xsl:choose>
		
		<xsl:choose><xsl:when test="boolean(@lien)">
			<a><xsl:attribute name="href"><xsl:value-of select="@lien"/></xsl:attribute>
				<xsl:value-of select="@titre"/>
			</a>
		</xsl:when><xsl:otherwise>
			<xsl:value-of select="@titre"/>
		</xsl:otherwise></xsl:choose>
		
		<xsl:if test="count(noeud|feuille)">
			<ul class="feuille">
				<xsl:apply-templates select="noeud|feuille"/>
			</ul>
		</xsl:if>
	</li>
</xsl:template>

<xsl:template match="feuille">
	<li>
		<xsl:choose><xsl:when test="boolean(@lien)">
			<a><xsl:attribute name="href"><xsl:value-of select="@lien"/></xsl:attribute>
				<xsl:value-of select="@titre"/>
			</a>
		</xsl:when><xsl:otherwise>
			<xsl:value-of select="@titre"/>
		</xsl:otherwise></xsl:choose>
	</li>
</xsl:template>

<!-- Eleves pour les anniversaires/signatures/qdj 
<xsl:template match="eleve">
	<xsl:choose><xsl:when test="@surnom != ''">
		<xsl:value-of select="@surnom"/>
	</xsl:when><xsl:otherwise>
		<xsl:value-of select="@prenom"/><xsl:text> </xsl:text><xsl:value-of select="@nom"/>
	</xsl:otherwise></xsl:choose>
	<xsl:if test="@promo != ''">
		<xsl:text> (</xsl:text><xsl:value-of select="@promo"/><xsl:text>)</xsl:text>
	</xsl:if>
	<br/>
</xsl:template>
-->

<!-- Page des binets -->

<xsl:template match="page/binet">
	<xsl:if test="preceding-sibling::binet[1]/@categorie != @categorie or position() = 2">
		<h2><xsl:value-of select="@categorie"/></h2>
	</xsl:if>
	
	<a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:apply-templates select="image"/>
	</a>
		<h3><xsl:value-of select="@nom"/></h3>
		<p><xsl:value-of select="description"/></p>
</xsl:template>

</xsl:stylesheet>
