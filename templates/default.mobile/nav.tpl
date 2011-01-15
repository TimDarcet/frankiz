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

<div id="nav-bar">
    <a id="nav-open-menu" class="nav-left">Menu</a>
    {$title}    
    {if $smarty.session.auth < AUTH_COOKIE }
    <a class="nav-right" {path_to_href_attribute path="login"}>Login</a>
    {else}
    <a class="nav-right" {path_to_href_attribute path="home"}>Home</a>
    {/if}


</div>

<ul id="nav-menu">
    <li>{include file="tol/quicksearch.tpl"|rel}</li>
    <li><a {path_to_href_attribute path="tol"}>TOL advanced</a></li>
    <li><a {path_to_href_attribute path="news"}>News</a></li>
    {if $smarty.session.auth >= AUTH_COOKIE }
    <li><a {path_to_href_attribute path="exit"} nosolo="true">Logout</a></li>
    {/if}

</ul>

{literal}
<script>
    var topvalue = "-100%";

    $("#nav-open-menu").click(function(){

        if ($("#nav-menu").css("top") == "41px") {
            $("#nav-menu").animate({"top": "-100%", "bottom": "100%"}, 700);
        } else {
            $("#nav-menu").animate({"top": "41px", "bottom": "0"}, 700);
        };

    });
</script>
{/literal}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}