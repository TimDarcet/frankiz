<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="module[@id='anniversaires']">
<xsl:if test="count(eleve) != 0">
<div class="fkz_anniversaire">Joyeux anniversaire: 
   <xsl:for-each select="eleve">
    <xsl:value-of select="@prenom" />
    <xsl:text> </xsl:text>
    <xsl:value-of select="translate(@nom,'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')" />
    <xsl:text> (</xsl:text><xsl:value-of select="@promo" /><xsl:text>)</xsl:text>
    <xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
  </xsl:for-each>
  <xsl:text> !</xsl:text>
  </div>
</xsl:if>
</xsl:template>

</xsl:stylesheet>
