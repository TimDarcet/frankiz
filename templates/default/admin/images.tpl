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

<div id="options">

    <div class="slide_form minimodule">
        <div class="head">
            Rechercher une image
        </div>
        <div class="body">
            <form enctype="multipart/form-data" method="post" accept-charset="UTF-8" action="{$smarty.server.REQUEST_URI}">
                <fieldset>
                <ul>
                    <li><label>IID<input type="text" name="iid" value="" /></label></li>
                    <li><label>Commentaire<textarea name="comment"></textarea></label></li>
                </ul>
                </fieldset>
                <fieldset class="submit">
                    <input type="submit" value="Chercher" />
                </fieldset>
            </form>
        </div>
    </div>
    
    <div class="slide_form minimodule">
        <div class="head">
            Envoyer une image
        </div>
        <div class="body">
            <form enctype="multipart/form-data" method="post" accept-charset="UTF-8" action="{$smarty.server.REQUEST_URI}">
                <fieldset>
                <ul>
                    <li><label>Fichier<input type="file" name="file" /></label></li>
                </ul>
                </fieldset>
                <fieldset>
                <ul>
                    <li><label>Commentaire<textarea name="comment"></textarea></label></li>
                </ul>
                </fieldset>
                <fieldset class="submit">
                    <input type="submit" value="Envoyer" />
                </fieldset>
            </form>
        </div>
    </div>
    
</div>

<table id="images">
{foreach from=$images item=image}
    <tr>
        <td>{$image->id()}</td>
        <td><a nosolo="nosolo" href="admin/image/{$image->id()}"><img style="max-width:150px; max-height:150px" src="admin/image/{$image->id()}?small" /></a></td>
        <td>{$image->mime()}</td>
        <td>{$image->x()}</td>
        <td>{$image->y()}</td>
        <td>{$image->size()|bytes_format}</td>
        <td>{$image->comment()}</td>
    </tr>
{/foreach}
</table>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
