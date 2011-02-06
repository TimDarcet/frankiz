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

{js src="qdj.js"}

<div class="msg">
    Attention : toutes les modifications sont prises en compte. <br />
    Pour déplanifier une QDJ, il suffit de supprimer la date.
</div>

<div class="msg hide">
</div>

<div class="module qdj">
    <div class="head">
        Administrer les QDJs
    </div>
    <div class="body">
        <div>
            Nous sommes le : {$smarty.now|date_format:"%Y-%m-%d"}
        </div>
        <div>
            Nb de QDJ planifiées : {$planned|@count}
        </div>
        <div>
            Nb de QDJ non planifiées : {$not_planned|@count}
        </div>

        <div class="title">
            Planifiées
        </div>
        <table>
            {foreach from=$planned item=qdj}
                <tr>
                    <td width="30%">
                        <form enctype="multipart/form-data" method="post" action="qdj/organize">
                            <input type=text name="qdj" id="{$qdj->id()}"
                                value="{$qdj->date()|datetime:'Y-m-d'}" placeholder='0000-00-00'/>
                            <input type="text" name="id" value="{$qdj->id()}" style="display:none;">
                            <input type=submit name="delete" value="Supprimer"/>
                            <input type="submit" name="send" value="Valider la planification" class="hide">
                        </form>
                    </td>
                    <td>
                        <div class="box">
                            {include file="qdj/preview.tpl"|rel}
                        </div>
                    </td>
                </tr>
            {/foreach}
        </table>

        <div class="title">
            Non planifiées
        </div>
        <table class="qdj_table">
            {foreach from=$not_planned item=qdj}
                <tr>
                    <td width="30%">
                        <form enctype="multipart/form-data" method="post" action="qdj/organize">
                            <input type=text name="qdj" id="{$qdj->id()}" placeholder='0000-00-00'/>
                            <input type="text" name="id" value="{$qdj->id()}" style="display:none;">
                            <input type=submit name="delete" value="Supprimer"/>
                            <input type="submit" name="send" value="Valider la planification" class="hide">
                        </form>
                    </td>
                    <td>
                        <div class="box">
                            {include file="qdj/preview.tpl"|rel}
                        </div>
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
