<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="module[@id='tours_kawa']">
  <xsl:if test="@visible='true'">
  <xsl:if test="count(element) !=0">
  <div class="fkz_titre">Tour Kawa</div>
  <xsl:for-each select="element">
    <xsl:if test="@nom = '1'">
      <xsl:text>Aujourd'hui: </xsl:text>
      <xsl:value-of select="current()"/>
      <br/>
    </xsl:if>
    <xsl:if test="@nom = '2'">
      <xsl:text>Demain: </xsl:text>
      <xsl:value-of select="current()"/>    
      <br/>
    </xsl:if>  
  </xsl:for-each>
  </xsl:if>
  </xsl:if>
</xsl:template>

</xsl:stylesheet>
