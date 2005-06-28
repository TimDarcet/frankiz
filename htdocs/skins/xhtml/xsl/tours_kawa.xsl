<?xml version="1.0" encoding="UTF-8" ?>
<!-- 
	$Log$
	Revision 1.3  2005/04/18 10:48:04  myk
	Modif de la skin pico-2
	de la skin simple


	et enfin du XSL pour pouvoir faire des zoulies boites

	Revision 1.2  2005/04/13 17:10:10  pico
	Passage de tous les fichiers en utf8.
	
	Revision 1.1  2004/11/24 20:26:43  schmurtz
	Reorganisation des skins (affichage melange skin/css + depacement des css)
	
	Revision 1.8  2004/11/15 20:54:19  pico
	Commit global de retour de gwz
	
	Revision 1.7  2004/11/05 19:38:02  pico
	Nouvelle Css un peu plus jolie pour la skin pico
	
	Revision 1.6  2004/11/05 14:08:22  pico
	BugFix
	
	 Revision 1.5  2004/11/05 13:48:50  pico
	Corrections diverses skin

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

<xsl:template match="module[@id='tour_kawa']">
	<xsl:if test="count(liste/element) !=0">
		<div class="fkz_module_1"><div class="fkz_module_2">
		<div class="fkz_module_3"><div class="fkz_module_4">
		<div class="fkz_module_5"><div class="fkz_module_6">
		<div class="fkz_module">
			<div class="fkz_titre"><span id="tour_kawa_logo"><xsl:text> </xsl:text></span><span id="tour_kawa">Tour Kawa</span></div>
			<div class="fkz_module_corps">
				<xsl:for-each select="liste/element">
					<em><xsl:value-of select="colonne[@id='jour']"/>: </em>
					<xsl:value-of select="colonne[@id='kawa']"/>
					<br/>
				</xsl:for-each>
			</div>
		</div>
		</div></div></div></div></div></div>
	</xsl:if>

</xsl:template>

</xsl:stylesheet>
