<?xml version="1.0" encoding="ISO-8859-1" ?>
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
	s
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="page[@id='trombino']">
	<dl class="boite">
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span>
				Résultat de la recherche
			</span>	
		</dt>
		<dd class="contenu">
			<br/>
			<xsl:for-each select="eleve">
                		<dl>
					<xsl:attribute name="class">trombino<xsl:text> </xsl:text><xsl:if test="(position() mod 2)=0">pair</xsl:if><xsl:if test="(position() mod 2)=1">impair</xsl:if></xsl:attribute>
                  			<dt class="nom">
						<xsl:value-of select="@prenom" />
						<xsl:text> </xsl:text>
						<xsl:value-of select="translate(@nom,'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')" />
					</dt> 
		  			<dd class="photo">
						<a>
							<xsl:attribute name="href"><xsl:text>trombino/index.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
							<xsl:attribute name="title"><xsl:value-of select="@prenom"/><xsl:text> </xsl:text><xsl:value-of select="@nom"/></xsl:attribute>
							<img width="100" height="125">
								<xsl:attribute name="src"><xsl:text>trombino/index.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
								<xsl:attribute name="alt"><xsl:value-of select="@login"/> (<xsl:value-of select="@promo"/>)</xsl:attribute>
							</img>
						</a>
					</dd>	
		  			<dd class="sport">
						<a>
							<xsl:attribute name="href"><xsl:text>trombino/index.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
							<img src="" width="100" height="125"/>
						</a>
					</dd>
                  			<dd class="element">
						<p class="right"><br/>
							<xsl:value-of select="@promo" />
							<xsl:value-of select="@surnom" /><br/>
							15/12/82<br/>
						</p>
						<p class="left"><br/>
							<strong>Tel : </strong><xsl:value-of select="@phone"/><br/>
							<strong>Kzt : </strong><xsl:value-of select="@casert"/><br/>
							<strong>Mail : </strong><a href="mailto:jaeck@poly">jaeck@poly</a><br/>
						</p>
						<p class="binets"><strong>Binets : </strong>
							<xsl:for-each select="liste[@id='binets']/element">
								<xsl:value-of select="current()"/>, 
							</xsl:for-each>
						</p>
		  			</dd>	
                		</dl>
			</xsl:for-each>
			<br/>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
	<xsl:if test="boolean(formulaire)">
		<dl class="boite">
			<dt class="titre">
				<span class="droitehaut"><xsl:text> </xsl:text></span>
				<span><xsl:value-of select="formulaire/@titre"/></span>	
			</dt>
			<dd class="contenu">
				<xsl:apply-templates select="formulaire"/>
			</dd>
			<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
		</dl>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
