{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010 Binet Réseau                                       *}
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

{js src="activities.js"}

<div class="msg hide">
    {$msg}
</div>

{if $delete}
    <div class="msg">
        L'activité a été supprimée.
    </div>

{else}
    <form enctype="multipart/form-data" method="post" action="activity/modify" id="activity_modify">
        {xsrf_token_field}
        <div class="module">
            <div class="head">
                <span class="helper" target="activity/modify"> </span>
                Sélectionner l'activité à modifier
            </div>

            <div class="body">
                <table>
                    {foreach from=$activities item=act}
                        <tr>
                            <td width=20%></td>
                            <td>
                                <input type="radio" name="admin_id" value="{$act->id()}" {if $id == $act->id()}checked{/if}>
                                    {$act->title()} du {$act->date()|date_format} à {$act->hour_begin()}
                                     au {$act->end()|date_format} à {$act->hour_end()}
                            </td>
                        </tr>
                    {/foreach}

                    <tr class="hide">
                        <td>
                            Sélectionner :
                        </td>
                        <td>
                            <input type="submit" name="send" value="Valider"/>
                        </td>
                    </tr>
                </table>
            </div>
        </div>


        <div class="module" id="modify_show">
            {if $activity != null}
                {if $activity->regular()}
                    {include file="activity/modify_instance_regular.tpl"|rel}
                {else}
                    {include file="activity/modify_punctual.tpl"|rel}
                {/if}
            {/if}
        </div>
    </form>
{/if}


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}