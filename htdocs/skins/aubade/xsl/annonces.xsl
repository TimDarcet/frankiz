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

<xsl:template match="page[@id='annonces']" mode="complet">
    <xsl:choose>
	<xsl:when test="count(annonce)">
	   <xsl:if test="$trier_annonces='pas_tri'">
		<xsl:apply-templates select="annonce" mode="complet">
                    <xsl:with-param name="n" select="1" />
                </xsl:apply-templates>
	   </xsl:if>
	   <xsl:if test="$trier_annonces!='pas_tri'"> 
	       <xsl:apply-templates select="annonce[@categorie='important']" mode="complet">
                     <xsl:with-param name="n" select="1" />
               </xsl:apply-templates>
                
		<xsl:apply-templates select="annonce[@categorie='nouveau']" mode="complet">
                     <xsl:with-param name="n" select="count(annonce[@categorie='important'])+1" />
                </xsl:apply-templates>
                
		<xsl:apply-templates select="annonce[@categorie='vieux']" mode="complet">
                     <xsl:with-param name="n" select="count(annonce[@categorie='important'])+count(annonce[@categorie='nouveau'])+1" />
                </xsl:apply-templates>
                
		<xsl:apply-templates select="annonce[@categorie='reste']" mode="complet">  
                     <xsl:with-param name="n" select="count(annonce[@categorie='important'])+count(annonce[@categorie='nouveau'])+count(annonce[@categorie='vieux'])+1" />
                </xsl:apply-templates>
			</xsl:if>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>


<xsl:template match="page[@id='annonces']" mode="sommaire">
	<xsl:if test="$sommaire='pas_tri'">
		<xsl:if test="count(annonce) != 0">
			<div class="fkz_sommaire">
                <div class="fkz_sommaire_haut"><xsl:text> </xsl:text></div>
				<xsl:apply-templates select="annonce" mode="sommaire"/>
				<xsl:apply-templates select="lien"/>
                <div class="fkz_sommaire_bas"><xsl:text> </xsl:text></div>
			</div>
			<br/>
		</xsl:if>
	</xsl:if>
	<xsl:if test="$trier_annonces!='pas_tri' and $sommaire!='cache'">
		<xsl:if test="count(annonce) != 0">
			<div class="fkz_sommaire">
                <div class="fkz_sommaire_bloc">
                    <xsl:if test="count(annonce[@categorie='important']) != 0">
                        <div class="fkz_sommaire_titre">
                        <span class="fkz_annonces_important"><xsl:text> </xsl:text></span> Important
                        </div>
                        <xsl:apply-templates select="annonce[@categorie='important']" mode="sommaire"/>
                    </xsl:if>
                    <xsl:if test="count(annonce[@categorie='nouveau']) != 0">
                        <div class="fkz_sommaire_titre"><span class="fkz_annonces_nouveau"><xsl:text> </xsl:text></span> Nouvelles Fraîches</div>
                        <xsl:apply-templates select="annonce[@categorie='nouveau']" mode="sommaire"/>
                    </xsl:if>
                    <xsl:if test="count(annonce[@categorie='vieux']) != 0">
                        <div class="fkz_sommaire_titre"><span class="fkz_annonces_vieux"><xsl:text> </xsl:text></span> Demain c'est fini</div>
                        <xsl:apply-templates select="annonce[@categorie='vieux']" mode="sommaire"/>
                    </xsl:if>
                    <xsl:if test="count(annonce[@categorie='reste']) != 0">
                        <div class="fkz_sommaire_titre"><span class="fkz_annonces_reste"><xsl:text> </xsl:text></span> En attendant...</div>
                        <xsl:apply-templates select="annonce[@categorie='reste']" mode="sommaire"/>
                    </xsl:if>
                    <xsl:apply-templates select="lien"/>
                </div>
			</div>
			<br/>
		</xsl:if>
	</xsl:if>
</xsl:template>

<xsl:template match="annonce" mode="complet">
	<xsl:param name="n" />
         
	<a> <xsl:attribute name="name">	<xsl:value-of select="concat('annonce_',@id)"/> </xsl:attribute> </a>

	<xsl:if test="@visible!='non'">
	<div class="fkz_annonces">
		<div class="fkz_annonces_titre">
            <span class="lecon">Le</span><span class="lecon_cedille">ç</span><span class="lecon">on n°<xsl:value-of select="$n + position() - 1" /></span><span class="lecon_cedille"> :</span>
			<b>
				<span>
					<xsl:attribute name="class">fkz_annonces_<xsl:value-of select="@categorie"/></xsl:attribute>
					<xsl:text> </xsl:text>
				</span>
				<span class="fkz_annonces_cat">(<xsl:value-of select="@categorie"/>)</span>
				<xsl:text> </xsl:text>
				<span class="fkz_annonces_titre"><xsl:value-of select="@titre"/></span>
			</b>
		</div>
		<div class="fkz_annonces_corps">
			<xsl:apply-templates/>

		</div>
	</div>
        </xsl:if>
</xsl:template>


<xsl:template match="annonce" mode="sommaire">
	<div class="fkz_sommaire_corps">
		<a>
			<xsl:attribute name="href">
				<xsl:if test="@visible='non'">
					<xsl:text>?nonlu=</xsl:text><xsl:value-of select="@id"/>
				</xsl:if>
				<xsl:text>#</xsl:text><xsl:value-of select="concat('annonce_',@id)"/>
			</xsl:attribute>
			<xsl:value-of select="@titre"/>
		</a>
	</div>
</xsl:template>


<xsl:template match="eleve[name(..)='annonce']">
	<p class="fkz_signature">
		<xsl:choose>
			<xsl:when test="@lien='oui'">
				<a>
					<xsl:attribute name="href">
						<xsl:text>trombino.php?chercher=&amp;loginpoly=</xsl:text>
						<xsl:value-of select="@login"/>
					</xsl:attribute>
					<xsl:choose>
						<xsl:when test="@surnom != ''">
							<xsl:value-of select="@surnom"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="@prenom"/><xsl:text>  </xsl:text><xsl:value-of select="@nom"/>
						</xsl:otherwise>
					</xsl:choose>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<xsl:choose>
					<xsl:when test="@surnom != ''">
						<xsl:value-of select="@surnom"/>
			                </xsl:when>
			                <xsl:otherwise>
				                <xsl:value-of select="@prenom"/><xsl:text>  </xsl:text><xsl:value-of select="@nom"/>
				        </xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test="@promo!=''">(<xsl:value-of select="@promo"/>)</xsl:if>
	</p>
</xsl:template>

</xsl:stylesheet>
