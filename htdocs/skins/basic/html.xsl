<?xml version="1.0" encoding="ISO-8859-1" ?>
<!-- $Id$ -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<!-- Images -->
<xsl:template match="image">
	<img class="image"><xsl:attribute name="src"><xsl:value-of select="@source"/></xsl:attribute></img><br/>
	<xsl:if test="boolean(@legende)"><span class="legende"><xsl:value-of select="@legende"/></span><br/></xsl:if>
</xsl:template>


<!-- Liens -->
<xsl:template match="lien">
	<a class="lien"><xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
		<xsl:value-of select="@titre"/>
		<xsl:if test="boolean(@legende) and count(element)!=0">
			<br/><xsl:apply-templates/>
		</xsl:if>
	</a><br/>
</xsl:template>


<!-- Listes -->
<xsl:template match="liste">
	<ul><xsl:apply-templates/></ul>
</xsl:template>

<xsl:template match="element">
	<li><xsl:apply-templates/></li>
</xsl:template>


<!-- Arbres -->


<!-- Formatage HTML -->
<xsl:template match="p">
	<xsl:apply-templates/><br/>
</xsl:template>
<xsl:template match="h1">
	<h1><xsl:apply-templates/></h1>
</xsl:template>
<xsl:template match="h2">
	<h2><xsl:apply-templates/></h2>
</xsl:template>
<xsl:template match="h3">
	<h3><xsl:apply-templates/></h3>
</xsl:template>
<xsl:template match="h4">
	<h4><xsl:apply-templates/></h4>
</xsl:template>
<xsl:template match="h5">
	<h5><xsl:apply-templates/></h5>
</xsl:template>
<xsl:template match="strong">
	<strong><xsl:apply-templates/></strong>
</xsl:template>
<xsl:template match="code">
	<code><xsl:apply-templates/></code>
</xsl:template>
<xsl:template match="a">
	<a><xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="@href"/></xsl:attribute><xsl:apply-templates/></a>
</xsl:template>


</xsl:stylesheet>
