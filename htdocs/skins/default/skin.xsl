<?xml version="1.0" encoding="ISO-8859-1" ?>
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
	
	$Log$
	Revision 1.5  2004/11/04 15:18:01  psycow
	Un bon debut mais plus compatible IE j'en ai peur

	Revision 1.4  2004/11/03 23:38:39  psycow
	Un bon début
	
	Revision 1.3  2004/11/03 21:23:03  psycow
	auvegarde de mon debut dans les xsl
	
	Revision 1.1  2004/11/03 18:21:32  psycow
	*** empty log message ***
	
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

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

<xsl:output method="xml" indent="yes" encoding="ISO-8859-1"
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>

<xsl:template match="/frankiz"> 
	<xsl:apply-templates select="page"/>
</xsl:template>

<!-- Définition d'une page web de frankiz -->
<xsl:template match="/frankiz/page">
	
	<html>
	<head>
		<title><xsl:value-of select="@titre"/></title>
		<!-- semble ne pas marcher avec certains navigateurs lorsque la base est relative -->
		<base>
			<xsl:attribute name="href">
				<xsl:value-of select="../@base"/>
			</xsl:attribute>
		</base>
		<link rel="stylesheet" type="text/css" href="skins/default/style.css" media="screen" />
		<xsl:apply-templates select="/frankiz/module[@id='liste_css']" mode="css"/>
	</head>

	<body>
		<div id="conteneur">
			<div id="header">
				<h1><a href="" title="Accueil - Page des Eleves de l'X"><span>Serveur des Elèves</span></a></h1>
				<h2><span>200 ans de Bob et de Pales</span></h2>
				<h3><a href="http://www.polytechnique.fr" title="Ecole Polytechnique"><span> Site de l'Ecole Polytechnique</span></a></h3>
			</div>
		
			<xsl:apply-templates select="/frankiz/module[@id='liens_navigation']" />
	
			<div id="droite">
				<xsl:for-each select="/frankiz/module[@id!='liens_navigation' and @id!='anniversaires']">
					<xsl:if test="(boolean(@visible) = false) or (@visible = 'true')">
						<dl>
							<dt><img class="droitehaut" src="skins/default/images/cadre-hautd.gif" alt="" />
								<span><xsl:value-of select="@titre"/></span>	
							</dt>
							<dd>
								<xsl:choose>
									<xsl:when test="@id='liens_contacts'">
										<xsl:apply-templates select="/frankiz/module[@id='liens_contacts']"/>
									</xsl:when>
									<xsl:when test="@id='liens_ecole'">
										<xsl:apply-templates select="/frankiz/module[@id='liens_ecole']"/>
									</xsl:when>
									<xsl:when test="@id='qdj'">
										<xsl:apply-templates select="/frankiz/module[@id='qdj']"/>
									</xsl:when>
									<xsl:when test="@id='qdj_hier'">
										<xsl:apply-templates select="/frankiz/module[@id='qdj_hier']"/>
									</xsl:when>
									<xsl:when test="@id='meteo'">
										<xsl:apply-templates select="/frankiz/module[@id='meteo']"/>
									</xsl:when>
									<xsl:when test="@id='tour_kawa'">
										<xsl:apply-templates select="/frankiz/module[@id='tour_kawa']"/>
									</xsl:when>
									<xsl:when test="@id='stats'">
										<xsl:apply-templates select="/frankiz/module[@id='stats']"/>
									</xsl:when>
									<xsl:otherwise>
										<!-- normalement il n'y a pas d'autres modules -->
									</xsl:otherwise>
								</xsl:choose>
							</dd>
							<dd class="bas"><img class="droitebas" src="skins/default/images/cadre-basd.gif" alt="" /></dd>
						</dl>
						<br />
						<br />
					</xsl:if>
				</xsl:for-each>
			</div><!--fin #droite -->
			
			<div id="centre">
				<xsl:apply-templates select="/frankiz/module[@id='anniversaires']"/>
				<xsl:apply-templates />
			</div><!--fin #centre -->
			
			<div id="footer">
				<span id="bas_gauche"><b/></span>
				<span id="bas_droit"><b/></span>
				<h5><a href="" title="Retour en haut"><span>Retour en Haut</span></a></h5>
			</div>
		</div><!--fin #conteneur -->
		
	</body>
	</html>

</xsl:template>


</xsl:stylesheet>
