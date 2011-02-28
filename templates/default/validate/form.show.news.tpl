{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010 Binet Réseau                                       *}
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

<tr>
    <td width=20%>
        Commentaire :
    </td>
    <td>
        {$item->comment()}
    </td>
</tr>

<tr>
    <td width=20%>
        Dernier jour :
    </td>
    <td>
        {$item->end()|datetime:'Y-m-d H:i'}
    </td>
</tr>

<tr>
    <td width=20%>
        Au nom de :
    </td>
    <td>
        {if $item->origin()}
            {$item->origin()|group:'text'}
        {else}
            personnel
        {/if}
    </td>
</tr>

<tr>
    <td>
        Annonce :
    </td>
    <td class="news">
        <div class="infos">
            <table><tr>
                <td class="origin">
                    {if $item->origin()}
                        {$item->origin()|group}
                    {else}
                        {$item->writer()|user}
                    {/if}
                </td>
                <td class="title">
                    {$item->title()}
                </td>
                <td class="date">
                    {$item->begin()|age}
                </td>
            </tr></table>
        </div>
        <div class="content">
            <div class="body">
                {if $item->image()}
                    <img class="image" src="{$item->image()|image:'small'|smarty:nodefaults}" />
                {/if}
                {$item->content()|miniwiki:'title'|smarty:nodefaults}
            </div>
            <div class="writer">
                {$item->writer()|user}
            </div>
        </div>
    </td>
</tr>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}