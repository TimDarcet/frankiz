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

<div class="partner center">
    <a href="links/new">Créer un nouveau lien</a>
</div>

<div class="partner module">
    <div class="head">
        Administrer les liens utiles
    </div>
    <div class="body">
        {foreach from=$links.usefuls item=link}

            <div class="collapsible">
                <table>
                        <tr>
                            <td width="20%">
                                <a href="{$link->link()}"> {$link->label()} </a>
                            </td>
                            <td>
                                <div class="subsection">
                                    <div class="type">
                                        Description :
                                    </div>
                                    {$link->description()}
                                </div>
                                <div class="subsection">
                                    <div class="type">
                                        Commentaire :
                                    </div>
                                    {$link->comment()}
                                </div>
                            </td>
                        </tr>
                </table>
                <label class="subtitle" for="link{$link->id()}">Modification des données</label>
                <input type="checkbox" id="link{$link->id()}" />
                <form enctype='multipart/form-data' method='post' action='links/admin'>
                    <table>
                        <tr>
                            <td width="20%">
                                Nom :
                            </td>
                            <td  class="form">
                                <input type="text" name="label" value="{$link->label()}"
                                    required placeholder="Nom du partenaire"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Site :
                            </td>
                            <td class="form val">
                                <input type="url" name="link" value="{$link->link()}" required placeholder="URL"/>
                                <div class="validation">
                                    L'url donnée n'est pas valide.
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                               Rang :
                            </td>
                            <td class="form">
                                <input type="text" name="rank" value="{$link->rank()}"
                                    required placeholder="place du lien dans la liste"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Description :
                            </td>
                            <td class="form">
                                <textarea name='description' placeholder="Description"
                                    rows=7 cols=50>{$link->description()}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Commentaire (administrateur):
                            </td>
                            <td class="form">
                                <textarea name='comment' placeholder="Commentaire pour les administrateurs"
                                    rows=7 cols=50>{$link->comment()}</textarea>
                            </td>
                        </tr>
                    </table>

                    <input type='hidden' name='id' value='{$link->id()}'>
                    <input type='submit' class="button" name='modify' value='Enregistrer'>
                </form>
            </div>
        {/foreach}
    </div>
</div>



<div class="module partner">
    <div class="head">
        Administrer les partenaires
    </div>
    <div class="body">
        {foreach from=$links.partners|order:'rank':false item=link}
            <div class="collapsible">
                <table>
                    <tr>
                        <td width="20%">
                            {if $link->image()}
                                <a href="{$link->link()}">
                                    <img src='{$link->image()|image:'small'|smarty:nodefaults}' alt="Logo">
                                </a>
                            {/if}
                        </td>
                        <td>
                            <div class="label">
                                <a href="{$link->link()}"> {$link->label()} </a>
                            </div>
                            <div class="subsection">
                                <div class="type">
                                    Description :
                                </div>
                                {$link->description()}
                            </div>
                            <div class="subsection">
                                <div class="type">
                                    Commentaire :
                                </div>
                                {$link->comment()}
                            </div>
                        </td>
                    </tr>
                </table>
                <label class="subtitle" for="link{$link->id()}">Modification des données</label>
                <input type="checkbox" id="link{$link->id()}" />
                <form enctype='multipart/form-data' method='post' action='partners/admin'>
                    <table>
                        <tr>
                            <td width="20%">
                                Nom :
                            </td>
                            <td  class="form">
                                <input type="text" name="label" value="{$link->label()}"
                                    required placeholder="Nom du partenaire"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Site :
                            </td>
                            <td class="form val">
                                <input type="url" name="link" value="{$link->link()}" required placeholder="URL"/>
                                <div class="validation">
                                    L'url donnée n'est pas valide.
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                               Rang :
                            </td>
                            <td class="form">
                                <input type="text" name="rank" value="{$link->rank()}"
                                    required placeholder="place du lien dans la liste"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Logo :
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
                                <textarea name='description' placeholder="Description"
                                    rows=7 cols=50>{$link->description()}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Commentaire (administrateur):
                            </td>
                            <td class="form">
                                <textarea name='comment' placeholder="Commentaire pour les administrateurs"
                                    rows=7 cols=50>{$link->comment()}</textarea>
                            </td>
                        </tr>
                    </table>

                    <input type='hidden' name='id' value='{$link->id()}'>
                    <input type='submit' class="button" name='modify' value='Enregistrer'>
                </form>
            </div>
        {/foreach}
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
