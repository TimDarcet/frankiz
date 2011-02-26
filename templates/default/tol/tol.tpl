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

{include file="tol/search.tpl"|rel}

<div class="loading" id="tol_infos">
    <div>
        <span class="empty">Utilise les filtres à gauche pour effectuer une recherche</span>
        <span class="notempty" {if isset($total|smarty:nodefaults)}style="display: inline"{/if}>
            <span class="count">{if isset($results|smarty:nodefaults)}{$results|@count}{/if}</span>
            résultats affichés sur 
            <span class="total">{if isset($total|smarty:nodefaults)}{$total}{/if}</span
        </span>
        <div class="notempty page"></div>
    </div>
</div>

<ul id="tol_results">
    {if isset($results|smarty:nodefaults)}
        {foreach from=$results item=result}
            {include file="tol/result.tpl"|rel result=$result}
        {/foreach}
    {/if}
</ul>

{js src="tol.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
