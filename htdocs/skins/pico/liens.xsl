<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">



<xsl:template match="module[@id='liens_contacts']">
 <div class="fkz_titre"> <b>Contacts </b></div>
  <div class="fkz_module">
    <xsl:for-each select="lien">
      <div class="fkz_lien">
      <a>
        <xsl:attribute name="href">
	  <xsl:value-of select="@url"/>
	</xsl:attribute>
	<xsl:value-of select="@titre"/>
      </a>
      </div>
    </xsl:for-each>
  <xsl:text> </xsl:text>
  </div>
  <br/>
</xsl:template>

<xsl:template match="module[@id='liens_ecole']">
   <div class="fkz_titre"> <b>Liens Ecole</b></div>
   <div class="fkz_module">
    <xsl:for-each select="lien">
      <div class="fkz_lien">
      <a>
        <xsl:attribute name="href">
	  <xsl:value-of select="@url"/>
	</xsl:attribute>
	<xsl:value-of select="@titre"/>
      </a>
      </div>
    </xsl:for-each>
  <xsl:text> </xsl:text>
  </div>
  <br/>
</xsl:template>

<xsl:template match="module[@id='liens_navigation']">
    <div class="fkz_liens_nav">
    <xsl:for-each select="lien">
      <a>
        <xsl:attribute name="href">
          <xsl:value-of select="@url"/>
	</xsl:attribute>
	<xsl:value-of select="@titre" />
      </a>
      <xsl:if test="position() != last()">
        <xsl:text> | </xsl:text>
      </xsl:if>
    </xsl:for-each>
    </div>
</xsl:template>




</xsl:stylesheet>
