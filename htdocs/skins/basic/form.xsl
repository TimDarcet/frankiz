<?xml version="1.0" encoding="ISO-8859-1" ?>
<!--
	Affichage des �l�ments de formulaire
	
	$Log$
	Revision 1.14  2004/10/16 00:30:56  kikx
	Permet de modifier des binets d�j� existants

	Revision 1.13  2004/10/10 22:31:41  kikx
	Voil� ... Maintenant le webmestre prut ou non valider des activit� visibles de l'exterieur
	
	Revision 1.12  2004/10/04 21:48:54  kikx
	Modification du champs fichier pour uploader des fichiers
	
	Revision 1.11  2004/09/17 22:49:29  kikx
	Rajout de ce qui faut pour pouvoir faire des telechargeement de fichiers via des formulaires (ie des champs 'file' des champ 'hidden') de plus maintenant le formulaire sont en enctype="multipart/form-data" car sinon il parait que ca marche pas !
	
	Revision 1.10  2004/09/17 09:05:32  kikx
	La personne peut maintenant rajouter une annonce
	Ceci dit je ne comprend pas trop comment on prot�ge les champs avec les <!CDATA
	-> j'ai laisser ca comme ca mais faudra modifier
	
	Revision 1.9  2004/09/15 23:19:56  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- Formulaires -->
<xsl:template match="formulaire">
	<xsl:if test="boolean(@titre)">
		<h2><xsl:value-of select="@titre"/></h2>
	</xsl:if>
	<xsl:apply-templates select="commentaire"/>
	<form enctype="multipart/form-data" method="POST">
		<xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<table class="formulaire" cellspacing="0" cellpadding="0">
			<xsl:if test="boolean(@titre)">
				<tr><td class="titre" colspan="2"><xsl:value-of select="@titre"/></td></tr>
			</xsl:if>
			<xsl:apply-templates select="champ|choix|zonetext|textsimple|hidden|warning|image"/>
			<tr><td class="boutons" colspan="2"><center><xsl:apply-templates select="bouton"/></center></td></tr>
		</table>
	</form>
</xsl:template>

<xsl:template match="commentaire">
	<p class="commentaire"><xsl:apply-templates/></p>
</xsl:template>

<xsl:template match="warning">
	<p class="warning"><xsl:apply-templates/></p>
</xsl:template>

<xsl:template match="note">
	<p class="note"><xsl:apply-templates/></p>
</xsl:template>

<!--texte simple dans un formulaire -->
<xsl:template match="formulaire/textsimple">
	<tr>
		<td class="gauche"></td>
		<td class="droite">
			<xsl:value-of select="@valeur"/>
		</td>
	</tr>
</xsl:template>

<!-- champs contenant du texte -->
<xsl:template match="formulaire/zonetext">
	<tr><td class="gauche">
		<xsl:value-of select="@titre"/><xsl:text>�:</xsl:text>
	</td><td class="droite">
		<xsl:choose><xsl:when test="@modifiable='non'">
			<xsl:value-of select="@valeur"/>
		</xsl:when><xsl:otherwise>
			<textarea>
				<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:attribute name="rows">7</xsl:attribute>
				<xsl:attribute name="cols">50</xsl:attribute>
				<xsl:value-of select="@valeur"/>
			</textarea>
		</xsl:otherwise></xsl:choose>
	</td></tr>
</xsl:template>


<xsl:template match="formulaire/champ">
	<tr><td class="gauche">
		<xsl:value-of select="@titre"/><xsl:text>�:</xsl:text>
	</td><td class="droite">
		<xsl:choose><xsl:when test="@modifiable='non'">
			<xsl:value-of select="@valeur"/>
		</xsl:when><xsl:otherwise>
			<xsl:if test="boolean(@taille)">
				<hidden id="MAX_FILE_SIZE">
					<xsl:attribute name="valeur"><xsl:value-of select="@taille"/></xsl:attribute>
				</hidden>
			</xsl:if>
			<input>
				<xsl:choose>
					<xsl:when test="starts-with(@id,'passwd')"><xsl:attribute name="type">password</xsl:attribute></xsl:when>
					<xsl:when test="starts-with(@id,'file')"><xsl:attribute name="type">file</xsl:attribute></xsl:when>
					<xsl:otherwise><xsl:attribute name="type">text</xsl:attribute></xsl:otherwise>
				</xsl:choose>
				<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="@valeur"/></xsl:attribute>
			</input>
		</xsl:otherwise></xsl:choose>
	</td></tr>
</xsl:template>

<xsl:template match="formulaire/hidden">
	<tr><td class="gauche">
	</td><td class="droite">
		<input>
			<xsl:attribute name="type">hidden</xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="@valeur"/></xsl:attribute>
		</input>
	</td></tr>
</xsl:template>

<xsl:template match="zonetext">
	<textarea>
		<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:attribute name="ROWS">5</xsl:attribute>
		<xsl:attribute name="COLS">25</xsl:attribute>
		<xsl:value-of select="@valeur"/>
	</textarea>
</xsl:template>

<xsl:template match="champ">
	<input>
		<xsl:choose>
			<xsl:when test="starts-with(@id,'passwd')"><xsl:attribute name="type">password</xsl:attribute></xsl:when>
			<xsl:otherwise><xsl:attribute name="type">text</xsl:attribute></xsl:otherwise>
		</xsl:choose>
		<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@valeur"/></xsl:attribute>
	</input>
</xsl:template>

<!-- choix multiples (radio, combo ou checkbox) -->
<xsl:template match="choix[@type='combo']">
	<tr><td class="gauche">
		<xsl:value-of select="@titre"/><xsl:text>�:</xsl:text>
	</td><td class="droite">
		<select><xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:for-each select="option">
				<option>
					<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
					<xsl:if test="../@valeur = @id"><xsl:attribute name="selected"/></xsl:if>
					<xsl:value-of select="@titre"/>
				</option>
			</xsl:for-each>
		</select>
	</td></tr>
</xsl:template>

<xsl:template match="choix[@type='radio']">
	<tr><td class="gauche">
		<xsl:value-of select="@titre"/><xsl:text>�:</xsl:text>
	</td><td class="droite">
		<xsl:for-each select="option">
			<input type="radio">
				<xsl:attribute name="name"><xsl:value-of select="../@id"/></xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:if test="../@valeur = @id"><xsl:attribute name="checked"/></xsl:if>
				<xsl:value-of select="@titre"/><br/>
			</input>
		</xsl:for-each>
	</td></tr>
</xsl:template>

<xsl:template match="choix[@type='checkbox']">
	<tr><td class="gauche">
		<xsl:value-of select="@titre"/><xsl:text>�:</xsl:text>
	</td><td class="droite">
		<xsl:for-each select="option">
			<input type="checkbox">
				<xsl:if test="@modifiable='non'"><xsl:attribute name="disabled"/></xsl:if>
				<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
				<xsl:if test="contains(../@valeur,@id)"><xsl:attribute name="checked"/></xsl:if>
			</input>
			<xsl:value-of select="@titre"/><br/>
		</xsl:for-each>
	</td></tr>
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
		<xsl:when test="onClick"><xsl:attribute name="onClick"><xsl:value-of select="@onClick"/></xsl:attribute></xsl:when>
	</input>
</xsl:template>

</xsl:stylesheet>