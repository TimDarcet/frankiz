{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010 Binet Réseau                                      *}
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

<h3><a href="groups/see/x-live"> <img class="logo" src="css/default/images/xlive.png" /></a>Prochainement avec X-Live</h3>
<h3 class="question">Are you alive?</h3>
{if $minimodule.concerts|@count > 0}
<div class="display">
    {foreach from=$minimodule.concerts item='concert'}
    {if $concert.withpic == "true"}
    <p><a href={$concert.link_pict} target=_blank><img class="img_groupe" src={$concert.pict} /></a></p>
    <table>
        <tr>
            <th class="group">Groupe</th>
            <th class="place">Salle</th>
        </tr>
        <tr>
            <td class="group"><a href={$concert.link_group} target=_blank>{$concert.group}</a></td>
            <td class="place"><a href={$concert.link_place} target=_blank>{$concert.place}</a></td>
        </tr>
        <tr>
            <th class="date">Date</th>
            <th class="price">Prix</th>
        </tr>
        <tr>
            <td class="date">{$concert.date}</td>
            <td class="price">{$concert.price}</td>
        </tr>
    </table>
    {/if}
    {/foreach}
<h3 class="later_on">Later On</h3>
    {foreach from=$minimodule.concerts item='concert'}
    {if $concert.withpic == "false"}
    {* pas d'image pour ces concerts <p><a href={$concert.link_pict} target=_blank><img class="img_groupe" src={$concert.pict} /></a></p> *}
    <table>
        <tr>
            <th class="group">Groupe</th>
            <th class="place">Salle</th>
        </tr>
        <tr>
            <td class="group"><a href={$concert.link_group} target=_blank>{$concert.group}</a></td>
            <td class="place"><a href={$concert.link_place} target=_blank>{$concert.place}</a></td>
        </tr>
        <tr>
            <th class="date">Date</th>
            <th class="price">Prix</th>
        </tr>
        <tr>
            <td class="date">{$concert.date}</td>
            <td class="price">{$concert.price}</td>
        </tr>
      </table>
    {/if}
    {/foreach}
</div>
<p class="info_fin">Pour plus d'information, consulter le site <a href="http://x-live" target=_blank>X-Live</a>.</p>
<p class="info_fin"><a href="http://x-live/index.php?categorie_page=inscription" target=_blank>S'inscrire</a> pour réserver maintenant.</p>
{/if}

{if $minimodule.concerts|@count == 0}
<h4>Pas de concert prÃ©vu pour instant.</h4>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
