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

<div class="module">
    <div class="head">
        Annonce: {$news->title()}
    </div>
    <div class="body see {if $news->read()}read{else}unread{/if} {if $news->star()}star{else}unstar{/if}"
         nid="{$news->id()}">

        <div class="content">
            {if $news->image()}
                <div class="img">
                    <img src="{$news->image()|image:'small'|smarty:nodefaults}" />
                </div>
            {/if}
            {$news->content()|miniwiki:'title'|smarty:nodefaults}
            <br class="clear">
        </div>

        <div class="meta">
            <div class="star_switcher" title="Suivre l'annonce"></div>
            {canEdit target=$news->target()}
                <div class="admin">
                    <a href="news/admin/{$news->id()}"><div class="edit"></div>Modifier</a>
                </div>
            {/canEdit}
            <div class="infos">
                Rédigée par 

                {assign var='writer' value=$news->writer()}
                <a href="tol/?hruid={$writer->login()}">
                <img src="{$writer->image()|image:'micro'|smarty:nodefaults}" 
                     title="{$writer->displayName()}"/>
                </a>

                {if $news->origin()}
                au nom de 
                    {assign var='origin' value=$news->origin()}
                    <a href="groups/see/{$origin->name()}">
                    <img src="{$origin->image()|image:'micro'|smarty:nodefaults}" 
                         title="{$origin->label()}"/>
                    </a>
                {/if}

                le 
                {$news->begin()|datetime:'d/m/y'}
                à
                {$news->begin()|datetime:'h:i'}
            </div>
        </div>

    </div>
</div>

{js src="news.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
