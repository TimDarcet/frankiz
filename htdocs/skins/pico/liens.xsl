<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">



<xsl:template match="module[@id='liens_contacts']">
  <xsl:if test="(boolean(@visible) = false) or (@visible = 'true')">
  <b><div class="fkz_titre">Contacts </div></b>
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
  </xsl:if>
</xsl:template>

<xsl:template match="module[@id='liens_ecole']">
  <xsl:if test="(boolean(@visible) = false) or (@visible = 'true')">
  <script>
  <xsl:text>loc_ecole = new Array(</xsl:text>
  <xsl:for-each select="lien">
    <xsl:text>"</xsl:text>
    <xsl:value-of select="@url"/>
    <xsl:text>"</xsl:text>
    <xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
  </xsl:for-each>
  <xsl:text>);
  </xsl:text>

  function gotourl_ecole() {
    sel = document.getElementById("ecoles");
    f = document.forms["ecole"];
    f.setAttribute("action",loc_ecole[sel.selectedIndex]);
  }
  
  </script>
  <form name="ecole" style="valign:bottom">
  <b><xsl:text>Liens de l'école: </xsl:text></b>
  <select onchange="gotourl_ecole()" class="fkz_liens" id="ecoles">
    <xsl:for-each select="lien">
      <option>
        <xsl:attribute name="value">
	  <xsl:value-of select="position()"/>
	</xsl:attribute>
	<xsl:value-of select="@titre"/>
      </option>
    </xsl:for-each>
  </select>
  <xsl:text> </xsl:text>
  <input type="submit" value="Go" onclick="gotourl_ecole()" />
  </form>
  </xsl:if>
</xsl:template>

<xsl:template match="module[@id='liens_navigation']">
    <xsl:if test="(boolean(@visible) = false) or (@visible = 'true')">
    <div class="fkz_liens_nav">
    <xsl:for-each select="lien">
      <a>
        <xsl:attribute name="href">
          <xsl:value-of select="@url"/>
	</xsl:attribute>
	<xsl:value-of select="@titre" />
      </a>
      <xsl:if test="position() != last()">
        <xsl:text> :: </xsl:text>
      </xsl:if>
    </xsl:for-each>
    </div>
    </xsl:if>
</xsl:template>




</xsl:stylesheet>
