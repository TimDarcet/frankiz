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

<xsl:template match="skins">
<form method="GET" accept-charset="utf-8" action="profil/skin.php">
    <div style="text-align: center"><b><xsl:text>Quelle skin voulez-vous pour le site ?</xsl:text></b></div>
    <xsl:for-each select="skin">
      <input type="radio" name="newskin">
        <xsl:attribute name="value">
          <xsl:value-of select="nom"/>
	</xsl:attribute>
        <xsl:if test="contains(@actif,'true')">
           <xsl:attribute name="checked">true</xsl:attribute>
        </xsl:if>	
      </input>
	<xsl:value-of select="nom"/>
      <xsl:text>  </xsl:text>
      <xsl:value-of select="description"/>
      <xsl:if test="position() != last()">
        <br/>
      </xsl:if>
    </xsl:for-each>
<br/>
<br/>Afficher les annonces 	
	<xsl:if test="contains(@annonces_complet,'false')">
        <select name="annonces_complet">
        <option value="0" selected="true">Non</option>
      	<option value="1">Non triées</option>
      	<option value="2">Triées</option>
      	</select>
      	</xsl:if>
      	<xsl:if test="contains(@annonces_complet,'pas_tri')">
        <select name="annonces_complet">
        <option value="0">Non</option>
      	<option value="1" selected="true">Non triées</option>
      	<option value="2">Triées</option>
      	</select>
      	</xsl:if>
      	<xsl:if test="contains(@annonces_complet,'trie')">
        <select name="annonces_complet">
        <option value="0">Non</option>
      	<option value="1">Non triées</option>
      	<option value="2" selected="true">Triées</option>
      	</select>
      	</xsl:if>
<br/>Afficher le sommaire des annonces	<xsl:if test="contains(@annonces_sommaire,'false')">
        <select name="annonces_sommaire">
        <option value="0" selected="true">Non</option>
      	<option value="1">Non triées</option>
      	<option value="2">Triées</option>
      	</select>
      	</xsl:if>
      	<xsl:if test="contains(@annonces_sommaire,'pas_tri')">
        <select name="annonces_sommaire">
        <option value="0">Non</option>
      	<option value="1" selected="true">Non triées</option>
      	<option value="2">Triées</option>
      	</select>
      	</xsl:if>
      	<xsl:if test="contains(@annonces_sommaire,'trie')">
        <select name="annonces_sommaire">
        <option value="0">Non</option>
      	<option value="1">Non triées</option>
      	<option value="2" selected="true">Triées</option>
      	</select>
      	</xsl:if>
<br/><input type="checkbox" value= "1" name="activites">
        <xsl:if test="contains(@activites,'true')">
        <xsl:attribute name="checked">true</xsl:attribute>
        </xsl:if>
	Afficher les activites
</input>
<br/><input type="checkbox" value= "1" name="qdj">
        <xsl:if test="contains(@qdj,'true')">
        <xsl:attribute name="checked">true</xsl:attribute>
        </xsl:if>
        Afficher la QDJ
        </input>
<br/><input type="checkbox" value= "1" name="qdj_hier">
        <xsl:if test="contains(@qdj_hier,'true')">
        <xsl:attribute name="checked">true</xsl:attribute>
        </xsl:if>
        Afficher les résultats de la QDJ d'hier
        </input>
<br/><input type="checkbox" value= "1" name="anniversaires">
        <xsl:if test="contains(@anniversaires,'true')">
        <xsl:attribute name="checked">true</xsl:attribute>
        </xsl:if>
        Afficher les anniversaires
        </input>
<br/><input type="checkbox" value= "1" name="contacts">
        <xsl:if test="contains(@contacts,'true')">
        <xsl:attribute name="checked">true</xsl:attribute>
        </xsl:if>
        Afficher les contacts
        </input>
<br/><input type="checkbox" value= "1" name="liens_br">
        <xsl:if test="contains(@liens_br,'true')">
        <xsl:attribute name="checked">true</xsl:attribute>
        </xsl:if>
        Afficher les liens du BR (ne PAS décocher)
        </input>
<br/><input type="checkbox" value= "1" name="liens_ecole">
        <xsl:if test="contains(@liens_ecole,'true')">
        <xsl:attribute name="checked">true</xsl:attribute>
        </xsl:if>
        Afficher les liens de l'école
        </input>
<br/><input type="checkbox" value= "1" name="tours_kawa">
        <xsl:if test="contains(@tours_kawa,'true')">
        <xsl:attribute name="checked">true</xsl:attribute>
        </xsl:if>
        Afficher les tours kawa
        </input>
<br/>
<br/>
<div style="text-align: center"><b><xsl:text>Quel CSS voulez-vous pour le site ?</xsl:text></b></div><br/>
<xsl:for-each select="css">
      <input type="radio" name="newcss">
        <xsl:attribute name="value">
          <xsl:value-of select="url"/>
	</xsl:attribute>
        <xsl:if test="contains(@actif,'true')">
           <xsl:attribute name="checked">true</xsl:attribute>
        </xsl:if>	
      </input>
	<xsl:value-of select="nom"/>
      <xsl:text>  </xsl:text>
      <xsl:value-of select="description"/>
      <xsl:if test="position() != last()">
        <br/>
      </xsl:if>
    </xsl:for-each>
<br/>CSS Perso: (donner une adresse web http ou ftp)<br/>
<input type="text" name="newcss_perso"/>
<br/>
<br/>
<input type="submit" value="OK"/>
</form>
</xsl:template>

<xsl:template match="module[@id='liste_css']" mode="css">
	<xsl:for-each select="lien">
		<link rel="alternate stylesheet" type="text/css">
			<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:value-of select="@titre"/></xsl:attribute>
		</link>
    </xsl:for-each>
</xsl:template>

</xsl:stylesheet>
