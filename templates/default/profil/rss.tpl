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

{if isset($rss_add|smarty:nodefaults)}
<span class='note'>Flux rss ajouté</span>
{/if}

{if isset($rss_del|smarty:nodefaults)}
<span class='note'>Flux rss supprimé</span>
{/if}

{if isset($rss_update|smarty:nodefaults)}
<span class='note'>Flux rss mis à jour</span>
{/if}

<form enctype='multipart/form-data' method='post' action='profil/rss/update'>
  <table>
    <tr>
      <td></td>
      <td>Affichage sommaire</td>
      <td>Affichage complet</td>
      <td>Affichage en module</td>
      <td>Supprimer</td>
    </tr>
    {foreach name='foo' from=$smarty.session.liens_rss key=lien item=lien_status}
    <tr>
      {assign var=count value=$smarty.foreach.foo.index}
      <td>
        <input type='hidden' name='rss_lien_{$count}' value='{$lien}' />
        {$lien_status.description}
      </td>
      <td>
        <input type='checkbox' name='rss_sommaire_{$count}' {if $lien_status.sommaire}checked='1'{/if} />
      </td>
      <td>
        <input type='checkbox' name='rss_complet_{$count}' {if $lien_status.complet}checked='1'{/if} />
      </td>
      <td>
        <input type='checkbox' name='rss_module_{$count}' {if $lien_status.module}checked='1'{/if} />
      </td>
      <td>
        <input type='checkbox' name='rss_del_{$count}' {if isset($nodelete.$lien|smarty:nodefaults)}disabled='1'{/if} />
      </td>
    </tr>
    {/foreach}
  </table>
  
  <input type='hidden' name='nbr_rss' value='{$smarty.foreach.foo.total}' />
  <input type='submit' value='Mise à jour' />
</form>

<br />

<form method='post' action='profil/rss/add'>
  <input type='text' name='rss_lien_add' />
  <input type='submit' value='Ajouter un flux perso' />
</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
