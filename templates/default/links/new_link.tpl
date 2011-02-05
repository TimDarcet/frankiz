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

{if t($msg)}
    <div class="msg">
        {$msg|smarty:nodefaults}
    </div>
{/if}

{if t($err)}
    <div class="error">
        {$err|smarty:nodefaults}
    </div>
{/if}

<div class="partner center">
    <a href="links/admin">Administrer les liens</a>
</div>

<form enctype='multipart/form-data' method='post' action='links/new'>
    <div class='partner module'>
        <div class="head">
            Nouveau lien
        </div>
        <div class="body">
            <table>
                <tr>
                    <td width="20%">
                        Nom :
                    </td>
                    <td  class="form">
                        <input type="text" name="label" value="{$label}" required placeholder="Nom du lien"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Site :
                    </td>
                    <td class="form val">
                        <input type="url" name="link" value="{$link}" required placeholder="URL"/>
                        <div class="validation">
                            L'url donnée n'est pas valide.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="20%">
                        Type :
                    </td>
                    <td class="form">
                        <select name="type">
                            <option value="partners">Partenaire</option>
                            <option value="usefuls">Lien utile</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        Logo (pour les partenaires) :
                    </td>
                    <td class="form">
                        <input type="hidden" id="MAX_FILE_SIZE" value="200000">
                        <input type="file" name="image"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Description :
                    </td>
                    <td class="form">
                        <textarea name='description' placeholder="Description" rows=7 cols=50>{$description}</textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        Commentaire (administrateur):
                    </td>
                    <td class="form">
                        <textarea name='comment' placeholder="Commentaire pour les administrateurs" rows=7 cols=50>{$comment}</textarea>
                    </td>
                </tr>
            </table>

            <input type='submit' class="button" name='create' value='Enregistrer'>
        </div>
    </div>
</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
