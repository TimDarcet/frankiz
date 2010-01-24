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
{include file="skin/common.header.tpl" more="skin/default/header.tpl"}
    <body>
        <div id="errorBox" title="Erreur"></div>

        {include file=skin/common.devel.tpl}
        <div id="header">
            <div id="logo">
                <a href="accueil"></a>
            </div>
        </div>
        <div id="center">
            <div id="navigation">
                {include file="skin/common.menu.tpl"}
            </div>
            <table id="bigandminimodules">
                <tr>
                    <td id="module">
                        {include file="content.tpl"}
                    </td>
                    <td id="column" class="{if ($minimodules_layout[4]|@count) == 0}empty{else}full{/if}">
                        <ul id="column4" class="minimodules_zone">
                            {foreach from=$minimodules_layout[4] item=module}
                                {include file="minimodule.tpl" module_name=$module}
                            {/foreach}
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
        <div id="footer"></div>
    </body>
</html>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
