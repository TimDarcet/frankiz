<?xml version="1.0" encoding="ISO-8859-1" ?>
<!--
	Skin tr�s simple utilisant des tables pour la disposition des �l�ments.
	Le but de cette skin est de ne pas se triturer les neurones pour faire
	une sortie html propre et skinnable quand on travail sur le code php.
	
	$Log$
	Revision 1.13  2004/09/18 00:51:41  kikx
	Permet d'uploader des fichiers
	rajout d'un cahmp image dan sles annonces car on a le droit qu'a une seule image ...

	Revision 1.12  2004/09/17 16:27:13  schmurtz
	Simplification de l'affichage des anniversaires et correction d'un bug d'affichage.
	
	Revision 1.11  2004/09/17 15:28:14  schmurtz
	Utilisation de la balise <eleve> pour les derniers votants à la qdj, les anniversaires, la signature des annonces…
	
	Revision 1.10  2004/09/16 15:32:43  schmurtz
	Suppression de la fonction afficher_identifiant(), utilisation de <![CDATA[......]]> à la place.
	
	Revision 1.9  2004/09/16 13:44:34  schmurtz
	Amélioration de l'affichage de la page des binets (avec le classement par categorie mais pas le trie).
	Passage de la sortie de la skin basic en xhtml avec doctype.
	
	Revision 1.8  2004/09/16 11:09:38  kikx
	C'est les vacances maintenant ...
	Bon bref .. c'est dur aussi
	Bon j'ai un peu arrang� la page des binets
	
	Revision 1.7  2004/09/15 23:19:56  schmurtz
	Suppression de la variable CVS "Id" (fait double emploi avec "Log")
	
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="html.xsl"/>
<xsl:include href="form.xsl"/>
<xsl:output method="xml" indent="yes" encoding="ISO-8859-1"
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>

<xsl:template match="/frankiz"> 
	<xsl:apply-templates select="page"/>
</xsl:template>

<!-- D�finition d'une page web de frankiz -->
<xsl:template match="/frankiz/page">
	
	<html>
	<head>
		<title><xsl:value-of select="@titre"/></title>
		<!-- semble ne pas marcher avec certains navigateurs lorsque la base est relative -->
		<base>
			<xsl:attribute name="href">
				<xsl:value-of select="../@base"/>
			</xsl:attribute>
		</base>
		<link rel="stylesheet" type="text/css" href="skins/basic/style.css"/>
		<xsl:apply-templates select="/frankiz/module[@id='liste_css']" mode="css"/>
	</head>

	<body style="margin: 0">
		<table cellspacing="0" cellpadding="0">
			<tr><td id="frankiz" colspan="2">
				Frankiz, le serveur des �l�ves
				
			</td></tr><tr><td id="modules">
				<table cellspacing="0" cellpadding="0">
					<xsl:apply-templates select="/frankiz/module"/>
				</table>
				
			</td><td id="contenu">
				<xsl:apply-templates/>
				
			</td></tr>
		</table>
	</body>
	</html>

</xsl:template>

<!-- les CSS compl�mentaires -->
<xsl:template match="/frankiz/module[@id='liste_css']" mode="css">
	<xsl:for-each select="lien">
		<link rel="alternate stylesheet" type="text/css">
			<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:value-of select="@titre"/></xsl:attribute>
		</link>
    </xsl:for-each>
</xsl:template>

<!-- D�finition des modules -->
<xsl:template match="/frankiz/module">
	<xsl:if test="(boolean(@visible) = false) or (@visible = 'true')">
		<tr><th><xsl:value-of select="@titre"/></th></tr>
		<tr><td><xsl:apply-templates/></td></tr>
	</xsl:if>
</xsl:template>

<!-- Annonces (une annonce dans un module correspond � une activit�) -->
<xsl:template match="page/annonce">
	<table class="annonce" cellspacing="0" cellpadding="0">
		<tr><th><xsl:value-of select="@titre"/> (<xsl:value-of select="@date"/>)</th></tr>
		<tr><td>
			<center><xsl:apply-templates select="image"/></center>
		</td></tr>
		<tr><td>
			<xsl:value-of disable-output-escaping="yes" select="text()"/>
			<p class="signature"><xsl:apply-templates select="eleve"/></p>
		</td></tr>
	</table><br/>
</xsl:template>

<xsl:template match="module/annonce">
	<xsl:apply-templates/>
</xsl:template>

<!-- statistiques -->
<xsl:template match="statistiques">
	�tat des serveurs�:<br/>
	<xsl:for-each select="serveur">
		- <span><xsl:attribute name="class">serveur_<xsl:value-of select="@etat"/></xsl:attribute>
			<xsl:value-of select="@nom"/></span>
		<xsl:if test="boolean(@uptime)">: <xsl:value-of select="@uptime"/> jours</xsl:if>
		<br/>
	</xsl:for-each>
	Statistiques�:<br/>
	<xsl:for-each select="service">
		- <a>
			<xsl:attribute name="href"><xsl:value-of select="@stat"/></xsl:attribute>
			<xsl:value-of select="@nom"/>
		</a>
		<br/>
	</xsl:for-each>
</xsl:template>

<!-- qdj (peut contenir plus de deux r�ponses) -->
<xsl:template match="module/qdj">
	<xsl:apply-templates select="question"/><br/>
	
	<xsl:choose>
		<!-- l'utilisateur n'a pas encore vot� -->
		<xsl:when test="boolean(@action)">
			<xsl:for-each select="reponse">
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="../@action"/><xsl:value-of select="@id"/>
					</xsl:attribute>
					<xsl:apply-templates/>
				</a><br/>
			</xsl:for-each>
		</xsl:when>
		
		<!-- l'utilisateur � d�j� vot� (on affiche les r�sultats) -->
		<xsl:otherwise>
			<xsl:variable name="sum_votes" select="sum(reponse/@votes)"/>
			<xsl:choose>
				<xsl:when test="$sum_votes != 0">
					<xsl:for-each select="reponse">
						<xsl:apply-templates/>: <xsl:value-of select="round((@votes * 100) div $sum_votes)"/>%<br/>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise> <!-- petite subtilit� si aucun vote n'a �t� effectu� -->
					<xsl:for-each select="reponse"><xsl:apply-templates/>: 0%<br/></xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
	
	<xsl:for-each select="dernier">
		<xsl:apply-templates select="eleve"/>
	</xsl:for-each>
</xsl:template>

<!-- Eleves pour les anniversaires/signatures/qdj -->
<xsl:template match="eleve">
	<xsl:choose><xsl:when test="@surnom != ''">
		<xsl:value-of select="@surnom"/>
	</xsl:when><xsl:otherwise>
		<xsl:value-of select="@prenom"/><xsl:text> </xsl:text><xsl:value-of select="@nom"/>
	</xsl:otherwise></xsl:choose>
	<xsl:if test="@promo != ''">
		<xsl:text> (</xsl:text><xsl:value-of select="@promo"/><xsl:text>)</xsl:text>
	</xsl:if>
	<br/>
</xsl:template>

<!-- Eleves pour le trombino -->
<xsl:template match="page/eleve">
	<table class="trombino" cellspacing="0" cellpadding="0">
		<tr><td class="titre" colspan="2">
			<xsl:value-of select="@prenom"/><xsl:text> </xsl:text><xsl:value-of select="@nom"/>
			<xsl:text> (</xsl:text><xsl:value-of select="@promo"/><xsl:text>)</xsl:text>
		</td></tr>
		<tr><td>
			<img alt="photo" width="80" height="95">
				<xsl:attribute name="src">trombino/?image=true&amp;login=<xsl:value-of select="@login"/>&amp;promo=<xsl:value-of select="@promo"/></xsl:attribute>
			</img>
		</td><td width="100%">
			Surnom�: <xsl:value-of select="@surnom"/><br/>
			Tel�: <xsl:value-of select="@tel"/><br/>
			Kazert�: <xsl:value-of select="@casert"/><br/>
			Mail�: <xsl:value-of select="@mail"/><br/>
			Section�: <xsl:value-of select="@section"/> (<xsl:value-of select="@cie"/>e Cie)<br/>
			Binets : <xsl:apply-templates select="binet"/><br/>
		</td></tr>
	</table>
</xsl:template>

<xsl:template match="page/eleve/binet">
	<xsl:value-of select="@nom"/><xsl:text> (</xsl:text><xsl:value-of select="."/><xsl:text>) </xsl:text>
</xsl:template>

<!-- Page des binets -->

<xsl:template match="page/binet">
	<xsl:if test="preceding-sibling::binet[1]/@categorie != @categorie or position() = 2">
		<!-- TODO comprendre pourquoi postion() = 2 et pas 1 :) -->
		<xsl:text disable-output-escaping="yes">&lt;table class="binets" cellpadding="0" cellspacing="0"&gt;</xsl:text>
		<tr><th colspan="2"><h2><xsl:value-of select="@categorie"/></h2></th></tr>
	</xsl:if>
	
	<tr><td width="120">
		<a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:apply-templates select="image"/>
		</a>
	</td><td width="100%">
		<h3><xsl:value-of select="@nom"/></h3>
		<p><xsl:value-of select="description"/></p>
	</td></tr>
	
	<xsl:if test="following-sibling::binet[1]/@categorie != @categorie or position() = last()-1">
		<!-- TODO comprendre pourquoi postion() = last()-1 et pas last() :) -->
		<xsl:text disable-output-escaping="yes">&lt;/table&gt;</xsl:text>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
