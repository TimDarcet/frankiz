<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" omit-xml-declaration="yes" indent="yes" encoding="ISO-8859-1"/>

<xsl:template match="/weather">
	<meteo>
		<now>
			<sunrise><xsl:value-of select="loc/sunr"/></sunrise>
			<sunset><xsl:value-of select="loc/suns"/></sunset>
			<temperature><xsl:value-of select="cc/tmp"/></temperature>
			<ciel><xsl:apply-templates select="cc/icon"/></ciel>
			<image><xsl:value-of select="cc/icon"/></image>
			<pression><xsl:value-of select="cc/bar/r"/></pression>
			<vent><xsl:value-of select="cc/wind/s"/> km/h <xsl:value-of select="cc/wind/t"/></vent>
			<humidite><xsl:value-of select="cc/hmid"/></humidite>
		</now>
		<xsl:for-each select="dayf/day">
		<jour>
			<xsl:attribute name="date"><xsl:value-of select="@d"/></xsl:attribute>
			<temperature_hi><xsl:value-of select="hi"/></temperature_hi>
			<temperature_low><xsl:value-of select="low"/></temperature_low>
			<cieljour><xsl:apply-templates select="part[@p='d']/icon"/></cieljour>
			<cielnuit><xsl:apply-templates select="part[@p='n']/icon"/></cielnuit>
			<imagejour><xsl:value-of select="part[@p='d']/icon"/></imagejour>
			<imagenuit><xsl:value-of select="part[@p='n']/icon"/></imagenuit>
		</jour>
		</xsl:for-each>
	</meteo>
</xsl:template>

<xsl:template match="icon">
	<xsl:choose><xsl:when test="text()=31 or text()=32 or text()=36 or text()=33">
		<xsl:text>Ciel decouvert</xsl:text>
	</xsl:when><xsl:when test="text()=0 or text()=3 or text()=4 or text()=17 or text()=35 or text()=47">
		<xsl:text>Orage avec Pluie</xsl:text>
	</xsl:when><xsl:when test="text()=37 or text()=38">
		<xsl:text>Orage</xsl:text>
	</xsl:when><xsl:when test="text()=29 or text()=34">
		<xsl:text>Legers nuages</xsl:text>
	</xsl:when><xsl:when test="text()=30 or text()=44">
		<xsl:text>Quelques nuages</xsl:text>
	</xsl:when><xsl:when test="text()=26 or text()=27">
		<xsl:text>Nuageux</xsl:text>
	</xsl:when><xsl:when test="text()=28">
		<xsl:text>Nombreux nuages</xsl:text>
	</xsl:when><xsl:when test="text()=12">
		<xsl:text>Faible pluie</xsl:text>
	</xsl:when><xsl:when test="text()=12 or text()=39 or text()=40">
		<xsl:text>Pluie</xsl:text>
	</xsl:when><xsl:when test="text()=45">
		<xsl:text>Pluie ou neige</xsl:text>
	</xsl:when><xsl:when test="text()=1 or text()=2">
		<xsl:text>Pluie avec vent</xsl:text>
	</xsl:when><xsl:when test="text()=6 or text()=18">
		<xsl:text>Grele</xsl:text>
	</xsl:when><xsl:when test="text()=10">
		<xsl:text>Pluie givrante</xsl:text>
	</xsl:when><xsl:when test="text()=11">
		<xsl:text>Faible pluie</xsl:text>
	</xsl:when><xsl:when test="text()=7">
		<xsl:text>Pluie ou neige givrante</xsl:text>
	</xsl:when><xsl:when test="text()=13">
		<xsl:text>Neige faible</xsl:text>
	</xsl:when><xsl:when test="text()=14 or text()=41 or text()=42 or text()=46">
		<xsl:text>Chute de neige</xsl:text>
	</xsl:when><xsl:when test="text()=43">
		<xsl:text>Chute de neige et vent</xsl:text>
	</xsl:when><xsl:when test="text()=15 or text()=16">
		<xsl:text>Forte chute de neige</xsl:text>
	</xsl:when><xsl:when test="text()=19 or text()=20 or text()=21 or text()=22">
		<xsl:text>Brume</xsl:text>
	</xsl:when><xsl:when test="text()=9">
		<xsl:text>Brume / Pluie</xsl:text>
	</xsl:when><xsl:when test="text()=8">
		<xsl:text>Brume givrante</xsl:text>
	</xsl:when><xsl:when test="text()=23 or text()=24">
		<xsl:text>Venteux</xsl:text>
	</xsl:when><xsl:when test="text()=25">
		<xsl:text>Givre probable</xsl:text>
	</xsl:when><xsl:otherwise>
		<xsl:text>N/A</xsl:text>
	</xsl:otherwise></xsl:choose>
</xsl:template>

</xsl:stylesheet>