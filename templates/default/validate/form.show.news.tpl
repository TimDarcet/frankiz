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
{assign var='news' value=$item->news()}

<tr>
    <td width=20%>
        Commentaire :
    </td>
    <td>
        {$news->comment()}
    </td>
</tr>

<tr>
    <td width=20%>
        Privé :
    </td>
    <td>
        {if $news->priv()} oui {else} non {/if}
    </td>
</tr>

{if $news->important()} 
    <tr>
        <td width=20%>
            Important :
        </td>
        <td>
            oui
        </td>
    </tr> 
{/if}

<tr>
    <td width=20%>
        Dernier jour :
    </td>
    <td>
        {$news->end()}
    </td>
</tr>

<tr>
    <td width=20%>
        Annonce :
    </td>
    <td>
        {assign var='group' value=$news->group()}
        <div class="news_validate">
        <fieldset>  
            <legend>[{$group->label()}] {$news->title()}</legend>
            <div class="body">
                {assign var='image' value=$news->image()}
                {$news->content()|miniwiki|smarty:nodefaults}
            </div>
            <div class="infos">
                {if !is_null($news->origin())}
                    {assign var='origin' value=$news->origin()}
                    Pour le groupe {$origin->label()},
                {/if}
                {assign var='user' value=$news->user()}
                {$user->displayName()}
            </div>
        </fieldset>
        </div>
    </td>
</tr>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}