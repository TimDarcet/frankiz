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
<xsl:output method="xml" indent="yes" encoding="utf-8"
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>

<xsl:param name="user_nom"/>
<xsl:param name="user_prenom"/>
<xsl:param name="date"/>
<xsl:param name="heure"/>
<xsl:param name="sommaire"/>
<xsl:param name="trier_annonces"/>
<xsl:param name="blending"/>

<!-- a modifier -->
<xsl:include href="../../xhtml/xsl/html.xsl"/>
<xsl:include href="../../xhtml/xsl/form.xsl"/>
<xsl:include href="../../xhtml/xsl/arbre.xsl"/>
<xsl:include href="../../xhtml/xsl/annonces.xsl"/>
<xsl:include href="annonces.xsl"/>
<xsl:include href="../../xhtml/xsl/skins.xsl"/>
<xsl:include href="../../xhtml/xsl/liens.xsl"/>
<xsl:include href="../../xhtml/xsl/qdj.xsl"/>
<xsl:include href="../../xhtml/xsl/anniversaires.xsl"/>
<xsl:include href="../../xhtml/xsl/activites.xsl"/>
<xsl:include href="../../xhtml/xsl/tours_kawa.xsl"/>
<xsl:include href="../../xhtml/xsl/trombino.xsl"/>
<xsl:include href="../../xhtml/xsl/stats.xsl"/>
<xsl:include href="../../xhtml/xsl/meteo.xsl"/>

<xsl:template match="/">
	<html lang="fr" xml:lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<xsl:if test="$blending='on'">
			<meta http-equiv="Page-Exit" content="blendTrans(Duration=0.3)"/>
			<meta http-equiv="Page-Enter" content="blendTrans(Duration=0.3)"/>
		</xsl:if>
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
		<link rel="alternate" type="application/rss+xml" title="Version RSS" href="rss_annonces.php" />
		<link rel="glossary" title="Vocabulaire" href="vocabulaire.php" />
		<link rel="help" title="Contact" href="contact.php"/>
		<link rel="index" href="index.php"/>
		<link rel="start" href="index.php"/>
		<link href="mailto:web@fkz" rev="made" />
		<link rel="shortcut icon" href="favicon.ico" />
		<xsl:apply-templates select="frankiz/module[@id='liens_navigation']" mode="link"/>
		<xsl:apply-templates select="frankiz/module[@id='liens_perso']" mode="link"/>
	</head>
	<body>
        <xsl:attribute name="class">fond<xsl:value-of select="1 + (frankiz/module[@id='random']/p[1] mod 13)" /></xsl:attribute>
		<div class="fkz_entetes">
			<div class="fkz_logo"><a href="index.php"><span class="fkz_logo">Frankiz,</span></a></div>
			<div  class="fkz_logo_eleves"><span class="fkz_logo_eleves">le site Web des élèves de l'École Polytechnique</span></div>
		</div>
		<div class="fkz_page">
			<div class="fkz_centre">
				<xsl:apply-templates select="frankiz/module[@id='anniversaires']"/>
				<xsl:apply-templates select="frankiz/module[@id='virus']"/>
				<xsl:apply-templates select="frankiz/page[@id='annonces']" mode="sommaire"/>
				<xsl:apply-templates select="frankiz/page[@id='annonces']" mode="complet"/>
				<xsl:apply-templates select="frankiz/page[@id='trombino']"/>
				<xsl:apply-templates select="frankiz/page[@id!='annonces' and @id!='trombino']"/>
			</div>
			<div class="fkz_gauche">
				<xsl:apply-templates select="frankiz/module[@id='liens_navigation']"/>
				<xsl:apply-templates select="frankiz/module[@id='liens_profil']"/>
				<xsl:apply-templates select="frankiz/module[@id='liens_perso']"/>
				<xsl:apply-templates select="frankiz/module[@id='activites']"/>
				<xsl:apply-templates select="frankiz/module[@id='liens_contacts']"/>
				<xsl:apply-templates select="frankiz/module[@id='liens_ecole']"/>
				
				<xsl:apply-templates select="frankiz/module[@id='stats']"/>
			</div>
	
			<div class="fkz_droite">
				<xsl:apply-templates select="frankiz/module[@id='lienik']"/>
				<xsl:apply-templates select="frankiz/module[@id='lien_tol']"/>
				<xsl:apply-templates select="frankiz/module[@id='lien_wikix']"/>
				<xsl:apply-templates select="frankiz/module[@id='tour_kawa']"/>
				<xsl:apply-templates select="frankiz/module[@id='qdj']"/>
				<xsl:apply-templates select="frankiz/module[@id='qdj_hier']"/>
				<xsl:apply-templates select="frankiz/module[@id='meteo']"/>
				<xsl:apply-templates select="frankiz/module[@id!='tour_kawa' and @id!='qdj' and @id!='qdj_hier' and @id!='meteo' and @id!='stats' and @id!='liens_ecole' and @id!='liens_contacts' and @id!='activites' and @id!='liens_navigation' and @id!='liens_profil' and @id!='liens_perso' and @id!='anniversaires' and @id!='liste_css' and @id!='lien_tol' and @id!='lien_wikix' and @id!='virus' and @id!='lienik']"/>
				<xsl:if test="count(frankiz/module[@id!='stats' and @id!='liens_ecole' and @id!='activites' and @id!='liens_navigation' and @id!='liens_contacts' and @id!='anniversaires' and @id!='liste_css'])">
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
        <xsl:if test="@id != ''">
            <div class="fkz_page_titre"><xsl:value-of select="substring-after(@itre,'Frankiz : ')"/></div>
        </xsl:if>
        <div class="fkz_page_corps"><xsl:apply-templates/></div>
    </div>
</xsl:template>

<xsl:template match="/frankiz/page[@id='meteo']">
    <div class="fkz_page_meteo">
        <div class="fkz_page_titre">Météo</div>
        <div class="fkz_page_corps"><xsl:apply-templates/></div>
    </div>
</xsl:template>

<xsl:template match="cadre">
    <h2>
        <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
        <xsl:value-of select="@titre"/>
    </h2>
    <xsl:apply-templates/>
</xsl:template>




<xsl:template match="page/binet">
    <xsl:if test="preceding-sibling::binet[1]/@categorie != @categorie or position() = 2">
        <h2><xsl:value-of select="@categorie"/></h2>
    </xsl:if>
    <h3><a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:value-of select="@nom"/></a></h3>
    <a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
        <xsl:apply-templates select="image"/>
    </a>
    <p><xsl:value-of select="description"/></p>
</xsl:template>






<xsl:template match="module[@id='rss']">
     <div class="fkz_module" id='mod_rss'>
        <div class="fkz_titre">
            <a>
                <xsl:attribute name="href"><xsl:value-of select="lien[position()=1]/@url"/></xsl:attribute>
                <xsl:value-of select="lien[position()=1]/@titre" />
            </a>
            <xsl:value-of select="lien[position()=1]"/>
        </div>
        <div class="fkz_module_corps">
            <dl class="fkz_rss">
                <xsl:for-each select="lien[position()>1]">
                    <dt class="fkz_rss">
                        <a>
                            <xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
                            <xsl:value-of select="@titre" />
                        </a>
                    </dt>
                    <dd>
                        <xsl:value-of select="current()" />
                    </dd>
                </xsl:for-each>
            </dl>
        </div>
    </div>
</xsl:template>

<xsl:template match="module">
    <xsl:if test="false = starts-with(@visible,'false')">
        <div class="fkz_module"><xsl:attribute name="id">mod_<xsl:value-of select="@id"/></xsl:attribute>
            <div class="fkz_titre">
                <span><xsl:attribute name="id"><xsl:value-of select="@id"/>_logo</xsl:attribute><xsl:text> </xsl:text></span>
                <span><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute><xsl:value-of select="@titre"/></span>
            </div>
            <div class="fkz_module_corps">
                <xsl:apply-templates/>
            </div>
        </div>
    </xsl:if>
</xsl:template>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             


</xsl:stylesheet>
