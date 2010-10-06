{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009 Binet Réseau                                       *}
{*  http://www.polytechnique.fr/eleves/binets/reseau/                     *}
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
<link type="text/css" href="css/{"jquery-ui.css"|rel}" rel="stylesheet" />
<link type="text/css" href="css/{"fancybox/jquery.fancybox.css"|rel}" rel="stylesheet" />

{js src="jquery-ui.js"}
{js src="minimodules.js"}
{js src="jquery.fancybox.js"}

{literal}
<script type="text/javascript">
    $(document).ready(function(){
        ajaxify($("body"));
    });
</script>
{/literal}
        
{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
