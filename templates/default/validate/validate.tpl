{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2003-2010 Polytechnique.org                             *}
{*  http://opensource.polytechnique.org/                                  *}
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

{*  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA               *}
{*                                                                        *}
{**************************************************************************}

<h1>Requêtes à destination de « {$group->label()} »</h1>

{include file="wiki.tpl"|rel name='validate/help'}

{if $msg}
    <div class="msg_proposal">
        {$msg}
    </div>
{/if}

{if $val->count() == 0}

    <div class="msg_proposal">
        Il n'y a rien à valider
    </div>

{else}
    <ul>
    {foreach from=$val item=valid}
        <li class="validate">
            {assign var='writer' value=$valid->writer()}
            {assign var='group' value=$valid->group()}
            {assign var='item' value=$valid->item()}

            <div class="title">
                <table><tr>
                    <td class="writer" title="Utilisateur à l'origine de la requête">{$writer|user}</td>
                    <td class="type" title="Type de la requête">{$valid->label()}</td>
                    <td class="age" title="Temps d'attente">{$valid->created()|age}</td>
                </tr></table>
            </div>

            <div class="more {if $valid->id() == $validation}show{/if}">
            <form enctype="multipart/form-data" method="post" action="admin/validate/{$group->name()}">
                <input type="hidden" name="val_id" value="{$valid->id()}" />
                {if $logged && $user->isWeb()}
                    <div>
                        {include file="wiki.tpl"|rel name='validate/rules/'|cat:$valid->type() class="webmaster"}
                    </div>
                {/if}

                <div>
                    <span class="created">Date de demande: {$valid->created()}</span>
                    Demande de {$writer->fullName()}
                </div>

                <div class="pair">
                    <div class="click">
                        Commentaires (entre administrateurs)
                    </div>
                    <table class="hide show">
                        {foreach from=$item->comments() item=c}
                        <tr>
                            <td width=20%>
                                {$c.name|smarty:nodefaults}
                            </td>
                            <td>
                                {$c.com|smarty:nodefaults}
                            </td>
                        </tr>
                        {/foreach}
                        <tr>
                            <td width=20%>
                                Commentaire pour les administrateurs :
                            </td>
                            <td>
                                <div>
                                    <textarea name="comm" class="text_validate" placeholder="C'est ici qu'il faut mettre des remarques intelligentes ..."></textarea>
                                </div>
                                <input type="submit" name="add_comm" class="addcom_validate"  value="Ajouter un commentaire" />
                            </td>
                        </tr>
                    </table>
                </div>

                {if $item->show()}
                    <div class="impair">
                        <div class="click">
                            Informations
                        </div>
                        <table class="hide show">
                            {include file=$item->show()|rel}
                        </table>
                    </div>
                {/if}

                {if $item->editor()}
                    <div class="pair">
                        <div class="click">
                            Editer
                        </div>
                        <table class="hide">
                            {include file=$item->editor()|rel is_validation=true}
                            <tr>
                                <td width=20%></td>
                                <td>
                                    <input type="submit" name="edit"   value="Éditer" />
                                </td>
                            </tr>
                        </table>
                    </div>
                {/if}

                <table class="{if $item->editor()}impair{elseif $item->show()}pair{else}impair{/if}">
                    <tr>
                        <td width=20%>
                            Réponse :
                        </td>
                        <td>
                            <textarea name="ans"></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td width="20%">
                        </td>
                        <td>
                            {if $item->refuse()} <input type="submit" name="delete" value="Refuser" /> {/if}
                            <input type="submit" name="accept"   value="Valider" />
                        </td>
                    </tr>
                </table>
            </form>
            </div>

        </li>
    {/foreach}
    </ul>

    {js src="validate.js"}
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
