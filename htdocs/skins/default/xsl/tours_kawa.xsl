<?xml version="1.0" encoding="UTF-8" ?>
<!-- $Id$ -->
<!--
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

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="module[@id='tour_kawa']">
	<xsl:if test="count(liste/element) !=0">
		<dl id="tour_kawa" class="boite">
          <dt class="titre">
            <span class="droitehaut"><xsl:text> </xsl:text></span><span>Tour Kawa</span>
          </dt>
			<dd class="contenu">
				<ul class="none">
				<xsl:for-each select="liste/element">
					<li><strong><xsl:value-of select="colonne[@id='jour']"/>: </strong><br/>
					<span class="tour_kawa"><xsl:value-of select="colonne[@id='kawa']"/></span></li>
				</xsl:for-each>
				</ul>
			</dd>
			<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
		</dl>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
