<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                       *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                     *
 *                                                                         *
 *  This program is free software; you can redistribute it and/or modify   *
 *  it under the terms of the GNU General Public License as published by   *
 *  the Free Software Foundation; either version 2 of the License, or      *
 *  (at your option) any later version.                                    *
 *                                                                         *
 *  This program is distributed in the hope that it will be useful,        *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 *  GNU General Public License for more details.                           *
 *                                                                         *
 *  You should have received a copy of the GNU General Public License      *
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/

if(est_authentifie(AUTH_INTERNE)) {
?>
<module id="lienik" titre="IK électronique">
<?php
	if(!cache_recuperer('lienik',0)) {
		$DB_web->query("SELECT valeur FROM parametres WHERE nom='lienik'");
		list($lienik) = $DB_web->next_row();
		$lienik_full = "ik.php?id=".$lienik;

		echo "<a href=\"$lienik_full\"><image source=\"".BASE_URL."/data/ik_thumbnails/$lienik.png\" texte=\"IK de la semaine\"/></a> \n";

		cache_sauver('lienik');
	}
	if (est_interne()) {
		echo "<a href='".URL_BINETS."ik/'>Archive de l'IK</a>";
	}
?>
</module>
<?php
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
