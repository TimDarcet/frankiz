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
{if isset($banana|smarty:nodefaults)}
{$banana|smarty:nodefaults}
{else}

<ul id="onglet"> 
	<li class="actif">Préférences</a> </li> 
	<li><a href="banana/subscribe">Abonnements</a> </li> 
	<li><a href="banana">Les forums</a> </li>
</ul> 

<a href="banana/updateall"> Marquer tous les messages comme lus </a>

<p class="normal">
  Tu pourras voir dans les forums les nouveaux messages mis en valeur (en
  général en gras). Si tu consultes les forums régulièrement, tu peux en avoir
  assez de voir tout le contenu du forum&nbsp;: la dernière option te permet de
  n'afficher que les fils de discussion contenant des messages non lus.
</p>

<form action="banana/profile" method="post">
  <table class="bicol" cellpadding="3" cellspacing="0" summary="Configuration de Banana">
    <tr>
      <th colspan="2">Profil sur les brs</th>
    </tr>
    <tr>
      <td class="titre">Signature</td>
      <td><textarea name="bananasig" cols="50" rows="4">{$sig}</textarea></td>
    </tr>
    <tr class="pair">
      <td class="titre">Affichage des fils de discussion</td>
      <td>
        <label><input type="radio" name="bananadisplay" value="0"
               {if !$disp}checked="checked"{/if} /> Afficher tous les messages</label><br />
        <label><input type="radio" name="bananadisplay" value="1" {if $disp}checked="checked"{/if} />
        Afficher seulement les fils de discussion contenant des messages non lus</label>
      </td>
    </tr>
    <tr class="pair">
      <td class="titre">Aspect de l'arborescence</td>
      <td>
        {foreach from=$colors item=color}
          <label>non-lu <input type="radio" name="unread" value="{$color}" {if $unread eq $color}checked="checked"{/if} /></label>
          <img src="data/banana/m2{$color}.gif" alt="{$color}" />
          <label><input type="radio" name="read" value="{$color}" {if $read eq $color}checked="checked"{/if} /> lu</label>
          <br />
        {/foreach}
      </td>
    </tr>
    <tr class="pair">
      <td class="titre">Mise à jour des messages non lus</td>
      <td>
        <label><input type="radio" name="bananaupdate" value="1"
               {if $maj}checked="checked"{/if} /> Automatique</label><br />
        <label><input type="radio" name="bananaupdate" value="0"
               {if !$maj}checked="checked"{/if} /> Manuelle</label>
      </td>
    </tr>
  </table>
  <div class="center"><input type="submit" name="action" value="Enregistrer" /></div>
</form>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
