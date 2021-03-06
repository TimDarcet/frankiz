{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010-2013 Binet Réseau                                  *}
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

{if ($item->type_mail() == 'promo')}
    <tr>
        <td width=20%>
            Promos :
        </td>
        <td>
            {if $item->targets()}
                {foreach from=$item->targets() item=promo}
                    {assign value=$promo->group() var=promo_group}
                    {$promo_group->label()}
                {/foreach}
            {else}
                toutes
            {/if}
        </td>
    </tr>
{/if}

<tr>
    <td width=20%>
        Titre :
    </td>
    <td>
        {$item->subject()}
    </td>
</tr>

<tr>
    <td width=20%>
        Mail :
    </td>
    <td>
        {if $item->nowiki()}
            {$item->body()}
        {else}
            {$item->body()|miniwiki|smarty:nodefaults}
        {/if}
    </td>
</tr>



{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
