<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<xsl:template match="module[@id='activites']">
  <xsl:if test="@visible='true'">
  <xsl:if test="count(element) !=0">
  <center>
  <div class="fkz_titre">Activités</div><br/>
  <div class="fkz_module">
  <xsl:for-each select="annonce">
    <xsl:if test="@titre = 'brc'">
      <xsl:if test="current()=0">
        <b><xsl:text>Ce soir, au BRC</xsl:text></b><br/>
      </xsl:if>
      <i><xsl:value-of select="titre"/>
      <xsl:text> </xsl:text>à<xsl:text> </xsl:text>
      <xsl:value-of select="heure"/></i><br/>
    </xsl:if>
    <xsl:apply-templates/>
    <br/>
    <br/>
  </xsl:for-each>
  </div>
  </center>
  <br/>
  </xsl:if>
  </xsl:if>
</xsl:template>

</xsl:stylesheet>
