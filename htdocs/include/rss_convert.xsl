<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" indent="yes" encoding="ISO-8859-1"/>
	
<xsl:template match="/rss">
	<xsl:for-each select="channel">
		<module>
			<xsl:attribute name="id">rss</xsl:attribute>
			<xsl:attribute name="titre"><xsl:value-of select="title"/></xsl:attribute>
			<lien id='titre'>
				<xsl:attribute name="titre"><xsl:value-of select="title"/></xsl:attribute>
				<xsl:attribute name="url"><xsl:value-of select="link"/></xsl:attribute>
				<xsl:value-of select="description"/>
			</lien>
			<xsl:for-each select="item">
					<lien>
						<xsl:attribute name="titre"><xsl:value-of select="title"/></xsl:attribute>
						<xsl:attribute name="url"><xsl:value-of select="link"/></xsl:attribute>
						<xsl:value-of select="description"/>
					</lien>
			</xsl:for-each>
		</module>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>