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

<?xml version='1.0' encoding='UTF-8' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
        {include file="common.header.tpl"}

        <link rel="stylesheet" type="text/css" href="css/default/default.css" media="all"/>
        <link type="text/css" href="css/default/jquery-ui.css" rel="stylesheet" />
        <link type="text/css" href="css/default/jquery_tree/style.css" rel="stylesheet" />
    </head>
    <body>
        <div id="errorBox" title="Erreur"></div>

        {include file=common.devel.tpl}
        <div id="header">
            <div id="logo">
                <a href="accueil"></a>
            </div>
        </div>
        <div id="center">
            <div id="navigation">
                {include file="default/menu.tpl"}
            </div>
            <table id="bigandminimodules">
                <tr>
                    <td id="module">
                        {include file="default/content.tpl"}
                    </td>
                    <td id="column" class="{if ($minimodules_layout[4]|@count) == 0}empty{else}full{/if}">
                        <ul id="column4" class="minimodules_zone">
                            {foreach from=$minimodules_layout[4] item=module}
                                {include file="default/minimodule.tpl" module_name=$module}
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
