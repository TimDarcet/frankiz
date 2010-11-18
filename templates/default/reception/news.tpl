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

<div class="newstitles">
<fieldset>
    {foreach from=$news_array item=news_ar key=membership}
        {if $membership == 'member'}
            <div class="section">
                Binets membres
                <label class="hide2" id="{$membership}">[-]</label>
            </div>
        {elseif $membership == 'friend'}
            <div class="section">
                Autres binets
                <label class="hide2" id="{$membership}">[-]</label>
            </div>
        {/if}
        
        {foreach from=$news_ar item=news_col key=date}
            {if $date == 'important'}
                <div class="subsection {$membership}">
                    <label class="hide2" id="{$membership}_{$date}">[-]</label>
                    Important
                </div>
            {elseif $date == 'new'}
                <div class="subsection {$membership}">
                    <label class="hide2" id="{$membership}_{$date}">[-]</label>
                    Nouvelles fraiches
                </div>
            {elseif $date == 'old'}
                <div class="subsection {$membership}">
                    <label class="hide2" id="{$membership}_{$date}">[-]</label>
                    Demain c'est fini
                </div>
            {elseif $date == 'other'}
                <div class="subsection {$membership}">
                    <label class="hide2" id="{$membership}_{$date}">[-]</label>
                    En attendant
                </div>
            {/if}
            
            {foreach from=$news_col item=news}
                <div class="div_newstitle">
                    {assign var='group' value=$news->group()}
                    <a href="an/#news_{$news->id()}" class="newstitle {$membership} {$membership}_{$date}">[{$group->label()}] {$news->title()}</a>
                </div>
            {/foreach}
        {/foreach}
    {/foreach}
</fieldset>
</div>



{foreach from=$news_array item=news_ar key=membership}        
    {foreach from=$news_ar item=news_col key=date}            
        {foreach from=$news_col item=news}
            {assign var='group' value=$news->group()}
            <div class="news {$membership} {$membership}_{$date} {$group->name()}" id="news_{$news->id()}">
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
        {/foreach}
    {/foreach}
{/foreach}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
