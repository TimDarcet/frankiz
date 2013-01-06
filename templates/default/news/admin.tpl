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

{if t($msg) && $msg}
    <div class="msg_proposal">
        {$msg}
    </div>
{/if}

{if t($delete) && $delete}
    <div class="msg_proposal">
        L'annonce a été supprimée.
    </div>
{/if}

{if $news}
    <div class="module">
        <div class="head">
            <span class="helper" target="news/admin"></span>
           {$title}
        </div>
        <div class="body">
            <form enctype="multipart/form-data" method="post" action="news/admin/{$news->id()}">
                {xsrf_token_field}
                <table class="bicol">
                    {include file="validate/form.edit.news.tpl"|rel item=$news}
                    <tr>
                        <td>
                            <input type="submit" name="delete" value="Supprimer"
                                   onclick="return window.confirm(areyousure)" />
                        </td>
                        <td>
                            <input type="submit" name="modify" value="Modifier"
                                   onclick="return window.confirm(areyousure)" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
{/if}

{js src="news.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}