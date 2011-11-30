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
        {include file="common.head.tpl"}
        {include file="head.tpl"|rel}
    </head>

    {if !$simple}
    <body class="{if ($MiniModules_COL_FLOAT|@count) == 0}disabledAside{else}enabledAside{/if}">

        {include file="universe.tpl"}

        <div id="body">
            <div id="header">
                {include file="nav.tpl"|rel}
            </div>
            <div id="banner_message">{include file="wiki.tpl"|rel name='banner_message'}</div>
            <div id="content">
                <div id="section">
                    {include file="section.tpl"|rel}
                </div>
                <div id="aside">
                    {include file="aside.tpl"|rel}
                </div>
            </div>
        </div>

        {include file=common.devel.tpl}
    {else}
    <body class="simple">
        {include file="section.tpl"|rel}
    {/if}

    </body>
</html>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
