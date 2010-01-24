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


<div class="accueil">
    <table>
        <tr>
            {section name=i start=1 loop=4 step=1}
            <td class="{if ($minimodules_layout[i]|@count) == 0}empty{else}full{/if}">
                <ul id="column{$smarty.section.i.index}" class="minimodules_zone">
                    {foreach from=$minimodules_layout[i] item=module}
                        {include file="minimodule.tpl" module_name=$module}
                    {/foreach}
                </ul>
            </td>
            {/section}
        </tr>
    </table>
    <br class="clear" />
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
