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
	Revision 1.13  2004/12/15 22:11:37  psycow
	Gestion anniversaire et activités

	Revision 1.12  2004/12/15 07:04:35  schmurtz
	epsilon
	
	Revision 1.11  2004/12/15 01:08:53  schmurtz
	Image des annonces centrees
	
	Revision 1.10  2004/12/14 22:36:27  schmurtz
	Bug sur la skin defaut : <p> n'etait pas traduit
	
	Revision 1.9  2004/12/14 14:40:56  psycow
	Modification de la qdj et du sommaire de la page annonces, suite des test IE
	
	Revision 1.8  2004/12/13 21:02:59  pico
	Voilà !
	
	Revision 1.7  2004/12/13 20:51:24  pico
	New balises :)
	
	Revision 1.6  2004/12/11 06:08:04  psycow
	Gestion de l'effacement des news lues
	
	Revision 1.5  2004/12/10 03:04:31  psycow
	Resolution du probleme des boites sous Firefox, reste un probleme sur le positionnement des formulaires dans les boites...
	
	Revision 1.4  2004/11/30 21:33:18  pico
	Rajout des feuilles à la base d'un arbre
	
	Revision 1.3  2004/11/28 01:33:33  pico
	Gestion des listes sur le wiki (arbre + feuille)
	
	Revision 1.2  2004/11/27 20:58:37  pico
	Ajout de la balise <br/>
	
	Revision 1.1  2004/11/24 20:26:40  schmurtz
	Reorganisation des skins (affichage melange skin/css + depacement des css)
	
	Revision 1.9  2004/11/12 00:23:04  psycow
	Modification du trombi, gestion graphique des formulaires; c'est pas trop mal on touche a la fin
	
	Revision 1.8  2004/11/08 12:20:14  psycow
	Derniere modif...
	
	Revision 1.4  2004/11/04 15:18:01  psycow
	Un bon debut mais plus compatible IE j'en ai peur
	
	Revision 1.3  2004/11/03 23:38:39  psycow
	Un bon début
	
	
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<!-- Images -->
<xsl:template match="image">
	<p class="image">
		<img>
		<xsl:attribute name="src"><xsl:value-of select="@source"/></xsl:attribute>
		<xsl:attribute name="alt"><xsl:value-of select="@legende"/></xsl:attribute>
		<xsl:if test="boolean(@height)"><xsl:attribute name="height"><xsl:value-of select="@height"/></xsl:attribute></xsl:if>
		<xsl:if test="boolean(@width)"><xsl:attribute name="width"><xsl:value-of select="@width"/></xsl:attribute></xsl:if>
		</img>
	</p>
	<xsl:if test="boolean(@legende)">
		<span class="legende"><xsl:value-of select="@legende"/></span><br />
	</xsl:if>
</xsl:template>


<!-- Liens -->
<xsl:template match="lien">
	<a class="lien">
		<xsl:if test="boolean(@id)">
			<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
		</xsl:if>
		<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
		<xsl:attribute name="title"><xsl:value-of select="@titre"/></xsl:attribute>
		<span><xsl:value-of select="@titre"/><xsl:apply-templates/></span>
	</a>
</xsl:template>


<!-- Listes -->
<xsl:template match="liste">
	<xsl:apply-templates select="commentaire"/>
	<form method="post"><xsl:attribute name="action"><xsl:value-of select="@action"/></xsl:attribute>
		<xsl:variable name="nombre_colonnes" select="count(entete)+count(@selectionnable)"/>
		<table class="liste" cellspacing="0" cellpadding="0">
			<thead>
				<xsl:if test="boolean(@titre)">
					<tr class="titre">
						<td>
							<xsl:attribute name="colspan"><xsl:value-of select="$nombre_colonnes"/></xsl:attribute>
							<xsl:value-of select="@titre"/>
						</td>
					</tr>
				</xsl:if>
				<tr>
					<xsl:if test="@selectionnable='oui'">
						<td class="entete">&#160;</td>
					</xsl:if>
					<xsl:apply-templates select="entete"/>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td class="boutons">
						<xsl:attribute name="colspan"><xsl:value-of select="$nombre_colonnes"/></xsl:attribute>
						<xsl:apply-templates select="bouton"/>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<xsl:apply-templates select="element"/>
			</tbody>
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
		<xsl:attribute name="class"><xsl:if test="(position() mod 2)=0">pair</xsl:if><xsl:if test="(position() mod 2)=1">impair</xsl:if></xsl:attribute>
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
	<ul>
		<xsl:apply-templates select="noeud|feuille"/>
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
	<p class="texte"><xsl:apply-templates/></p>
</xsl:template>
<xsl:template match="ul">
	<ul><xsl:apply-templates/></ul>
</xsl:template>
<xsl:template match="li">
	<li><xsl:apply-templates/></li>
</xsl:template>
<xsl:template match="br">
	<br/>
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
<xsl:template match="new_string">
	<span style="color: green"><xsl:apply-templates/></span>
</xsl:template>
<xsl:template match="old_string">
	<span style="color: red; text-decoration: line-through"><xsl:apply-templates/></span>
</xsl:template>
<xsl:template match="html"><!-- très moche car impossible à skinner, mais parfois indispensable -->
	<xsl:value-of disable-output-escaping="yes" select="text()"/>
</xsl:template>

</xsl:stylesheet>
