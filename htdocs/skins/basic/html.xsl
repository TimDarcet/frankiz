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
	Balises de formatage.
	
	$Log$
	Revision 1.19  2004/10/29 16:42:31  kikx
	Rajout des id sur les liens de navigation
	Ca pêrmet au skinneur soit de mettre en gras certain liens specifique soit de remplacer les liens par des images comme il le souhaite

	Revision 1.18  2004/10/21 22:43:11  kikx
	Bug fix et mise en place de la possibilité de modifier la photo du trombino
	
	Revision 1.17  2004/10/21 22:19:38  schmurtz
	GPLisation des fichiers du site
	
	Revision 1.16  2004/10/21 21:57:07  schmurtz
	Petites modifs sur les skins
	
	Revision 1.15  2004/10/20 23:45:48  schmurtz
	<br/> ==> <br /> pour compatibilite avec IE
	
	Revision 1.14  2004/10/20 23:21:39  schmurtz
	Creation d'un element <html> qui permet d'afficher du html brute sans verification
	C'est ce qui est maintenant utilise dans les annonces/cadres
	
	Revision 1.13  2004/10/20 22:10:57  schmurtz
	Correction du bug qui affichait deux images dans la faq
	
	Revision 1.12  2004/10/20 22:05:36  schmurtz
	Juste pour pico, qu'il puisse y voir quelque chose
	
	Revision 1.11  2004/10/20 19:58:02  pico
	Changement skin pico -> valide html strict
	Changement des balises qui étaient pas valides
	
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
	<img class="image" style="border:0">
		<xsl:attribute name="src"><xsl:value-of select="@source"/></xsl:attribute>
		<xsl:attribute name="alt"><xsl:value-of select="@legende"/></xsl:attribute>
		<xsl:if test="boolean(@height)"><xsl:attribute name="height"><xsl:value-of select="@height"/></xsl:attribute></xsl:if>
		<xsl:if test="boolean(@width)"><xsl:attribute name="width"><xsl:value-of select="@width"/></xsl:attribute></xsl:if>
	</img><br />
	<xsl:if test="boolean(@legende)"><span class="legende"><xsl:value-of select="@legende"/></span><br /></xsl:if>
</xsl:template>


<!-- Liens -->
<xsl:template match="module/lien">
	<xsl:choose><xsl:when test="boolean(@id)">
		<xsl:choose><xsl:when test="@id='connect'">
			<strong><a class="lien"><xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
			!<xsl:value-of select="@titre"/>!<xsl:apply-templates/>
			</a></strong><br />
		</xsl:when><xsl:otherwise>
			<a class="lien"><xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
			<xsl:value-of select="@titre"/><xsl:apply-templates/>
			</a><br />
		</xsl:otherwise></xsl:choose>
	</xsl:when><xsl:otherwise>
		<a class="lien"><xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
		<xsl:value-of select="@titre"/><xsl:apply-templates/>
		</a><br />
	</xsl:otherwise></xsl:choose>


</xsl:template>

<xsl:template match="lien">
	<a class="lien"><xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
		<xsl:value-of select="@titre"/><xsl:apply-templates/>
	</a><br />
</xsl:template>


<!-- Listes -->
<xsl:template match="liste">
	<xsl:if test="boolean(@titre)">
		<h2><xsl:value-of select="@titre"/></h2>
	</xsl:if>
	<xsl:apply-templates select="commentaire"/>
	<form method="post"><xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
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
<xsl:template match="arbre">
	<xsl:if test="boolean(@titre)"><h2><xsl:value-of select="@titre"/></h2></xsl:if>
	<ul><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:apply-templates select="noeud"/>
	</ul>
</xsl:template>

<xsl:template match="noeud">
	<li>
		<xsl:choose><xsl:when test="count(noeud|feuille)">
			<xsl:attribute name="class">noeud_ouvert</xsl:attribute>
		</xsl:when><xsl:otherwise>
			<xsl:attribute name="class">noeud_ferme</xsl:attribute>
		</xsl:otherwise></xsl:choose>
		
		<xsl:choose><xsl:when test="boolean(@lien)">
			<a><xsl:attribute name="href"><xsl:value-of select="@lien"/></xsl:attribute>
				<xsl:value-of select="@titre"/>
			</a>
		</xsl:when><xsl:otherwise>
			<xsl:value-of select="@titre"/>
		</xsl:otherwise></xsl:choose>
		
		<xsl:if test="count(noeud|feuille)">
			<ul class="feuille">
				<xsl:apply-templates select="noeud|feuille"/>
			</ul>
		</xsl:if>
	</li>
</xsl:template>

<xsl:template match="feuille">
	<li>
		<xsl:choose><xsl:when test="boolean(@lien)">
			<a><xsl:attribute name="href"><xsl:value-of select="@lien"/></xsl:attribute>
				<xsl:value-of select="@titre"/>
			</a>
		</xsl:when><xsl:otherwise>
			<xsl:value-of select="@titre"/>
		</xsl:otherwise></xsl:choose>
	</li>
</xsl:template>

<!-- Formatage HTML -->
<xsl:template match="p">
	<xsl:apply-templates/><br />
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
<xsl:template match="a">
	<a><xsl:attribute name="href"><xsl:value-of select="@href"/></xsl:attribute><xsl:apply-templates/></a>
</xsl:template>

<xsl:template match="html"><!-- très moche car impossible à skinner, mais parfois indispensable -->
	<xsl:value-of disable-output-escaping="yes" select="text()"/>
</xsl:template>

</xsl:stylesheet>
