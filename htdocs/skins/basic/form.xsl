<?xml version="1.0" encoding="ISO-8859-1" ?>
<!--
	Affichage des éléments de formulaire
	
	$Id$
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- Formulaires -->
<xsl:template match="formulaire">
	<xsl:if test="boolean(@titre)">
		<h2><xsl:value-of select="@titre"/></h2>
	</xsl:if>
	<xsl:apply-templates select="commentaire"/>
	<form method="POST"><xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<table class="formulaire" cellspacing="0" cellpadding="0">
			<xsl:if test="boolean(@titre)">
				<tr><td class="titre" colspan="2"><xsl:value-of select="@titre"/></td></tr>
			</xsl:if>
			<xsl:apply-templates select="champ|choix"/>
			<tr><td class="boutons" colspan="2"><center><xsl:apply-templates select="bouton"/></center></td></tr>
		</table>
	</form>
</xsl:template>

<xsl:template match="formulaire/commentaire">
	<p class="commentaire"><xsl:apply-templates/></p>
</xsl:template>

<!-- champs contenant du texte -->
<xsl:template match="formulaire/champ">
	<tr><td class="gauche">
		<xsl:value-of select="@titre"/><xsl:text> :</xsl:text>
	</td><td class="droite">
		<input>
			<xsl:choose>
				<xsl:when test="starts-with(@id,'passwd')"><xsl:attribute name="type">password</xsl:attribute></xsl:when>
				<xsl:otherwise><xsl:attribute name="type">text</xsl:attribute></xsl:otherwise>
			</xsl:choose>
			<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="@valeur"/></xsl:attribute>
		</input>
	</td></tr>
</xsl:template>

<!-- choix multiples (radio, combo ou checkbox) -->
<xsl:template match="formulaire/choix[@type='combo']">
	<tr><td class="gauche">
		<xsl:value-of select="@titre"/><xsl:text> :</xsl:text>
	</td><td class="droite">
		<select><xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:for-each select="option">
				<option>
					<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
					<xsl:if test="../@valeur = @id"><xsl:attribute name="selected"/></xsl:if>
					<xsl:value-of select="@titre"/>
				</option>
			</xsl:for-each>
		</select>
	</td></tr>
</xsl:template>

<xsl:template match="formulaire/choix[@type='radio']">
	<tr><td class="gauche">
		<xsl:value-of select="@titre"/><xsl:text> :</xsl:text>
	</td><td class="droite">
		<xsl:for-each select="option">
			<input type="radio">
				<xsl:attribute name="name"><xsl:value-of select="../@id"/></xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:if test="../@valeur = @id"><xsl:attribute name="checked"/></xsl:if>
				<xsl:value-of select="@titre"/><br/>
			</input>
		</xsl:for-each>
	</td></tr>
</xsl:template>

<xsl:template match="formulaire/choix[@type='checkbox']">
	<tr><td class="gauche">
		<xsl:value-of select="@titre"/><xsl:text> :</xsl:text>
	</td><td class="droite">
		<xsl:for-each select="option">
			<input type="checkbox">
				<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:if test="contains(../@valeur,@id)"><xsl:attribute name="checked"/></xsl:if>
			</input>
			<xsl:value-of select="@titre"/><br/>
		</xsl:for-each>
	</td></tr>
</xsl:template>

<!-- boutons -->
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