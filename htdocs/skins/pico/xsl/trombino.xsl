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

<xsl:template match="eleve">
	<div class="fkz_trombino_eleve_1"><div class="fkz_trombino_eleve_2">
	<div class="fkz_trombino_eleve_3"><div class="fkz_trombino_eleve_4">
	<div class="fkz_trombino_eleve_5"><div class="fkz_trombino_eleve_6">
	<div class="fkz_trombino_eleve">
	<h3 class="nom">
		<xsl:value-of select="@prenom" />
		<xsl:text> </xsl:text>
		<xsl:value-of select="translate(@nom,'abcdefghijklmnopqrstuvwxyzéèàçêëù','ABCDEFGHIJKLMNOPQRSTUVWXYZÉÈÀÇÊËÙ')" />
	</h3>
	<div class="fkz_trombino_photo">
		<a>
			<xsl:attribute name="href"><xsl:text>trombino.php?image=show&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
			<img height="122" width="80">
			<xsl:attribute name="src"><xsl:text>trombino.php?image=true&amp;login=</xsl:text><xsl:value-of select="@login"/><xsl:text>&amp;promo=</xsl:text><xsl:value-of select="@promo"/></xsl:attribute>
			<xsl:attribute name="alt"><xsl:value-of select="@login"/> (<xsl:value-of select="@promo"/>)</xsl:attribute>
			</img>
		</a>
	</div>
	<div class="fkz_trombino_infos">
	<p class="telephone">Tel: <xsl:value-of select="@tel"/>
	</p>
	<p class="mail">Mail: <a><xsl:attribute name="href">mailto:<xsl:value-of select="@mail"/></xsl:attribute><xsl:value-of select="@mail"/></a>
	</p>
	<p class="casert">Casert: <xsl:value-of select="@casert"/>
	</p>
	<p class="section">Section: <xsl:value-of select="@section"/>
	</p>
	</div>
	<div class="fkz_trombino_section">
		<a>
			<xsl:attribute name="href"><xsl:text>trombino.php?sections=</xsl:text><xsl:value-of select="@section"/></xsl:attribute>
			<img height="84" width="63">
			<xsl:attribute name="src">skins/pico/default/images/sections/<xsl:value-of select="translate(@section,'ABCDEFGHIJKLMNOPQRSTUVWXYZéè','abcdefghijklmnopqrstuvwxyzee')"/><xsl:value-of select="@promo mod 2"/>.jpg</xsl:attribute>
			<xsl:attribute name="alt"><xsl:value-of select="@section"/></xsl:attribute>
			</img>
		</a>
	</div>
	<div class="fkz_trombino_infos2">
		<p class="promo">
			<xsl:value-of select="@promo" />
		</p>
		<p class="surnom">
			<xsl:value-of select="@surnom" />
		</p>
		<p class="date_naissance">
			<xsl:value-of select="@date_nais" />
		</p>
	</div>
	<div class="binets">
		<xsl:apply-templates select="prise"/>
		<xsl:if test="count(binet) != 0">
		Binets :
			<ul>
			<xsl:for-each select="binet">
				<li><a>	<xsl:attribute name="href"><xsl:text>trombino.php?binets=</xsl:text><xsl:value-of select="@nom"/></xsl:attribute><xsl:value-of select="@nom"/></a><xsl:text>  </xsl:text><xsl:if test="text()!=''"><em>(<xsl:value-of select="text()"/>)</em></xsl:if></li>
			</xsl:for-each>
			</ul>
		</xsl:if>
		<xsl:text> </xsl:text>
	</div>
		<xsl:apply-templates select="*[name()!='binet' and name()!='prise']"/>
	</div>
	</div></div></div></div></div></div>
</xsl:template>

<xsl:template match="page[@id='trombino']">
	<div class="fkz_trombino_1"><div class="fkz_trombino_2">
	<div class="fkz_trombino_3"><div class="fkz_trombino_4">
	<div class="fkz_trombino_5"><div class="fkz_trombino_6">
	<div class="fkz_trombino">
		<xsl:apply-templates/>
	</div>
	</div></div></div></div></div></div>
</xsl:template>

<xsl:template match="formulaire[../@id='trombino']">
	<!-- le formulaire lui même-->
	<form class="trombino" enctype="multipart/form-data" method="post"  accept-charset="UTF-8">
		<xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
			<div class="fkz_trombino_eleve_1"><div class="fkz_trombino_eleve_2">
			<div class="fkz_trombino_eleve_3"><div class="fkz_trombino_eleve_4">
			<div class="fkz_trombino_eleve_5"><div class="fkz_trombino_eleve_6">
			<div class="fkz_trombino_eleve">
			<h3>Rechercher sur le trombino</h3>
				<table>
					<tr>
						<td>
						<label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'prenom')"/></xsl:attribute>
							Prénom :
						</label>
						<xsl:apply-templates select="champ[@id='prenom']"/>
						</td>
						<td>
						<label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'nom')"/></xsl:attribute>
							Nom :
						</label>
						<xsl:apply-templates select="champ[@id='nom']"/>
						</td>
						<td>
						<label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'surnom')"/></xsl:attribute>
							Surnom :
						</label>
							<xsl:apply-templates select="champ[@id='surnom']"/>
						</td>
					</tr>
					<tr>
						<td>
						<label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'promo')"/></xsl:attribute>
							Promo :
						</label>
							<xsl:apply-templates select="choix[@id='promo']"/>
						</td>
						<td>
						<label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'section')"/></xsl:attribute>
							Section :
						</label>
							<xsl:apply-templates select="choix[@id='section']"/>
						</td>
						<td>
						<label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'binet')"/></xsl:attribute>
							Binet :
						</label>
							<xsl:apply-templates select="choix[@id='binet']"/>
						</td>
					</tr>
					<tr>
						<td>
						<label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'loginpoly')"/></xsl:attribute>
							Login :
						</label>
							<xsl:apply-templates select="champ[@id='loginpoly']"/>
						</td>
						<td>
						<label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'phone')"/></xsl:attribute>
							Tel :
						</label>
							<xsl:apply-templates select="champ[@id='phone']"/>
						</td>
						<td>
						<label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'casert')"/></xsl:attribute>
							Casert :
						</label>
							<xsl:apply-templates select="champ[@id='casert']"/>
						</td>
					</tr>
					<xsl:if test="count(champ) > 6">
                                        <tr>
                                                <td>
                                                <label class="gauche" >
                                                        <xsl:attribute name='for'><xsl:value-of select="concat(@id,'ip')"/></xsl:attribute>
                                                	IP :
                                                </label>
                                                        <xsl:apply-templates select="champ[@id='ip']"/>
                                                </td>
                                                <td>
                                                <label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'dns')"/></xsl:attribute>
                                                	DNS :
                                                </label>
                                                        <xsl:apply-templates select="champ[@id='dns']"/>
                                                </td>
                                                <td>
                                                <label class="gauche" >
                                                         <xsl:attribute name='for'><xsl:value-of select="concat(@id,'prise')"/></xsl:attribute>
                                                	Prise :
                                                </label>
                                                         <xsl:apply-templates select="champ[@id='prise']"/>
                                                </td>
                                        </tr>
					<tr>
						<td></td>
						<td>
						<label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'mac')"/></xsl:attribute>
							@mac :
						</label>
							<xsl:apply-templates select="champ[@id='mac']"/>
						</td>
						<td>
						<label class="gauche" >
							<xsl:attribute name='for'><xsl:value-of select="concat(@id,'admin')"/></xsl:attribute>
							Tol Admin :
						</label>
							<xsl:apply-templates select="choix[@id='admin']"/>
						</td>
						<td></td>
					</tr>
					</xsl:if>
				</table>
			<p class="bouton">
				<xsl:apply-templates select="hidden"/>
				<xsl:apply-templates select="bouton"/>
			</p>
			</div>
			</div></div></div></div></div></div>
	</form>
	<br/>
</xsl:template>

</xsl:stylesheet>

