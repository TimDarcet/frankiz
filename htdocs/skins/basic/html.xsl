<?xml version="1.0" encoding="ISO-8859-1" ?>
<!--
	Balises de formatage.
	
	$Log$
	Revision 1.10  2004/10/20 18:47:07  kikx
	Pour rajouter des lignes non selectionnables dans une liste

	Revision 1.9  2004/10/18 19:14:27  pico
	Changement balises pour me conformer à la dtd
	
	Revision 1.8  2004/10/17 17:09:43  pico
	Gestion des listes
	Classes FAQ pour affichage graphique
	
	Revision 1.7  2004/09/20 22:19:28  kikx
	test
	
	Revision 1.6  2004/09/20 20:31:20  schmurtz
	Rajout de la balise html <em>
	
	Revision 1.5  2004/09/16 11:09:38  kikx
	C'est les vacances maintenant ...
	Bon bref .. c'est dur aussi
	Bon j'ai un peu arrangé la page des binets
	
	Revision 1.4  2004/09/15 23:19:56  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<!-- Images -->
<xsl:template match="image">
	<img class="image">
		<xsl:attribute name="src"><xsl:value-of select="@source"/></xsl:attribute>
		<xsl:attribute name="border">0</xsl:attribute>
	</img><br/>
	<xsl:if test="boolean(@legende)"><span class="legende"><xsl:value-of select="@legende"/></span><br/></xsl:if>
</xsl:template>


<!-- Liens -->
<xsl:template match="lien">
	<a class="lien"><xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
		<xsl:value-of select="@titre"/>
			<xsl:apply-templates/>
	</a><br/>
</xsl:template>


<!-- Listes -->
<xsl:template match="liste">
	<xsl:if test="boolean(@titre)">
		<h2><xsl:value-of select="@titre"/></h2>
	</xsl:if>
	<xsl:apply-templates select="commentaire"/>
	<form method="POST"><xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<xsl:variable name="nombre_colonnes" select="count(entete)+count(@selectionnable)"/>
		<table class="liste" cellspacing="0" cellpadding="0">
			<tr>
				<xsl:if test="@selectionnable='oui'"><td class="entete">&#160;</td></xsl:if>
				<xsl:apply-templates select="entete"/>
			</tr>
			<xsl:apply-templates select="element"/>
			<tr><td class="boutons"><xsl:attribute name="colspan"><xsl:value-of select="$nombre_colonnes"/></xsl:attribute>
				<xsl:apply-templates select="bouton"/>
			</td></tr>
		</table>
	</form>
</xsl:template>

<xsl:template match="liste/entete">
	<td class="entete">
		<xsl:choose><xsl:when test="boolean(@action)">
			<a><xsl:attribute name="href"><xsl:value-of select="@action"/></xsl:attribute>
				<xsl:value-of select="@titre"/>
			</a>
		</xsl:when><xsl:otherwise>
			<xsl:value-of select="@titre"/>
		</xsl:otherwise></xsl:choose>
	</td>
</xsl:template>

<xsl:template match="liste/element">
	<tr>
		<xsl:if test="../@selectionnable='oui'">
			<td class="element">
				<xsl:choose>
					<xsl:when test="not(boolean(@selectionnable))">
						<input type="checkbox">
							<xsl:attribute name="name">elements[<xsl:value-of select="@id"/>]</xsl:attribute>
						</input>
					</xsl:when>
					<xsl:when test="@selectionnable='oui'">
						<input type="checkbox">
							<xsl:attribute name="name">elements[<xsl:value-of select="@id"/>]</xsl:attribute>
						</input>
					</xsl:when>

				</xsl:choose>
			</td>
		</xsl:if>
		<xsl:for-each select="colonne">
			<td class="element"><xsl:apply-templates/></td>
		</xsl:for-each>
	</tr>
</xsl:template>

<!-- Arbres -->


<!-- Formatage HTML -->
<xsl:template match="p">
	<xsl:apply-templates/><br/>
</xsl:template>
<xsl:template match="h1">
	<h1><xsl:apply-templates/></h1>
</xsl:template>
<xsl:template match="h2">
	<h2><xsl:apply-templates/></h2>
</xsl:template>
<xsl:template match="h3">
	<h3><xsl:apply-templates/></h3>
</xsl:template>
<xsl:template match="h4">
	<h4><xsl:apply-templates/></h4>
</xsl:template>
<xsl:template match="h5">
	<h5><xsl:apply-templates/></h5>
</xsl:template>
<xsl:template match="strong">
	<strong><xsl:apply-templates/></strong>
</xsl:template>
<xsl:template match="em">
	<em><xsl:apply-templates/></em>
</xsl:template>
<xsl:template match="code">
	<code><xsl:apply-templates/></code>
</xsl:template>
<xsl:template match="noeud">
	<ul><xsl:attribute name="class"><xsl:value-of select="@class"/></xsl:attribute><xsl:apply-templates/></ul>
</xsl:template>
<xsl:template match="feuille">
	<li><xsl:attribute name="class"><xsl:value-of select="@class"/></xsl:attribute><xsl:apply-templates/></li>
</xsl:template>
<xsl:template match="a">
	<a><xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="@href"/></xsl:attribute><xsl:apply-templates/></a>
</xsl:template>


</xsl:stylesheet>
