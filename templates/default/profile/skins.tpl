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

<ul class="objects">
    {foreach from=$skinsList item=someskin}
    <li {if $skin == $someskin.name}class="on"{/if}>
        <p class="frequency">Popularité: {math equation="100 * x / y" x=$someskin.frequency y=$total format="%d"}%</p>
        <p class="label">{$someskin.label}</p>
        <p class="description">{$someskin.description}</p>
        <div class="change">
            {if $skin == $someskin.name}
                Habillage actuel
            {else}
            <form enctype="multipart/form-data" method="post" action="profile/skin">
                <input type="hidden" name="skin" value="{$someskin.name}" />
                <input type="submit" value="Choisir cet habillage" />
            </form>
            {/if}
        </div>
    </li>
    {/foreach}
</ul>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
