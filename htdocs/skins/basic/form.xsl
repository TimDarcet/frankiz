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
	Revision 1.27  2004/12/14 22:16:06  schmurtz
	Correction de bug du moteur wiki.
	Simplication du code.

	Revision 1.26  2004/11/29 17:27:33  schmurtz
	Modifications esthetiques.
	Nettoyage de vielles balises qui trainaient.
	
	Revision 1.25  2004/11/24 13:05:23  schmurtz
	Ajout d'un attribut type='discret' pour les liste et formulaire, afin d'avoir
	une presentation par defaut sans gros cadres autour.
	
	Revision 1.24  2004/11/23 23:30:20  schmurtz
	Modification de la balise textarea pour corriger un bug
	(return fantomes)
	
	Revision 1.23  2004/11/16 18:32:34  schmurtz
	Petits problemes d'interpretation de <note> et <commentaire>
	
	Revision 1.22  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.21  2004/10/21 21:57:07  schmurtz
	Petites modifs sur les skins
	
	Revision 1.20  2004/10/21 08:33:07  pico
	Chgts divers pour matcher avec la balise <html>
	
	Revision 1.19  2004/10/20 23:45:48  schmurtz
	<br/> ==> <br /> pour compatibilite avec IE
	
	Revision 1.18  2004/10/20 20:16:00  schmurtz
	Correction d'un bug de la skin basic : dans une liste, les boutons/champs
	s'affichaient dans la mauvaise colonne.
	
	Revision 1.17  2004/10/20 19:58:02  pico
	Changement skin pico -> valide html strict
	Changement des balises qui étaient pas valides
	
	Revision 1.16  2004/10/19 18:16:24  kikx
	hum
	
	Revision 1.15  2004/10/19 14:58:43  schmurtz
	Creation d'un champ de formulaire specifique pour les fichiers (sans passer
	l'element champ, qui actuellement est un peu acrobatique).
	
	Revision 1.14  2004/10/16 00:30:56  kikx
	Permet de modifier des binets déjà existants
	
	Revision 1.13  2004/10/10 22:31:41  kikx
	Voilà ... Maintenant le webmestre prut ou non valider des activité visibles de l'exterieur
	
	Revision 1.12  2004/10/04 21:48:54  kikx
	Modification du champs fichier pour uploader des fichiers
	
	Revision 1.11  2004/09/17 22:49:29  kikx
	Rajout de ce qui faut pour pouvoir faire des telechargeement de fichiers via des formulaires
	(ie des champs 'file' des champ 'hidden') de plus maintenant le formulaire sont en
	enctype="multipart/form-data" car sinon il parait que ca marche pas !
	
	Revision 1.10  2004/09/17 09:05:32  kikx
	La personne peut maintenant rajouter une annonce
	Ceci dit je ne comprend pas trop comment on protège les champs avec les <!CDATA
	-> j'ai laisser ca comme ca mais faudra modifier
	
	Revision 1.9  2004/09/15 23:19:56  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- Formulaires -->
<xsl:template match="formulaire[@type='discret']">
	<!-- le formulaire lui même, mis en page avec une table -->
	<form enctype="multipart/form-data" method="post">
		<xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<!-- les options du formulaire -->
		<xsl:for-each select="*[not (self::bouton or self::commentaire or self::warning)]">
			<xsl:apply-templates select="."/>
		</xsl:for-each>
		<!-- les boutons gérant les actions du formulaire -->
		<xsl:apply-templates select="bouton"/>
	</form>
</xsl:template>

<xsl:template match="formulaire">
	<!-- la déco -->
	<xsl:if test="boolean(@titre)">
		<h2><xsl:value-of select="@titre"/></h2>
	</xsl:if>

	<!-- le formulaire lui même, mis en page avec une table -->
	<form enctype="multipart/form-data" method="post">
			<xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<table class="formulaire" cellspacing="0" cellpadding="0">
			<!-- le titre du formulaire -->
			<xsl:if test="boolean(@titre)">
				<tr><td class="titre" colspan="2"><xsl:value-of select="@titre"/></td></tr>
			</xsl:if>
			<!-- les options et notes du formulaire -->
			<xsl:for-each select="*[not (self::bouton)]">
				<xsl:choose><xsl:when test="self::note">
					<tr><td colspan="2"><xsl:apply-templates select="."/></td></tr>
				</xsl:when><xsl:otherwise>
					<tr><td class="gauche">
						<xsl:if test="boolean(@titre)"><xsl:value-of select="@titre"/> :</xsl:if>
					</td><td class="droite">
						<xsl:apply-templates select="."/>
					</td></tr>
				</xsl:otherwise></xsl:choose>
			</xsl:for-each>
			<!-- les boutons gérant les actions du formulaire -->
			<tr><td class="boutons" colspan="2">
				<xsl:apply-templates select="bouton"/>
			</td></tr>
		</table>
	</form>
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
			<xsl:attribute name="rows">
				<xsl:choose><xsl:when test="@type='grand'">30</xsl:when><xsl:otherwise>7</xsl:otherwise></xsl:choose>
			</xsl:attribute>
			<xsl:attribute name="cols">50</xsl:attribute>
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
	<select><xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
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
			<xsl:attribute name="name"><xsl:value-of select="../@id"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:if test="../@valeur = @id"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
		</input>
		<xsl:value-of select="@titre"/><br />
	</xsl:for-each>
</xsl:template>

<xsl:template match="choix[@type='checkbox']">
	<xsl:for-each select="option">
		<input type="checkbox">
			<xsl:if test="@modifiable='non'"><xsl:attribute name="disabled"/></xsl:if>
			<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:if test="contains(../@valeur,@id)"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
		</input>
		<xsl:value-of select="@titre"/><br />
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