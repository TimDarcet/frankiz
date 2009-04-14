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

<div style='text-align:center'>
  <em>
    {if $bob_ouvert}
      Le BôB est ouvert
    {else}
      Le BôB est fermé
    {/if}
  </em>
</div>
<br />
<div style='text-align:center'>
  <em>
    {if $kes_ouverte}
      La Kès est ouverte
    {else}
      La Kès est fermée
    {/if}
  </em>
</div>
{foreach from=$activites item=activites_date key=date}
<h3>{$date|date_format_humain}</h3>
{foreach from=$activites_date item=activite}
<div style='text-align:center'>
  A {$activite.date|date_format:"%H:%M"}<br />
  <a class='lien' href="{$activite.url}">
    <span class='image' style='display:block; text-align:center'>
      <img src='http://frankiz/data/affiches/{$activite.id}' alt='Affiche' />
    </span>
    <span class='legende'>
      {$activite.titre}
    </span>
  </a>
  <br />
  <p>
    {$activite.texte}
  </p>
</div>
<br />
{/foreach}
{/foreach}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
