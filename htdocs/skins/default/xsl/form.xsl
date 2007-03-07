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
	Affichage des éléments de formulaire
	
	$Id$
	
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- Formulaires -->
<xsl:template match="formulaire">
	<!-- la déco -->
	<form enctype="multipart/form-data" method="post" class="formulaire" accept-charset="UTF-8">
			<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<fieldset>
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
			<xsl:for-each select="*[name()!='bouton' and name()!='hidden']">
 				<!--<xsl:choose>
					<xsl:when test="name()!='warning' and name()!='commentaire' and name()!='note' and name()!='notice'">
					 --><!--	<span class="droite">
							<xsl:apply-templates select="."/>
						</span>-->
 				<!--	</xsl:when> 
					<xsl:otherwise>
						<span class="gauche">
							<xsl:apply-templates select="."/>
						</span>
					</xsl:otherwise>
				</xsl:choose> -->
				<!--<xsl:if test="boolean(@titre)">
					<label class="gauche">
						<xsl:attribute name='for'><xsl:value-of select="concat(../@id,@id)"/></xsl:attribute>
						<xsl:value-of select="@titre"/> :
					</label>
				</xsl:if>-->
				
				<xsl:if test="boolean(@titre)">
					<p class="titre"><xsl:value-of select="@titre"/> :</p>
				</xsl:if>
				<xsl:choose>
					<xsl:when test="name()!='warning' and name()!='commentaire' and name()!='note' and name()!='notice'">
						<span class="objet">
							<xsl:apply-templates select="."/>
						</span>
					</xsl:when> 
					<xsl:otherwise>
							<xsl:apply-templates select="."/>
					</xsl:otherwise>
				</xsl:choose> 
				
			</xsl:for-each> 
			<p class="boutons">
				<xsl:apply-templates select="bouton | hidden"/>
			</p>
		</fieldset>
	</form>
</xsl:template>


<xsl:template match="commentaire">
	<p class="commentaire"><xsl:apply-templates/></p>
</xsl:template>

<xsl:template match="warning">
	<p class="warning"><xsl:apply-templates/></p>
</xsl:template>

<xsl:template match="note|notice">
	<p class="note"><xsl:apply-templates/></p>
</xsl:template>

<!--texte simple dans un formulaire -->
<xsl:template match="textsimple">
	<xsl:value-of select="@valeur"/>
</xsl:template>

<!-- champs contenant du texte -->
<xsl:template match="zonetext">
		<xsl:choose><xsl:when test="@modifiable='non'">
			<xsl:value-of select="@valeur"/>
		</xsl:when><xsl:otherwise>
			<textarea>
				<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
				
				<xsl:value-of select="text()"/>
			</textarea>
		</xsl:otherwise></xsl:choose>
</xsl:template>

<xsl:template match="champ">
		<xsl:choose><xsl:when test="@modifiable='non'">
			<xsl:value-of select="@valeur"/>
		</xsl:when><xsl:otherwise>
			<input>
				<xsl:choose>
					<xsl:when test="starts-with(@id,'passwd')"><xsl:attribute name="type">password</xsl:attribute></xsl:when>
					<xsl:otherwise><xsl:attribute name="type">text</xsl:attribute></xsl:otherwise>
				</xsl:choose>
				<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="@valeur"/></xsl:attribute>
			</input>
		</xsl:otherwise></xsl:choose>
</xsl:template>

<!-- choix multiples (radio, combo ou checkbox) -->
<xsl:template match="choix[@type='combo']">
		<select>
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
	<xsl:apply-templates mode='radio' />
</xsl:template>

<xsl:template match="option" mode='radio'>
	<label>
		<input type="radio">
			<xsl:attribute name="id"><xsl:value-of select="concat(ancestor::choix/@id,@id)"/></xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="ancestor::choix/@id"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:if test="ancestor::choix/@valeur = @id"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
		</input>
		<xsl:value-of select="@titre"/><br/>
	</label>
</xsl:template>

<xsl:template match="choix[@type='checkbox']">
	<xsl:apply-templates mode='checkbox' />
</xsl:template>

<xsl:template match="option" mode='checkbox'>
	<label>
		<input type="checkbox">
			<xsl:attribute name="id"><xsl:value-of select="concat(ancestor::choix/@id,@id)"/></xsl:attribute>
			<xsl:if test="@modifiable='non'"><xsl:attribute name="disabled"/></xsl:if>
			<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:if test="contains(ancestor::choix/@valeur,@id)"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
		</input>
		<xsl:value-of select="@titre"/><br/>
	</label>
</xsl:template>

<xsl:template match="table" mode="radio">
	<table border='1'><xsl:apply-templates mode="radio" /></table>
</xsl:template>

<xsl:template match="th" mode="radio">
	<th align='center'><xsl:apply-templates mode="radio" /></th>
</xsl:template>

<xsl:template match="tr" mode="radio">
	<tr><xsl:apply-templates mode="radio" /></tr>
</xsl:template>

<xsl:template match="td" mode="radio">
	<td align='center'><xsl:apply-templates mode="radio" /></td>
</xsl:template>

<xsl:template match="table" mode="checkbox">
	<table border='1'><xsl:apply-templates mode="checkbox" /></table>
</xsl:template>

<xsl:template match="th" mode="checkbox">
	<th align='center'><xsl:apply-templates mode="checkbox" /></th>
</xsl:template>

<xsl:template match="tr" mode="checkbox">
	<tr><xsl:apply-templates mode="checkbox" /></tr>
</xsl:template>

<xsl:template match="td" mode="checkbox">
	<td align='center'><xsl:apply-templates mode="checkbox" /></td>
</xsl:template>

<xsl:template match="table">
	<table border='1'><xsl:apply-templates /></table>
</xsl:template>

<xsl:template match="th">
	<th align='center'><xsl:apply-templates /></th>
</xsl:template>

<xsl:template match="tr">
	<tr><xsl:apply-templates /></tr>
</xsl:template>

<xsl:template match="td">
	<td align='center'><xsl:apply-templates /></td>
</xsl:template>


<!-- autres -->
<xsl:template match="fichier">
	<hidden id="MAX_FILE_SIZE">
		<xsl:attribute name="valeur"><xsl:value-of select="@taille"/></xsl:attribute>
	</hidden>
	<input type="file">
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
		<xsl:choose><xsl:when test="@onClick"><xsl:attribute name="onclick"><xsl:value-of select="@onClick"/></xsl:attribute></xsl:when></xsl:choose>
	</input>
</xsl:template>

</xsl:stylesheet>
