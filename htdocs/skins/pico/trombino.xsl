<?xml version="1.0" encoding="ISO-8859-1" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="page[@id='trombino']">
	<div class="fkz_trombino">
		<xsl:for-each select="eleve">
			<p>
				<xsl:value-of select="@prenom" />
				<xsl:text> </xsl:text>
				<xsl:value-of select="translate(@nom,'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')" />
			</p>
			<p>
				<xsl:value-of select="@surnom" />
			</p>
			<p>
				<xsl:value-of select="@promo" />
			</p>
			<p>Tel:
				<xsl:value-of select="@phone"/>  Casert:
			<xsl:value-of select="@casert"/>
			</p>
			<p>Section: <xsl:value-of select="@section"/>
			</p>
			<p>Binets:
				<xsl:for-each select="liste[@id='binets']/element">
					<xsl:value-of select="current()"/>, 
				</xsl:for-each>
			</p>
			<p>
				<a>
					<xsl:attribute name="href"><xsl:text>trombino/trombino.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
					<img heigth="95" width="80" border="0"><xsl:attribute name="src"><xsl:text>trombino/trombino.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute></img>
				</a>
			</p>
			<hr/>
		</xsl:for-each>
		<xsl:apply-templates select="formulaire"/>
	</div>
</xsl:template>

<!--
<xsl:template match="formulaire[@id='trombino']">
	<form method="post"><xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<xsl:apply-templates/>
	</form>
</xsl:template>

<xsl:template match="formulaire[@id='trombino']/champ">
	<xsl:value-of select="@titre"/><xsl:text> : </xsl:text>
		<input type="text"><xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:value-of select="@valeur"/>
		</input>
		<xsl:choose>
		<xsl:when test="@id='surnom' or @id='casert'"><br/><br/></xsl:when>
		<xsl:otherwise><xsl:text>	</xsl:text></xsl:otherwise>
		</xsl:choose>
</xsl:template>

<xsl:template match="formulaire[@id='trombino']/choix[@type='combo']">
	<xsl:value-of select="@titre"/><xsl:text> : </xsl:text>
		<select><xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:for-each select="option">
				<option>
					<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
					<xsl:if test="../@valeur = @id"><xsl:attribute name="selected"/></xsl:if>
					<xsl:value-of select="@titre"/>
				</option>
			</xsl:for-each>
		</select>
		<xsl:choose>
		<xsl:when test="@id='binet'"><br/><br/></xsl:when>
		<xsl:otherwise><xsl:text>	</xsl:text></xsl:otherwise>
		</xsl:choose>
</xsl:template>

<xsl:template match="formulaire[@id='trombino']/choix[@type='radio']">
	<xsl:value-of select="@titre"/><xsl:text> : </xsl:text><br/>
		<xsl:for-each select="option">
			<input type="radio">
				<xsl:attribute name="name"><xsl:value-of select="../@id"/></xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:if test="../@valeur = @id"><xsl:attribute name="checked"/></xsl:if>
				<xsl:value-of select="@titre"/><br/>
			</input>
		</xsl:for-each>
</xsl:template>

<xsl:template match="formulaire[@id='trombino']/choix[@type='checkbox']">
	<xsl:value-of select="@titre"/><xsl:text> : </xsl:text>
		<xsl:for-each select="option">
			<input type="checkbox">
				<xsl:attribute name="name"><xsl:value-of select="../@id"/></xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:if test="contains(../@valeur,@id)"><xsl:attribute name="checked"/></xsl:if>
			</input>
			<xsl:value-of select="@titre"/><br/>
		</xsl:for-each>
</xsl:template>

<xsl:template match="formulaire[@id='trombino']/bouton[@id != 'reset']">
	<input type="submit">
		<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@titre"/></xsl:attribute>
	</input>
</xsl:template>

<xsl:template match="formulaire[@id='trombino']/bouton[@id = 'reset']">
	<input type="reset">
		<xsl:attribute name="value"><xsl:value-of select="@titre"/></xsl:attribute>
	</input>
</xsl:template>
-->
</xsl:stylesheet>
