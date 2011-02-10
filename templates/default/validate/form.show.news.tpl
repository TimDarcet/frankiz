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
        {$item->end()|datetime:'Y-m-d'}
    </td>
</tr>

<tr>
    <td width=20%>
        Annonce :
    </td>
    <td>
        {assign var='caste' value=$item->target()}
        <div class="news_validate">
        <fieldset>
            {assign var='target_group' value=$caste->group()}
            <legend>[{$target_group|group}] {$item->title()}</legend>
            <div class="body">
                {assign var='image' value=$item->image()}
                {$item->content()|miniwiki|smarty:nodefaults}
            </div>
            <div class="infos">
                {if !$item->origin()}
                    Pour le groupe {$item->origin()|group},
                {/if}
                {$item->writer()|user}
            </div>
        </fieldset>
        </div>
    </td>
</tr>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}