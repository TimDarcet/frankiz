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
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" indent="yes" encoding="ISO-8859-1"
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>
	
<xsl:param name="sommaire"/>
<xsl:param name="trier_annonces"/>

<!-- a modifier -->
<xsl:include href="html.xsl"/>
<xsl:include href="form.xsl"/>

<xsl:include href="annonces.xsl"/>
<xsl:include href="skins.xsl"/>
<xsl:include href="liens.xsl"/>
<xsl:include href="qdj.xsl"/>
<xsl:include href="anniversaires.xsl"/>
<xsl:include href="activites.xsl"/>
<xsl:include href="tours_kawa.xsl"/>
<xsl:include href="trombino.xsl"/>
<xsl:include href="stats.xsl"/>
<xsl:include href="meteo.xsl"/>

<xsl:template match="/">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
		<title><xsl:value-of select="frankiz/page/@titre"/></title>
		<base>
			<xsl:attribute name="href">
			<xsl:value-of select="frankiz/@base"/>
			</xsl:attribute>
		</base>
		<link rel="stylesheet" type="text/css">
			<xsl:attribute name="href">
			<xsl:value-of select="frankiz/@css"/>
			</xsl:attribute>
		</link>
		<xsl:apply-templates select="frankiz/module[@id='liste_css']" mode="css"/>
	</head>
	<body>
		<div class="fkz_entetes">
			<a href="index.php"><div class="fkz_logo"><span class="fkz_logo">Frankiz,</span></div></a>
			<div  class="fkz_logo_eleves"><span class="fkz_logo_eleves">le site Web des élèves</span></div>
		</div>
		<div class="fkz_page">
			<div class="fkz_centre">
				<xsl:apply-templates select="frankiz/module[@id='anniversaires']"/>
				<xsl:apply-templates select="frankiz/page[@id='accueil']" mode="sommaire"/>
				<xsl:apply-templates select="frankiz/page[@id='accueil']" mode="complet"/>
				<xsl:apply-templates select="frankiz/page[@id='trombino']"/>
				<xsl:apply-templates select="frankiz/page[@id!='accueil' and @id!='trombino']"/>
			</div>
			<div class="fkz_gauche">
				<xsl:apply-templates select="frankiz/module[@id='liens_navigation']"/>
				<xsl:apply-templates select="frankiz/module[@id='activites']"/>
				<xsl:apply-templates select="frankiz/module[@id='liens_contacts']"/>
				<xsl:apply-templates select="frankiz/module[@id='liens_ecole']"/>
				<xsl:apply-templates select="frankiz/module[@id='stats']"/>
			</div>
	
			<div class="fkz_droite">
				<xsl:apply-templates select="frankiz/module[@id='tour_kawa']"/>
				<xsl:apply-templates select="frankiz/module[@id='qdj']"/>
				<xsl:apply-templates select="frankiz/module[@id='qdj_hier']"/>
				<xsl:apply-templates select="frankiz/module[@id='meteo']"/>
				<xsl:if test="count(frankiz/module[@id='qdj']|frankiz/module[@id='qdj_hier']|frankiz/module[@id='tour_kawa'])">
					<p class="valid">
						<a href="http://validator.w3.org/check?uri=referer">
							<span class="valid_xhtml"><xsl:text> </xsl:text></span>
						</a>
						<span class="valid_css"><xsl:text> </xsl:text></span>
					</p>
				</xsl:if>
			</div>
		</div>
		<div class="fkz_end_page"><xsl:text> </xsl:text></div>
	</body>
	</html>
</xsl:template>


<xsl:template match="/frankiz/page[@id!='annonces' and @id!='trombino']">
	<div class="fkz_page_divers">
		<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:apply-templates/>
	</div>
</xsl:template>

<xsl:template match="formulaire[starts-with(@id,'mod_xnet_')]">
	<xsl:apply-templates select="commentaire | warning | note"/>
	<form enctype="multipart/form-data" method="post">
			<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<xsl:if test="boolean(@titre)">
			<h2><span>Modification du mot de passe Xnet </span><span class="adresse_ip"><xsl:value-of select="substring-after(@titre,'Modification du mot de passe Xnet')"/></span></h2>
		</xsl:if>
	<div class="formulaire">
			<!-- les options du formulaire -->
			<xsl:for-each select="champ|choix|zonetext|textsimple|hidden|image|fichier|lien">
				<div>
				<xsl:if test="boolean(@titre)">
					<span class="gauche">
						<xsl:value-of select="@titre"/> :
					</span>
				</xsl:if>
				<span class="droite">
					<xsl:apply-templates select="."/>
				</span>
				</div>
			</xsl:for-each>
			<!-- les boutons gérant les actions du formulaire -->
			<div>
			<span class="boutons">
				<xsl:apply-templates select="bouton"/>
			</span>
			</div>
		</div>
	</form>
</xsl:template>

<xsl:template match="/frankiz/page[@id='meteo']">
	<div class="fkz_page_meteo">
		<xsl:apply-templates/>
	</div>
</xsl:template>


<xsl:template match="cadre">
	<h2><xsl:value-of select="@titre"/></h2>
	<xsl:if test="count(image)"><div style="text-align: center"><xsl:apply-templates select="image"/></div></xsl:if>
	<xsl:apply-templates/>
</xsl:template>



<!-- Arbres -->
<xsl:template match="arbre">
	<xsl:if test="boolean(@titre)"><h2><xsl:value-of select="@titre"/></h2></xsl:if>
	<ul>
		<xsl:apply-templates select="noeud"/>
	</ul>
</xsl:template>

<xsl:template match="noeud">
	<li>
		<xsl:choose><xsl:when test="count(noeud|feuille)">
			<xsl:attribute name="class">noeud_ouvert</xsl:attribute>
		</xsl:when><xsl:otherwise>
			<xsl:attribute name="class">noeud_ferme</xsl:attribute>
		</xsl:otherwise></xsl:choose>
		
		<xsl:choose><xsl:when test="boolean(@lien)">
			<a><xsl:attribute name="href"><xsl:value-of select="@lien"/></xsl:attribute>
				<xsl:value-of select="@titre"/>
			</a>
		</xsl:when><xsl:otherwise>
			<xsl:value-of select="@titre"/>
		</xsl:otherwise></xsl:choose>
		
		<xsl:apply-templates select="*[name()!='noeud' and  name()!= 'feuille']"/>
		
		<xsl:if test="count(noeud|feuille)">
			<ul class="feuille">
				<xsl:apply-templates select="noeud|feuille"/>
			</ul>
		</xsl:if>
	</li>
</xsl:template>

<xsl:template match="feuille">
	<li>
		<xsl:choose><xsl:when test="boolean(@lien)">
			<a><xsl:attribute name="href"><xsl:value-of select="@lien"/></xsl:attribute>
				<xsl:value-of select="@titre"/>
			</a>
		</xsl:when><xsl:otherwise>
			<xsl:value-of select="@titre"/>
		</xsl:otherwise></xsl:choose>
		<xsl:apply-templates />
	</li>
</xsl:template>

<!-- Eleves pour les anniversaires/signatures/qdj 
<xsl:template match="eleve">
	<xsl:choose><xsl:when test="@surnom != ''">
		<xsl:value-of select="@surnom"/>
	</xsl:when><xsl:otherwise>
		<xsl:value-of select="@prenom"/><xsl:text> </xsl:text><xsl:value-of select="@nom"/>
	</xsl:otherwise></xsl:choose>
	<xsl:if test="@promo != ''">
		<xsl:text> (</xsl:text><xsl:value-of select="@promo"/><xsl:text>)</xsl:text>
	</xsl:if>
	<br/>
</xsl:template>
-->

<!-- Page des binets -->

<xsl:template match="page/binet">
	<xsl:if test="preceding-sibling::binet[1]/@categorie != @categorie or position() = 2">
		<h2><xsl:value-of select="@categorie"/></h2>
	</xsl:if>
	
	<a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:apply-templates select="image"/>
	</a>
		<h3><xsl:value-of select="@nom"/></h3>
		<p><xsl:value-of select="description"/></p>
</xsl:template>

</xsl:stylesheet>
