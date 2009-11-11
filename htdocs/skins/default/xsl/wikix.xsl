<?xml version="1.0" encoding="UTF-8" ?>
<!-- $Id$ -->
<!--
	Copyright (C) 2005 Binet Réseau
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

<xsl:template match="module[@id='lien_wikix']">
	<dl id="lien_wikix" class="boite">
          <dt class="titre">
            <span class="droitehaut"><xsl:text> </xsl:text></span><span>WikiX</span>
          </dt>
		<dd class="contenu">
			<form enctype="multipart/form-data" method="post" class="cadretol" accept-charset="UTF-8">
				<xsl:attribute name="action"><xsl:value-of select="formulaire/@action"/></xsl:attribute>
				<div class="center">
					<xsl:apply-templates select="formulaire/hidden"/>
					<input>
						<xsl:attribute name="class">champ</xsl:attribute>
						<xsl:attribute name="name"><xsl:value-of select="formulaire/champ/@id"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="formulaire/champ/@valeur"/></xsl:attribute>
					</input>
					<input type="submit">
						<xsl:attribute name="class">bouton</xsl:attribute>
						<xsl:attribute name="name"><xsl:value-of select="formulaire/bouton/@id"/></xsl:attribute>
						<xsl:attribute name="value">Chercher</xsl:attribute>
					</input>	
				</div>
			</form>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
</xsl:template>

</xsl:stylesheet>