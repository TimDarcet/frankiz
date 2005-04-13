<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output  method="xml" omit-xml-declaration="yes" indent="yes" encoding="utf-8"/>

<xsl:param name="mode"/>

<xsl:template match="/rss">
	<xsl:for-each select="channel">
		<module>
			<xsl:attribute name="id">rss</xsl:attribute>
			<xsl:attribute name="titre"><xsl:value-of select="title"/></xsl:attribute>
			<lien id='titre'>
				<xsl:attribute name="titre"><xsl:value-of select="title"/></xsl:attribute>
				<xsl:attribute name="url"><xsl:value-of select="link"/></xsl:attribute>
				<xsl:if test="$mode='complet'"><xsl:value-of select="description"/></xsl:if>
			</lien>
			<xsl:for-each select="item">
					<lien>
						<xsl:attribute name="titre"><xsl:value-of select="title"/></xsl:attribute>
						<xsl:attribute name="url"><xsl:value-of select="link"/></xsl:attribute>
						<xsl:if test="$mode='complet'"><xsl:value-of select="description"/></xsl:if>
					</lien>
			</xsl:for-each>
		</module>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>
