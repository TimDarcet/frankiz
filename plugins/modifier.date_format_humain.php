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


function smarty_modifier_date_format_humain($string)
{
	$day_now = floor(time() / (24*3600));
	$day_target = floor(strtotime($string) / (24*3600));

	switch ($day_target - $day_now)
	{
	case '0':
		return "Aujourd'hui";
	case '1':
		return "Demain";
	case '2':
		return "Après-demain";
	case '7':
		return "Dans une semaine";
	case '-1':
		return "Hier";
	case '-2':
		return "Avant-hier";
	default:
		if ($day_target - $day_now > 0)
			return "Dans ".($day_target - $day_now)." jours";
		else
			return "Il y a ".($day_now - $day_target)." jours";
	}
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
