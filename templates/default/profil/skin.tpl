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

<form enctype="multipart/form-data" method="post" accept-charset="UTF-8" id="form_choix_skin" action="profil/skin/change_skin">
  <h2>
    <span>Choix de la skin</span>
  </h2>
  <div class="formulaire">
    <div>
      <span class="gauche">Skin :</span>
      <span class="droite">
      {foreach from=$liste_skins item=skin_desc key=skin_id}
	<input type="radio" id="{$skin_id}" name="newskin" value="{$skin_id}" {if $skin->id == $skin_id}checked="1"{/if} />
	{$skin_id} : {$skin_desc}<br />
      {/foreach}
      </span>
    </div>
    <div>
      <span class="boutons">
        <input type="submit" name="OK_skin" value="Appliquer" />
      </span>
    </div>
  </div>
</form>

<form enctype="multipart/form-data" method="post" accept-charset="UTF-8" id="form_param_skin" action="profil/skin/change_params">
  <h2>
    <span>Paramètres de la skin</span>
  </h2>
  <div class="formulaire">
    <div>
      <span class="droite">
        <span class="note">
	  Tu peux aussi ne pas faire apparaître tous les éléments de la skin. Tu gagneras ainsi de la
	  place. Choisis donc les éléments que tu veux afficher.
	</span>
      </span>
    </div>
    <div>
      <span class="gauche">
        Eléments :
      </span>
      <span class="droite">
      {foreach from=$liste_minimodules item=minimodule}
	<input type="checkbox" name="vis_{$minimodule.id}" {if $minimodule.est_visible}checked="1"{/if} />{$minimodule.desc}<br/>
      {/foreach}
      </span>
    </div>
    <div>
      <span class="boutons"><input type="submit" name="OK_param" value="Appliquer" /></span>
    </div>
  </div>
</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
