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

<!-- Formulaires -->
<xsl:template match="formulaire[@type='discret']">
	<!-- le formulaire lui même, mis en page avec une table -->
	<form enctype="multipart/form-data" method="post">
		<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<div class="formulaire">
		<!-- les options du formulaire -->
		<xsl:apply-templates select="*[name()!='bouton']"/>
		<!-- les boutons gérant les actions du formulaire -->
			<span class="boutons">
				<xsl:apply-templates select="bouton"/>
			</span>
		</div>
	</form>
</xsl:template>

<xsl:template match="formulaire">
	<!-- la déco -->
	<form enctype="multipart/form-data" method="post">
			<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<xsl:if test="boolean(@titre)">
			<xsl:choose>
				<xsl:when test="starts-with(@id,'mod_xnet_')">
					<h2><span>Modification du mot de passe Xnet </span><span class="adresse_ip"><xsl:value-of select="substring-after(@titre,'Modification du mot de passe Xnet')"/></span></h2>
				</xsl:when>
				<xsl:otherwise>
					<h2><span><xsl:value-of select="@titre"/></span></h2>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<div class="formulaire">
			<!-- les options du formulaire -->
			<xsl:for-each select="*[name()!='bouton']">
				<div>

				<span class="droite">
					<xsl:apply-templates select="."/>
				</span>
				<xsl:if test="boolean(@titre)">
					<label class="gauche" >
						<xsl:attribute name='for'><xsl:value-of select="concat(../@id,@id)"/></xsl:attribute>
						<xsl:value-of select="@titre"/> :
					</label>
				</xsl:if>
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

<xsl:template match="commentaire">
	<span class="commentaire"><xsl:apply-templates/></span>
</xsl:template>

<xsl:template match="warning">
	<span class="warning"><xsl:apply-templates/></span>
</xsl:template>

<xsl:template match="note">
	<span class="note"><xsl:apply-templates/></span>
</xsl:template>

<!--texte simple dans un formulaire -->
<xsl:template match="textsimple">
	<span>
		<xsl:attribute name="id"><xsl:value-of select="concat(../@id,@id)"/></xsl:attribute>
		<xsl:value-of select="@valeur"/>
	</span>
</xsl:template>

<!-- champs contenant du texte -->
<xsl:template match="zonetext">
	<xsl:choose>
	<xsl:when test="@modifiable='non'">
		<span>
			<xsl:attribute name="id"><xsl:value-of select="concat(../@id,@id)"/></xsl:attribute>
			<xsl:value-of select="@valeur"/>
		</span>
	</xsl:when>
	<xsl:otherwise>
		<textarea>
			<xsl:attribute name="id"><xsl:value-of select="concat(../@id,@id)"/></xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:attribute name="rows">
				<xsl:choose><xsl:when test="@type='grand'">30</xsl:when><xsl:otherwise>7</xsl:otherwise></xsl:choose>
			</xsl:attribute>
			<xsl:attribute name="cols">50</xsl:attribute>
			<xsl:value-of select="text()"/>
		</textarea>
	</xsl:otherwise></xsl:choose>
</xsl:template>

<xsl:template match="champ">
	<xsl:choose>
	<xsl:when test="@modifiable='non'">
		<span>
			<xsl:attribute name="id"><xsl:value-of select="concat(../@id,@id)"/></xsl:attribute>
			<xsl:value-of select="@valeur"/>
		</span>
	</xsl:when>
	<xsl:otherwise>
		<input>
			<xsl:choose>
				<xsl:when test="starts-with(@id,'passwd')"><xsl:attribute name="type">password</xsl:attribute></xsl:when>
				<xsl:otherwise><xsl:attribute name="type">text</xsl:attribute></xsl:otherwise>
			</xsl:choose>
			<xsl:attribute name="id"><xsl:value-of select="concat(../@id,@id)"/></xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="@valeur"/></xsl:attribute>
		</input>
	</xsl:otherwise></xsl:choose>
</xsl:template>

<!-- choix multiples (radio, combo ou checkbox) -->
<xsl:template match="choix[@type='combo']">
	<select>
		<xsl:attribute name="id"><xsl:value-of select="concat(../@id,@id)"/></xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:for-each select="option">
			<option>
				<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:if test="../@valeur = @id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
				<xsl:value-of select="@titre"/>
			</option>
		</xsl:for-each>
	</select>
</xsl:template>

<xsl:template match="choix[@type='radio']">
	<xsl:for-each select="option">
		<input type="radio">
			<xsl:attribute name="id"><xsl:value-of select="concat(../@id,@id)"/></xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="../@id"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:if test="../@valeur = @id"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
		</input>
		<xsl:value-of select="@titre"/><br/>
	</xsl:for-each>
</xsl:template>

<xsl:template match="choix[@type='checkbox']">
	<xsl:for-each select="option">
		<input type="checkbox">
			<xsl:attribute name="id"><xsl:value-of select="concat(../@id,@id)"/></xsl:attribute>
			<xsl:if test="@modifiable='non'"><xsl:attribute name="disabled"/></xsl:if>
			<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:if test="contains(../@valeur,@id)"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
		</input>
		<xsl:value-of select="@titre"/><br/>
	</xsl:for-each>
</xsl:template>

<!-- autres -->
<xsl:template match="fichier">
	<input type="hidden" id="MAX_FILE_SIZE">
		<xsl:attribute name="value"><xsl:value-of select="@taille"/></xsl:attribute>
	</input>
	<input type="file">
		<xsl:attribute name="id"><xsl:value-of select="concat(../@id,@id)"/></xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
	</input>
</xsl:template>

<xsl:template match="hidden">
	<input>
		<xsl:attribute name="type">hidden</xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@valeur"/></xsl:attribute>
	</input>
</xsl:template>

<!-- boutons -->
<xsl:template match="bouton[@id='reset']">
	<input type="reset">
		<xsl:attribute name="value"><xsl:value-of select="@titre"/></xsl:attribute>
	</input>
</xsl:template>

<xsl:template match="bouton[@type='detail']">
	<input type="image" SRC="skins/basic/detail.gif">
	<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
	<xsl:attribute name="value"></xsl:attribute>
	</input>
</xsl:template>

<xsl:template match="bouton">
	<input type="submit">
		<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@titre"/></xsl:attribute>
		<xsl:when test="@onClick"><xsl:attribute name="onclick"><xsl:value-of select="@onClick"/></xsl:attribute></xsl:when>
	</input>
</xsl:template>


</xsl:stylesheet>