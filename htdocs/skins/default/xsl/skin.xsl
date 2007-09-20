<?xml version="1.0" encoding="UTF-8" ?>
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
<!--
	Skin très simple utilisant des tables pour la disposition des éléments.
	Le but de cette skin est de ne pas se triturer les neurones pour faire
	une sortie html propre et skinnable quand on travail sur le code php.
	
	$Id$
	
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:param name="user_nom"/>
<xsl:param name="user_prenom"/>
<xsl:param name="date"/>
<xsl:param name="heure"/>


<xsl:include href="html.xsl"/>
<xsl:include href="form.xsl"/>

<xsl:include href="admin.xsl"/>
<xsl:include href="arbre.xsl"/>
<xsl:include href="liens.xsl"/>
<xsl:include href="meteo.xsl"/>
<xsl:include href="anniversaires.xsl"/>
<xsl:include href="qdj.xsl"/>
<xsl:include href="annonces.xsl"/>
<xsl:include href="tours_kawa.xsl"/>
<xsl:include href="trombino.xsl"/>
<xsl:include href="binets.xsl"/>
<xsl:include href="voca.xsl"/>
<xsl:include href="stats.xsl"/>
<xsl:include href="activites.xsl"/>
<xsl:include href="tol.xsl"/>
<xsl:include href="wikix.xsl"/>
<xsl:include href="faq_xshare.xsl"/>

<xsl:output method="xml" indent="yes" encoding="utf-8"
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>

<xsl:template match="/frankiz"> 
	
	<html>
	<head>
		<title><xsl:value-of select="@titre"/></title>
		<!-- semble ne pas marcher avec certains navigateurs lorsque la base est relative -->
		<base>
			<xsl:attribute name="href">
				<xsl:value-of select="@base"/>
			</xsl:attribute>
		</base>
		<link rel="stylesheet" type="text/css" media="screen"><xsl:attribute name="href"><xsl:value-of select="@css"/></xsl:attribute></link>
		<xsl:apply-templates select="module[@id='liste_css']" mode="css"/>
	</head>

	<body>
		<div id="conteneur">
			<div id="header">
				<h1><a href="" title="Accueil - Page des Eleves de l'X"><span>Frankiz</span></a></h1>
				<h2><span>Serveur des Eleves</span></h2>
				<h3><a href="http://www.polytechnique.fr" title="Ecole Polytechnique"><span> Site de l'Ecole Polytechnique</span></a></h3>
			</div>
			
			<div id="contenu">
			
				<div id="gauche">
					<xsl:apply-templates select="module[@id='liens_navigation']" />
					<xsl:apply-templates select="module[@id='meteo']"/>
					<xsl:apply-templates select="module[@id='lien_tol']"/>
					<xsl:apply-templates select="module[@id='lien_wikix']"/>
					<xsl:apply-templates select="module[@id='liens_perso']"/>
					<xsl:apply-templates select="module[@id='tour_kawa']"/>
					<xsl:apply-templates select="module[@id='activites']"/>	
					<xsl:apply-templates select="module[@id='sondages']"/>
				</div>
			
				<div id="droite">
					<xsl:apply-templates select="module[@id='lienik']"/>
					<xsl:apply-templates select="module[@id='qdj']"/>
					<xsl:apply-templates select="module[@id='qdj_hier']"/>
					<xsl:apply-templates select="module[@id='liens_profil']"/>
					<xsl:apply-templates select="module[@id='liens_contacts']"/>
					<xsl:apply-templates select="module[@id='liens_ecole']"/>
					<xsl:apply-templates select="module[@id='stats']"/>
					<xsl:apply-templates select="module[@id!='tour_kawa' and @id!='qdj' and @id!='qdj_hier' and @id!='meteo' and @id!='stats' and @id!='liens_ecole' and @id!='liens_contacts' and @id='liens_profil' and @id!='activites' and @id!='liens_navigation' and @id!='liens_perso' and @id!='anniversaires' and @id!='liste_css' and @id!='lien_tol' and @id!='lien_wikix' and @id='fetes']"/>
				</div><!--fin #droite -->
			
				<div id="centre">
					<xsl:apply-templates select="module[@id='virus']"/>
					<xsl:if test="/frankiz/page[@id='annonces' or @id='accueil']">
						<xsl:apply-templates select="module[@id='anniversaires']"/>
					</xsl:if>
					<xsl:apply-templates select="page[@id='annonces']" mode="sommaire"/>
					<xsl:apply-templates select="page[@id='annonces']" mode="complet"/>
					<xsl:apply-templates select="page[@id='trombino']"/>
					<xsl:apply-templates select="page[@id='faq']"/>
					<xsl:apply-templates select="page[@id='xshare']"/>
					<xsl:apply-templates select="page[@id='binets']"/>
					<xsl:apply-templates select="page[@id='vocabulaire']"/>
					<xsl:apply-templates select="page[@id='meteo']"/>
					<xsl:apply-templates select="page[@id!='annonces' and @id!='trombino' and @id!='faq' and @id!='xshare' and @id!='binets' and @id!='meteo' and @id!='vocabulaire']"/>
				</div><!--fin #centre -->

			
			
			<div id="footer">
				<span id="bas_gauche"><xsl:text> </xsl:text></span>
				<span id="bas_droit"><xsl:text> </xsl:text></span>
				<h5>
					<a href="#" title="Retour en haut">
						<span>Retour en Haut</span>
					</a>
				</h5>
			</div>
			</div><!--fin #contenu -->
		</div><!--fin #conteneur -->
		
	</body>
	</html>

</xsl:template>


<xsl:template match="page[@id!='annonces' and @id!='trombino' and @id!='binets' and @id!='meteo' and @id!='vocabulaire' ]"><!--and @id!='faq' and @id!='xshare'-->
	<dl class="boite">
		<dt class="titre">
			<span class="droitehaut"><xsl:text> </xsl:text></span>
			<span><xsl:value-of select="h1"/></span>	
		</dt>
		<dd  class="contenu">
			<xsl:apply-templates select="*[not(self::h1)]"/>
		</dd>
		<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
	</dl>
</xsl:template>

<xsl:template match="module">
	<xsl:if test="not (boolean(@visible))">
		<dl id="autres" class="boite">
			<dt class="titre">
				<span class="droitehaut"><xsl:text> </xsl:text></span>
				<span><xsl:value-of select="@titre"/></span>	
			</dt>
			<dd  class="contenu">
				<xsl:apply-templates/>
			</dd>
			<dd class="bas"><span class="droitebas"><xsl:text> </xsl:text></span></dd>
		</dl>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
