<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="module[@id='stats']">
		<div class="fkz_titre">État des serveurs :</div>
		<div class="fkz_module">
		<ul class="fkz_stats">
		<xsl:for-each select="statistiques/serveur">
			<li class="fkz_stats">
				<span>
					<xsl:attribute name="class">serveur_<xsl:value-of select="@etat"/></xsl:attribute>
					<xsl:value-of select="@nom"/>
				</span>
			<xsl:if test="boolean(@uptime)">: <xsl:value-of select="@uptime"/> jours</xsl:if>
			</li>
		</xsl:for-each>
		</ul>
		</div>
		
		<div class="fkz_titre">Statistiques :</div>
		<div class="fkz_module">
		<ul class="fkz_stats">
		<xsl:for-each select="statistiques/service">
			<li class="fkz_stats">
			<a>
				<xsl:attribute name="href"><xsl:value-of select="@stat"/></xsl:attribute>
				<xsl:value-of select="@nom"/>
			</a>
			</li>
		</xsl:for-each>
		</ul>
		</div>
</xsl:template>


</xsl:stylesheet>