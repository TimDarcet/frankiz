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

<div class="wiki_textarea" id="wiki_textarea_{$id}">
    <div class="wiki_preview">
        {if t($already)}{$already|miniwiki|smarty:nodefaults}{/if}
    </div>
    <div class="wiki_form">
        <div class="wiki_controls">
            <div class="help" title="Afficher la syntaxe wiki"> </div>
            <div class="down" title="Aggrandir la zone de texte"> </div>
        </div>
        <div class="textarea">
            <textarea name="{$id}" id="{$id}" {if t($placeholder)}placeholder="{$placeholder|smarty:nodefaults}"{/if}>{if t($already)}{$already}{/if}</textarea>
        </div>
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
