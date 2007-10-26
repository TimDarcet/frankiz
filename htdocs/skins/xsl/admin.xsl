<?xml version="1.0" encoding="UTF-8" ?>
<!--        Copyright (C) 2004 Binet Reseau 
	http://www.polytechnique.fr/eleves/binets/br/

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
			      xmlns="http://www.w3.org/1999/xhtml">
<xsl:template match="mac">
	<xsl:value-of select="@time" /> : <a><xsl:attribute name="href"><xsl:text>trombino.php?chercher&amp;mac=</xsl:text><xsl:value-of select="@id" /></xsl:attribute><xsl:value-of select="@id" /></a> <em>(<xsl:value-of select='@constructeur'/>)</em>
        <xsl:if test="count(ip) != 0">
                <ul>
                        <xsl:for-each select="ip">
                                <li><xsl:apply-templates select="."/></li>
                        </xsl:for-each>
                </ul>
        </xsl:if> 
</xsl:template>

<xsl:template match="ip">
        <xsl:value-of select="@id" /> (<xsl:value-of select="@dns"/>) - Client xNet : <xsl:value-of select="@clientxnet"/>/<xsl:value-of select="@os"/>
        <xsl:if test="count(mac) != 0">
                <ul>
                        <xsl:for-each select="mac">
                                <li><xsl:apply-templates select="."/></li>
                        </xsl:for-each>
                </ul>
        </xsl:if>
</xsl:template>

<xsl:template match="prise">
        Prise : <xsl:value-of select="@id" />
	<xsl:if test="count(ip) != 0">
                <ul>
                        <xsl:for-each select="ip">
                                <li><xsl:apply-templates select="."/></li>
                        </xsl:for-each>
                </ul>
        </xsl:if>
</xsl:template>

</xsl:stylesheet>
