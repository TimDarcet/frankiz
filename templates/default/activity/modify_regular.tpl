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

<div class="msg_proposal hide"> 
    {$msg}
</div>

<form enctype="multipart/form-data" method="post" action="activity/regular/modify" id="activity_modify">
    <div class="module">
        <div class="head">
           Sélectionner l'activité à modifier <span class="helper" target="activity/regular/modify" />
        </div>

        <div class="body">
            <table>
                {foreach from=$activities item=act}
                    <tr>
                        <td width=20%></td>
                        <td>
                            <input type="radio" name="aid" value="{$act->id()}" {if $aid == $act->id()}checked{/if}> {$act->title()}
                        </td>
                    </tr>
                {/foreach}

                <tr class="hide">
                    <td>
                        Modifier :
                    </td>
                    <td>
                        <input type="submit" name="send" value="Valider"/>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>

<div class="module" id="activity_show">
    {if $activity != null}
        {include file="activity/modify_regular_activity.tpl"|rel}
    {/if}
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
