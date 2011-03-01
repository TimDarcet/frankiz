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

{wiki name=$name output='wiki'}
{uniqid output='uniqid'}
<div class="wiki{if t($class)} {$class}{/if}" id="wiki_container_{$uniqid}" wid="{$wiki->id()}">
    <div class="content">
        {$wiki->html()|smarty:nodefaults}
    </div>

    {if $logged && $smarty.session.user->checkPerms('web')}
    <textarea>{$wiki->content()}</textarea>
    {/if}
</div>

{if $logged && $smarty.session.user->checkPerms('web')}
<script>
    wikify($("#wiki_container_{$uniqid}"));
</script>
{/if}


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
