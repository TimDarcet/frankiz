{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009-2013 Binet Réseau                                  *}
{*  http://br.binets.fr/                                                  *}
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

<div id="tol_searcher">
    <form class="trombino" enctype="multipart/form-data" method="post" action="tol/">
        <input type="hidden" name="mode" value="card" />
        <input type="hidden" name="page" value="1" />

        <div class="fields" id="tol_names">
            <ul>
                <li><label>Prénom<input auto="auto" type="text" name="firstname" value="{$fields.firstname}" /></label></li>
                <li><label>Nom<input auto="auto" type="text" name="lastname" value="{$fields.lastname}" /></label></li>
                <li><label>Surnom<input auto="auto" type="text" name="nickname" value="{$fields.nickname}" /></label></li>
                <li><label>Nationalités{include file="groups_picker.tpl"|rel id="nationalities" ns="nationality" check=-1}</label></li>
                <li><label>Portable<input auto="auto" type="text" name="cellphone" value="" /></label></li>
            </ul>
        </div>

        <div class="fields" id="tol_promo">
            <ul>
                <li><label>Promo
                {if $promoDefaultFilters == null}
                    {include file="groups_picker.tpl"|rel id="promo" ns="promo" order="name" check=-1}
                {else}
                    {include file="groups_picker.tpl"|rel id="promo" ns="promo" order="name" already=$promoDefaultFilters check=-1}
                {/if}
                </label></li>
                <li><label>Études{include file="groups_picker.tpl"|rel id="studies" ns="study" check=-1}</label></li>
                <li><label>Sports{include file="groups_picker.tpl"|rel id="sports" ns="sport" check=-1}</label></li>
                <li><label>Cours{include file="groups_picker.tpl"|rel id="courses" ns="course" check=-1}</label></li>
            </ul>
        </div>

        <div class="fields" id="tol_binets">
            <ul>
                <li><label>Binets{include file="groups_picker.tpl"|rel id="binets" ns="binet" check=-1 already=$already_groups|filter:'ns':'binet'}</label></li>
                <li><label>Groupes{include file="groups_picker.tpl"|rel id="frees" ns="free" check=-1 already=$already_groups|filter:'ns':'free'}</label></li>
            </ul>
        </div>

        <div class="fields" id="tol_rooms">
            <ul>
                <li><label>Casert<input auto="auto" type="text" name="room" value="" /></label></li>
                <li><label>Tel<input auto="auto" type="text" name="phone" value="" /></label></li>
                <li><label>IP<input auto="auto" type="text" name="ip" value="" /></label></li>
            </ul>
        </div>

        {if !$user->isFemale() && $user->isAdmin()}
        <div class="fields"  id="tol_gender">
            <ul>
                <li><label>Fifilles<input auto="auto" type="checkbox" name="gender" value="woman"/></label></li>
            </ul>
        </div>
        {/if}

        <div class="fields" id="tol_send">
            <input type="submit" name="chercher" value="Chercher" />
        </div>
    </form>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
