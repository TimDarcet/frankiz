<?xml version="1.0" encoding="ISO-8859-1" ?>
<!-- $Id$ -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<!-- Formulaires -->
<xsl:template match="formulaire">
	<h2><xsl:value-of select="@titre"/></h2>
	<xsl:apply-templates select="commentaire"/>
	<form method="POST"><xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<xsl:apply-templates select="champ|choix|bouton"/>
	</form>
</xsl:template>

<xsl:template match="formulaire/commentaire">
	<p class="commentaire"><xsl:apply-templates/></p>
</xsl:template>

<xsl:template match="formulaire/champ">
	<p><xsl:value-of select="@titre"/><xsl:text> : </xsl:text>
		<input>
			<xsl:choose>
				<xsl:when test="starts-with(@id,'passwd')"><xsl:attribute name="type">password</xsl:attribute></xsl:when>
				<xsl:otherwise><xsl:attribute name="type">text</xsl:attribute></xsl:otherwise>
			</xsl:choose>
			<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:value-of select="@valeur"/>
		</input>
	</p>
</xsl:template>

<xsl:template match="formulaire/choix[@type='combo']">
	<p><xsl:value-of select="@titre"/><xsl:text> : </xsl:text>
		<select><xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:for-each select="option">
				<option>
					<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
					<xsl:if test="../@valeur = @id"><xsl:attribute name="selected"/></xsl:if>
					<xsl:value-of select="@titre"/>
				</option>
			</xsl:for-each>
		</select>
	</p>
</xsl:template>

<xsl:template match="formulaire/choix[@type='radio']">
	<p><xsl:value-of select="@titre"/><xsl:text> : </xsl:text><br/>
		<xsl:for-each select="option">
			<input type="radio">
				<xsl:attribute name="name"><xsl:value-of select="../@id"/></xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:if test="../@valeur = @id"><xsl:attribute name="checked"/></xsl:if>
				<xsl:value-of select="@titre"/><br/>
			</input>
		</xsl:for-each>
	</p>
</xsl:template>

<xsl:template match="formulaire/choix[@type='checkbox']">
	<p><xsl:value-of select="@titre"/><xsl:text> : </xsl:text>
		<xsl:for-each select="option">
			<input type="checkbox">
				<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:if test="contains(../@valeur,@id)"><xsl:attribute name="checked"/></xsl:if>
			</input>
			<xsl:value-of select="@titre"/><br/>
		</xsl:for-each>
	</p>
</xsl:template>

<xsl:template match="formulaire/bouton[@id != 'reset']">
	<input type="submit">
		<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@titre"/></xsl:attribute>
	</input>
</xsl:template>

<xsl:template match="formulaire/bouton[@id = 'reset']">
	<input type="reset">
		<xsl:attribute name="value"><xsl:value-of select="@titre"/></xsl:attribute>
	</input>
</xsl:template>


</xsl:stylesheet>
