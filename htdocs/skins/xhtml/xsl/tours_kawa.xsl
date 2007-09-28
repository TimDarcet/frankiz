<?xml version="1.0" encoding="UTF-8" ?>
<!-- 
	$Id$

	Copyright (C) 2004 Binet Réseau
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

<xsl:template match="module[@id='tour_kawa']">
	<xsl:if test="count(liste/element) !=0">
		<div class="fkz_module_1"><div class="fkz_module_2">
		<div class="fkz_module_3"><div class="fkz_module_4">
		<div class="fkz_module_5"><div class="fkz_module_6">
		<div class="fkz_module">
			<div class="fkz_titre"><span id="tour_kawa_logo"><xsl:text> </xsl:text></span><span id="tour_kawa">Tour Kawa</span></div>
			<div class="fkz_module_corps">
				<xsl:for-each select="liste/element">
					<em><xsl:value-of select="colonne[@id='jour']"/>: </em>
					<xsl:value-of select="colonne[@id='kawa']"/>
					<br/>
				</xsl:for-each>
			</div>
		</div>
		</div></div></div></div></div></div>
	</xsl:if>

</xsl:template>

</xsl:stylesheet>
