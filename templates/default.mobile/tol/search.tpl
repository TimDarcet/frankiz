{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009 Binet RÃ©seau                                       *}
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

<div class="minimodule" id="tol_searcher">
    <div class="head">
        Rechercher sur le trombino
    </div>
    <div class="body">
        <form class="trombino" enctype="multipart/form-data" method="post" action="tol/">
        <input type="hidden" name="mode" value="card" />
        <fieldset id="tol_infos" class="loading">
            <span class="empty">Pas de résultats</span>
            <span class="notempty" {if isset($total|smarty:nodefaults)}style="display: inline"{/if}><span class="count">{if isset($results|smarty:nodefaults)}{$results|@count}{/if}</span> / <span class="total">{if isset($total|smarty:nodefaults)}{$total}{/if}</span></span>
        </fieldset>
        <fieldset id="tol_names">
        <ul>
            <li><label>Prénom<input auto="auto" type="text" name="firstname" value="{$fields.firstname}" /></label></li>
            <li><label>Nom<input auto="auto" type="text" name="lastname" value="{$fields.lastname}" /></label></li>
            <li><label>Surnom<input auto="auto" type="text" name="nickname" value="{$fields.nickname}" /></label></li>
            <li><label>Nationalités{include file="groups_picker.tpl"|rel id="nationalities" ns="nationality" check=-1}</label></li>
        </ul>
        </fieldset>
        <fieldset id="tol_studies">
        <ul>
            <li><label>Études{include file="groups_picker.tpl"|rel id="studies" ns="study" check=-1}</label></li>
        </ul>
        </fieldset>
        <fieldset id="tol_sports">
        <ul>
            <li><label>Sports{include file="groups_picker.tpl"|rel id="sports" ns="sport" check=-1}</label></li>
        </ul>
        </fieldset>
        <fieldset id="tol_binets">
        <ul>
            <li><label>Binet{include file="groups_picker.tpl"|rel id="binets" ns="binet" check=-1}</label></li>
        </ul>
        </fieldset>
        <fieldset id="tol_rooms">
        <ul>
            <li><label>Casert<input auto="auto" type="text" name="room" value="" /></label></li>
            <li><label>Tel<input auto="auto" type="text" name="phone" value="" /></label></li>
            <li><label>IP<input auto="auto" type="text" name="ip" value="" /></label></li>
        </ul>
        </fieldset>
        <fieldset id="tol_send">
            <input type="submit" name="chercher" value="Chercher" />
        </fieldset>
        </form>
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}