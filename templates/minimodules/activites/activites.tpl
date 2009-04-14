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

{foreach from=$minimodule.activites item=activite}
{if $activite.exterieur or $session->est_authentifie(AUTH_INTERNE)}
<div style="text-align:center">
  <strong><span>{$activite.titre}</span></strong>
</div>
<div style="text-align:center">
  {if $activite.date neq ""}{$activite.date|date_format:"A %H:%M"}<br />{/if}
  <a href="{if $activite.url neq ""}{$activite.url}{else}activites.php{/if}">
    <img src="{$activite.image}" alt="Affiche" /><br />
    <span class="legende">{$activite.titre}</span>
  </a>
</div>
{/if}
<br />
{/foreach}
{if $minimodule.activites_etat_bob}
{* and $session->est_authentifie(AUTH_INTERNE)} *}
<div style="text-align:center">
  <strong><span>Le Bôb est ouvert!</span></strong>
</div>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
