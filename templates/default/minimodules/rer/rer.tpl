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

{if $minimodule.trains|@count > 0}
<h3><img class="logo" src="css/default/images/rer_b.png" /> PROCHAINS TRAINS</h3>
<div class="display">
<h4 class="currentTime">{$minimodule.currentTime}</h4>
<h4>Direction MITRY-CLAYE<br/>AEROPORT CH.DE GAULLE</h4>
<table>
    <tr>
        <th class="name">Nom</th>
        <th class="desc">Destination</th>
        <th class="time">Heure de passage</th>
    </tr>
    {foreach from=$minimodule.trains item='train'}
        <tr>
            <td class="name">{$train.name}</td>
            <td class="desc">{$train.desc}</td>
            <td class="time">{$train.time}</td>
        </tr>
    {/foreach}
</table>
</div>
{/if}

{if $minimodule.trains|@count == 0}
<h4>Plus de trains aujourd'hui...</h4>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
