{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010-2013 Binet Réseau                                  *}
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

<div class="msg">
    Attention : toutes les modifications sont prises en compte. <br />
    Pour déplanifier une QDJ, il suffit de supprimer la date.
</div>

<div class="msg_qdj">
</div>

<div class="module qdj">
    <div class="head">
        Administrer les QDJs
    </div>
    <div class="body">
        <div>
            Nous sommes le : {$smarty.now|date_format:"%Y-%m-%d"}
        </div>

        <table>
            {foreach from=$qdjs item=qdj}
                <tr qdj_id="{$qdj->id()}">
                    <td width="30%">
                        <input type="text" class="date" name="qdj"
                               value="{if $qdj->date()}{$qdj->date()|datetime:'Y-m-d'}{/if}"/><br />
                        <input type="button" name="unplan" value="Déplanifier"/>
                        <input type="button" name="delete" value="Supprimer"/>
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

{js src="qdj_admin.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
