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
					<xsl:attribute name="href"><xsl:text>trombino/index.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
					<img heigth="95" width="80" border="0">
					<xsl:attribute name="src"><xsl:text>trombino/index.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
					<xsl:attribute name="alt"><xsl:value-of select="@login"/> (<xsl:value-of select="@promo"/>)</xsl:attribute>
					</img>
				</a>
			</p>
			<hr/>
		</xsl:for-each>
		<xsl:apply-templates select="formulaire"/>
	</div>
</xsl:template>

</xsl:stylesheet>
