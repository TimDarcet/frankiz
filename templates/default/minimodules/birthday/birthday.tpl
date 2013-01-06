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

{foreach from=$minimodule.users key='form' item='promos'}
    {assign var='formation' value=$minimodule.formations.$form}
    <table>
    {foreach from=$promos key='promo' item='users'}
        <tr>
            <td class="study">
                <div>
                    <img src="{$formation->image()|image:'micro'}" />
                    <span class="promo {if $promo % 2 == 0}rouje{else}jone{/if}">{$promo}</span>
                </div>
            </td>
            <td>
                {foreach from=$users item=user name=loop}
                    {$user|user}
                {/foreach}
            </td>
        </tr>
    {/foreach}
    </table>
{/foreach}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
