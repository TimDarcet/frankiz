<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:param name="sommaire"/>
<xsl:param name="trier_annonces"/>

<!-- a modifier -->
<xsl:include href="../basic/html.xsl"/>
<xsl:include href="../basic/form.xsl"/>

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
  <body marginwidth="0" marginheight="0">
    <table border="0" width="100%">
      <tr>
        <td valign="middle"><a href="index.php"><img src="skins/pico/frankiz.png" border="0"/></a></td>
        <td align="right" valign="bottom"><xsl:apply-templates select="frankiz/page/module[@id='liens_ecole']"/></td>
      </tr>
    </table>
  <xsl:apply-templates select="frankiz/module[@id='liens_navigation']"/>
      <div class="fkz_page">
      <div class="fkz_gauche">
        <xsl:apply-templates select="frankiz/module[@id='tours_kawa']"/>
        <xsl:apply-templates select="frankiz/module[@id='activites']"/>
        <xsl:apply-templates select="frankiz/module[@id='liens_contacts']"/>
	<xsl:apply-templates select="frankiz/module[@id='stats']"/>
      </div>
      <div class="fkz_centre">
        <xsl:apply-templates select="frankiz/module[@id='anniversaires']"/>
        <br/>
        <xsl:apply-templates select="frankiz/page/annonces" mode="sommaire"/>
        <xsl:apply-templates select="frankiz/page/annonces" mode="complet"/>
		<xsl:apply-templates select="frankiz/page"/>
      </div>
      <div class="fkz_droite">
      <xsl:apply-templates select="frankiz/module[@id='qdj']"/>
      </div>
      </div>
  </body>
  </html>
</xsl:template>


</xsl:stylesheet>
