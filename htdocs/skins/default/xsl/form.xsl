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
	Affichage des éléments de formulaire
	
	$Log$
	Revision 1.10  2005/01/25 12:50:42  pico
	Ajoute un accept-encoding dans les formulaires pour pas avoir de pbs de charset

	Revision 1.9  2005/01/23 20:56:11  pico
	Pour ajouter les hidden dans les formulaires, sinon y'a comportement quantique...
	
	(j'ai essayé d'appeler psycow pour le préviendre, mais il est pas là)
	
	Revision 1.8  2004/12/17 02:01:12  psycow
	Gros changement dans les Formulaires, mieux pour les sondages
	
	Revision 1.7  2004/12/15 00:38:30  psycow
	Commit du boulot de l'apres midi, a la place de l'EA
	
	Revision 1.5  2004/12/12 17:45:33  psycow
	Modification Mineure, pre-test IE: la dernière étape
	
	Revision 1.4  2004/12/10 14:31:28  psycow
	Elimination du Bug des Formulaires, etapes suivantes tests sous IE
	
	Revision 1.3  2004/12/10 03:04:31  psycow
	Resolution du probleme des boites sous Firefox, reste un probleme sur le positionnement des formulaires dans les boites...
	
	Revision 1.2  2004/12/09 14:00:07  psycow
	Bonne modification et resolutions des principaux problemes de versions... et oui je suis un boulet qui ecrase les bons fichier et mets les mauvais en commit...
	
	Revision 1.1  2004/11/24 20:26:40  schmurtz
	Reorganisation des skins (affichage melange skin/css + depacement des css)
	
	Revision 1.6  2004/11/23 23:32:22  schmurtz
	oubli
	
	Revision 1.5  2004/11/12 00:23:04  psycow
	Modification du trombi, gestion graphique des formulaires; c'est pas trop mal on touche a la fin
	
	Revision 1.4  2004/11/09 01:50:48  psycow
	Commit predodo, debut de modification des formulaires...
	
	Revision 1.3  2004/11/08 12:00:37  psycow
	Grosse Modification du WE
	
	Revision 1.2  2004/11/03 21:23:03  psycow
	auvegarde de mon debut dans les xsl
	
	Revision 1.1  2004/11/03 18:21:32  psycow
	*** empty log message ***
	
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- Formulaires -->
<xsl:template match="formulaire">
	<!-- la déco -->
	<form enctype="multipart/form-data" method="post" class="formulaire" accept-charset="ISO-8859-1">
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
		<xsl:for-each select="option">
			<label>
				<input type="radio">
					<xsl:attribute name="name"><xsl:value-of select="../@id"/></xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
					<xsl:if test="../@valeur = @id"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
				</input>
				<xsl:value-of select="@titre"/><br />
			</label>
		</xsl:for-each>
</xsl:template>

<xsl:template match="choix[@type='checkbox']">
		<xsl:for-each select="option">
			<label>
				<input type="checkbox">
					<xsl:if test="@modifiable='non'"><xsl:attribute name="disabled"/></xsl:if>
					<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
					<xsl:if test="contains(../@valeur,@id)"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
				</input>
				<xsl:value-of select="@titre"/><br />
			</label>
		</xsl:for-each>
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
		<xsl:when test="@onClick"><xsl:attribute name="onclick"><xsl:value-of select="@onClick"/></xsl:attribute></xsl:when>
	</input>
</xsl:template>

</xsl:stylesheet>