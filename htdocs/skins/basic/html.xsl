<?xml version="1.0" encoding="ISO-8859-1" ?>
<!--
	Balises de formatage.
	
	$Log$
	Revision 1.4  2004/09/15 23:19:56  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")

-->
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
	<xsl:if test="boolean(@titre)">
		<h2><xsl:value-of select="@titre"/></h2>
	</xsl:if>
	<xsl:apply-templates select="commentaire"/>
	<form method="POST"><xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<xsl:variable name="nombre_colonnes" select="count(entete)+count(@selectionnable)"/>
		<table class="liste" cellspacing="0" cellpadding="0">
			<tr>
				<xsl:if test="@selectionnable='oui'"><td class="entete">&#160;</td></xsl:if>
				<xsl:apply-templates select="entete"/>
			</tr>
			<xsl:apply-templates select="element"/>
			<tr><td class="boutons"><xsl:attribute name="colspan"><xsl:value-of select="$nombre_colonnes"/></xsl:attribute>
				<xsl:apply-templates select="bouton"/>
			</td></tr>
		</table>
	</form>
</xsl:template>

<xsl:template match="liste/entete">
	<td class="entete">
		<xsl:choose><xsl:when test="boolean(@action)">
			<a><xsl:attribute name="href"><xsl:value-of select="@action"/></xsl:attribute>
				<xsl:value-of select="@titre"/>
			</a>
		</xsl:when><xsl:otherwise>
			<xsl:value-of select="@titre"/>
		</xsl:otherwise></xsl:choose>
	</td>
</xsl:template>

<xsl:template match="liste/element">
	<tr>
		<xsl:if test="../@selectionnable='oui'">
			<td class="element">
				<input type="checkbox">
					<xsl:attribute name="name">elements[<xsl:value-of select="@id"/>]</xsl:attribute>
				</input>
			</td>
		</xsl:if>
		<xsl:for-each select="colonne">
			<td class="element"><xsl:apply-templates/></td>
		</xsl:for-each>
	</tr>
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
