<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="page[@id='annonces']" mode="complet">
  <xsl:if test="$trier_annonces='pas_tri'">
  <xsl:if test="last() != 0">
       <xsl:apply-templates select="annonce" mode="complet"/>
  </xsl:if>
  </xsl:if>
  <xsl:if test="$trier_annonces='trie'">  
  <xsl:if test="last() != 0">
    <xsl:apply-templates select="annonce[@categorie='important']" mode="complet"/>
    <xsl:apply-templates select="annonce[@categorie='nouveau']" mode="complet"/>
    <xsl:apply-templates select="annonce[@categorie='vieux']" mode="complet"/>
    <xsl:apply-templates select="annonce[@categorie='reste']" mode="complet"/>  
  </xsl:if>
  </xsl:if>
</xsl:template>


<xsl:template match="page[@id='annonces']" mode="sommaire">
<xsl:if test="$sommaire='pas_tri'">
	<xsl:if test="last() != 0">
		<xsl:apply-templates select="annonce" mode="sommaire"/>
		<br/>
	</xsl:if>
</xsl:if>
<xsl:if test="$sommaire='trie'">
	<xsl:if test="last() != 0">
		<xsl:if test="count(annonce[@categorie='important']) != 0">
			<div class="fkz_sommaire_titre"><img src="http://frankiz/accueil/info/icones/important.gif" alt="important"/> Important</div>
			<div class="fkz_module">
			<xsl:apply-templates select="annonce[@categorie='important']" mode="sommaire"/>
			</div>
		</xsl:if>
		<xsl:if test="count(annonce[@categorie='nouveau']) != 0">
			<div class="fkz_sommaire_titre"><img src="http://frankiz/accueil/info/icones/nouveau.gif" alt="nouveau"/> Nouvelles Fraîches</div>
			<div class="fkz_module">
			<xsl:apply-templates select="annonce[@categorie='nouveau']" mode="sommaire"/>
			</div>
		</xsl:if>
		<xsl:if test="count(annonce[@categorie='vieux']) != 0">
			<div class="fkz_sommaire_titre"><img src="http://frankiz/accueil/info/icones/vieux.gif" alt="vieux"/> Demain c'est fini</div>
			<div class="fkz_module">
			<xsl:apply-templates select="annonce[@categorie='vieux']" mode="sommaire"/>
			</div>
		</xsl:if>
		<xsl:if test="count(annonce[@categorie='reste']) != 0">
			<div class="fkz_sommaire_titre"><img src="http://frankiz/accueil/info/icones/reste.gif" alt="reste"/> En attendant...</div>
			<div class="fkz_module">
			<xsl:apply-templates select="annonce[@categorie='reste']" mode="sommaire"/>
			</div>
		</xsl:if>
		<br/>
	</xsl:if>
</xsl:if>
</xsl:template>

<xsl:template match="annonce" mode="complet">
<div class="fkz_annonces">
       <a> <xsl:attribute name="name">
       <xsl:value-of select="@id"/>
       </xsl:attribute>
       </a>
       <div style="text-align: center"><b>
       <img>
          <xsl:attribute name="alt">
	  <xsl:value-of select="@categorie"/>
	</xsl:attribute>
        <xsl:attribute name="src">
	  <xsl:text>http://frankiz/accueil/info/icones/</xsl:text>
	  <xsl:value-of select="@categorie"/>
	  <xsl:text>.gif</xsl:text>
	</xsl:attribute>
      </img>
      <xsl:text> </xsl:text>
	<xsl:value-of select="@titre"/>
      </b></div>
      <br/>
      <xsl:value-of select="current()"/>
      <p style="text-align: right">
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
       <xsl:text>#</xsl:text> 
       <xsl:value-of select="@id"/>
       </xsl:attribute>
       	<xsl:value-of select="@titre"/>
	</a>
</div>
</xsl:template>

</xsl:stylesheet>
