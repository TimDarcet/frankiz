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
	<xsl:if test="boolean(eleve)">
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
								<xsl:attribute name="href"><xsl:text>trombino.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
								<xsl:attribute name="title"><xsl:value-of select="@prenom"/><xsl:text> </xsl:text><xsl:value-of select="@nom"/></xsl:attribute>
								<img width="100" height="122">
									<xsl:attribute name="src"><xsl:text>trombino.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
									<xsl:attribute name="alt"><xsl:value-of select="@login"/> (<xsl:value-of select="@promo"/>)</xsl:attribute>
								</img>
							</a>
						</dd>	
						<dd class="sport">
							<a>
								<xsl:attribute name="href"><xsl:text>trombino.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
								<img width="100" height="125">
									<xsl:attribute name="src">
										<xsl:text>images/trombino/</xsl:text><xsl:value-of select="@section"/>
										<xsl:choose>
											<xsl:when test="(number(@promo) mod 2)=0">
												<xsl:text>-rouje</xsl:text>
											</xsl:when>
											<xsl:otherwise>
												<xsl:text>-jone</xsl:text>
											</xsl:otherwise>
										</xsl:choose>
										<xsl:text>.png</xsl:text>
									</xsl:attribute>
								</img>
							</a>
						</dd>
						<dd class="element">
							<p class="right"><br/>
								<xsl:value-of select="@promo" /><br/>
								<xsl:value-of select="@surnom" /><br/>
								<br/>
							</p>
							<p class="left"><br/>
								<strong>Tel : </strong><xsl:value-of select="@tel"/><br/>
								<strong>Kzt : </strong><xsl:value-of select="@casert"/><br/>
								<strong>Mail : </strong><a><xsl:attribute name="href"><xsl:text>mailto:</xsl:text><xsl:value-of select="@login"/><xsl:text>@poly</xsl:text></xsl:attribute><xsl:value-of select="@login"/><xsl:text>@poly</xsl:text></a><br/>
							</p>
							<p class="binets">
								<xsl:if test="count(binet)!=0">
									<strong>Binets : </strong>
									<xsl:apply-templates select="binet" mode="trombino"/>
								</xsl:if>
								<br/><br/>
								<xsl:apply-templates select="*[name()!='binet']"/>
							</p>
							<br/>
						</dd>
					</dl>
				</xsl:for-each>
				<br/>
			</dd>
			<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
		</dl>
	</xsl:if>
	<xsl:if test="boolean(formulaire)">
		<dl class="boite">
			<dt class="titre">
				<span class="droitehaut"><xsl:text> </xsl:text></span>
			</dt>
			<dd class="contenu">
					<p class="image"><img src="skins/basic/images/trombino.png"/></p>
					<br/>
					<xsl:apply-templates select="formulaire" mode="trombino"/>
			</dd>
			<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
		</dl>
	</xsl:if>
</xsl:template>

<xsl:template match="binet" mode="trombino">
	<xsl:value-of select="@nom"/><xsl:text> (</xsl:text><em><xsl:value-of select="."/></em><xsl:text>) </xsl:text>
	<xsl:if test="position()!=last()"> - </xsl:if>	
</xsl:template>

<xsl:template match="formulaire" mode="trombino">
	<!-- le formulaire lui même-->
	<form class="trombino" enctype="multipart/form-data" method="post">
		<xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
			<div>
				<p class="droite">
					<xsl:apply-templates select="champ[@id='surnom']" mode="trombino"/>
					<xsl:apply-templates select="choix[@id='binet']" mode="trombino"/>
					<xsl:apply-templates select="champ[@id='casert']" mode="trombino"/>
				</p>
				<p class="gauche">
					<xsl:apply-templates select="champ[@id='prenom']" mode="trombino"/>
					<xsl:apply-templates select="choix[@id='promo']" mode="trombino"/>
					<xsl:apply-templates select="champ[@id='loginpoly']" mode="trombino"/>
				</p>
				<p class="centre">
					<xsl:apply-templates select="champ[@id='nom']" mode="trombino"/>
					<xsl:apply-templates select="choix[@id='section']" mode="trombino"/>
					<xsl:apply-templates select="champ[@id='phone']" mode="trombino"/>
				</p>
			</div>
			<p class="bouton">
				<xsl:apply-templates select="hidden"/>
				<xsl:apply-templates select="bouton"/>
			</p>
	</form>
	<br/>
</xsl:template>

<xsl:template match="champ" mode="trombino">
	<strong><xsl:value-of select="@titre"/></strong><br/>
	<input size="15">
		<xsl:choose>
			<xsl:when test="starts-with(@id,'passwd')"><xsl:attribute name="type">password</xsl:attribute></xsl:when>
			<xsl:otherwise><xsl:attribute name="type">text</xsl:attribute></xsl:otherwise>
		</xsl:choose>
		<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@valeur"/></xsl:attribute>
	</input><br/>
</xsl:template>

<xsl:template match="choix[@type='combo']" mode="trombino">
		<strong><xsl:value-of select="@titre"/></strong><br/>
		<select>
			<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:for-each select="option">
				<option>
					<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
					<xsl:if test="../@valeur = @id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
					<xsl:value-of select="@titre"/>
				</option>
			</xsl:for-each>
		</select><br/>
</xsl:template>
</xsl:stylesheet>
