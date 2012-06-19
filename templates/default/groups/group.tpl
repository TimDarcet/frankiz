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

<div class="top">
    <div>
        <div>
            <input type="hidden" id="gid" name="gid" value="{$group->id()}" />
            {if $group->image()}
                <a href="{$group->image()|image:'full'}" fancy="fancy"><img src="{$group->image()|image:'big'}" /></a>
            {/if}
            <div class="label">{$group->label()}</div>
            {if $group->web()}
                <div class="web">Web : <a href="{$group->web()}">{$group->web()}</a></div>
            {/if}
            {if $group->wikix()}
                <div class="web">WikiX : <a href="http://wikix.polytechnique.org/{$group->wikix()}">{$group->wikix()}</a></div>
            {/if}
            {if $group->mail()}
                <div class="mail">Mail : {$group->mail()}</div>
            {/if}
            <div class="description">{$group->description()|miniwiki|smarty:nodefaults}</div>
            {assign var='premises' value=$group->premises()}
            {if sizeof($premises) > 0}
                <div class="locaux" gid="{$group->id()}">
                    {if sizeof($premises) > 1}Locaux : {else}Local : {/if}
                    {if sizeof($premises) > 1}<ul>{/if}
                    {foreach from=$premises item='premise' key='rid'}
                        {if sizeof($premises) > 1}<li>{/if}
                        <div class="{if $premise.open}open{else}close{/if}" rid="{$rid}">
                            <div class="traffic_light_switcher{if $roomMaster} room_master" title="{if $premise.open}Fermer{else}Ouvrir{/if} le local{/if}"></div>
                            {$premise.label}
                        </div>
                        {if sizeof($premises) > 1}</li>{/if}
                    {/foreach}
                    {if sizeof($premises) > 1}</ul>{/if}
                </div>
            {/if}
            <br class="clear" />
        </div>
    </div>
</div>

<div class="tools">
    <div>
        <div>
            <h1>Outils</h1>
            <ul>
                <li><a href="chat/{$group->name()}">Salon de discussion</a></li>
                <li><a href="http://{$group->name()}.platalpad.binets.fr" target="_blank">Platalpad</a></li>
            </ul>
        </div>
    </div>
</div>

{if $smarty.session.auth >= AUTH_INTERNAL}
    <table class="bottom"><tr>
        <td class="users">
            <div class="me">
            {if $smarty.session.auth >= AUTH_COOKIE}
                {if $user|hasRights:$group:'everybody'}
                    <div class="comments">
                        <label>Commentaire<br />
                        <input type="text" name="comments" value="{$user->comments($group)}" />
                    </label>
                    </div>

                    <div>
                    {if $user|hasRights:$group:'admin'}
                        <div>
                            <span class="edit"></span>
                            <a href="groups/admin/{$group->name()}">Administrer le groupe</a>
                        </div>
                    {elseif $group->leavable()}
                        {if !$user|hasRights:$group:'restricted'}
                            <div>
                                <span class="rights member"></span>
                                <a onclick="return confirm(areyousure);" href="groups/subscribe/{$group->id()}/member?token={xsrf_token}">Devenir membre</a>
                            </div>
                        {/if}
                        <div>
                            <span class="remove_participant"></span>
                            <a onclick="return confirm(areyousure);" href="groups/unsubscribe/{$group->id()}?token={xsrf_token}">Quitter le groupe</a>
                        </div>
                    {/if}
                    </div>
                {else}
                    <div>
                        <span class="rights friend"></span>
                        <a onclick="return confirm(areyousure);" href="groups/subscribe/{$group->id()}?token={xsrf_token}">Devenir sympathisant</a>
                    </div>
                    <div>
                        <span class="rights member"></span>
                        <a onclick="return confirm(areyousure);" href="groups/subscribe/{$group->id()}/member?token={xsrf_token}">Devenir membre</a>
                    </div>
                {/if}
                {if $user->isWeb() && !$user|hasRights:$group:'admin'}
                    <span class="webmaster" >
                        <span class="edit"></span>
                        <a href="groups/admin/{$group->name()}">Administrer le groupe</a>
                    </span>
                {/if}
            {/if}
            </div>

            <div class="members">
                <div class="tol">
                    <a href="tol?binets={$group->id()}">Voir dans le TOL</a>
                </div>

                <div class="filters">
                    <form name="filters">
                    <input type="hidden" name="gid" value="{$group->id()}" />
                    <label>Filtrer sur la promo</label>
                    {include file="groups_picker.tpl"|rel id="promo" ns="promo" check=-1 already=$user->defaultFilters()|filter:'ns':'promo' order="name"}
                    <input type="hidden" name="admin_page" value="1" />
                    <input type="hidden" name="member_page" value="1" />
                    <input type="hidden" name="friend_page" value="1" />
                    </form>
                </div>

                <ul class="rights_users">
                    <li>
                        <span class="rights admin"></span>
                        <span class="total"></span>
                         administrateur<span class="plural">s</span>
                        <span class="page"></span>
                        <ul class="admin">
                        </ul>
                    </li>

                    <li>
                        <span class="rights member"></span>
                        <span class="total"></span>
                         membre<span class="plural">s</span>
                        <span class="page"></span>
                        <ul class="member">
                        </ul>
                    </li>

                    <li>
                        <span class="rights friend"></span>
                        <span class="total"></span>
                         sympathisant<span class="plural">s</span>
                        <span class="page"></span>
                        <ul class="friend">
                        </ul>
                    </li>
                </ul>
            </div>
        </td>

        <td class="datas">
            <div class="news">
                <table>

                </table>
            </div>
        </td>
    </tr></table>

    {js src="group_see.js"}
    {literal}
    <script>
        $(function() {
            users.search();
            news.search();
        });
    </script>
    {/literal}
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
