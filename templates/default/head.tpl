{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009-2013 Binet Réseau                                  *}
{*  http://br.binets.fr/                                                  *}
{*                                                                        *}
{*  This program is free software; you can redistribute it and/or modify  *}
{*  it under the terms of the GNU General Public License as published by  *}
{*  the Free Software Foundation; either version 2 of the License, or     *}
{*  (at your option) any later version.                                   *}
{*                                                                        *}
{*  This program is distributed in the hope that it will be useful,       *}
{*  but WITHOUT ANY WARRANTY; without even the implied warranty of        *}
{*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *}
{*  GNU General Public License for more details.                          *}
{*                                                                        *}
{*  You should have received a copy of the GNU General Public License     *}
{*  along with this program; if not, write to the Free Software           *}
{*  Foundation, Inc.,                                                     *}
{*  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA               *}
{*                                                                        *}
{**************************************************************************}

<link rel="stylesheet" type="text/css" href="css/{"base.css"|rel}" media="all"/>
<link rel="stylesheet" type="text/css" href="css/universe.css" media="all"/>
<link type="text/css" href="css/{"jquery-ui.css"|rel}" rel="stylesheet" />
<link type="text/css" href="css/{"fancybox/jquery.fancybox.css"|rel}" rel="stylesheet" />
<link type="text/css" href="css/{"addition.css"|rel}" rel="stylesheet" />

{js src="jquery-ui.js"}
{js src="jquery-ui-timepicker-addon.js"}
{js src="localization.js"}
{js src="minimodules.js"}
{js src="jquery.fancybox.js"}
{js src="plugins/jquery.tmpl.js"}
{if $smarty.session.auth >= AUTH_COOKIE}
{js src="visibilityflag.js"}
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
