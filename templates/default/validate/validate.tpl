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

{foreach from=$val item=valid}
<div class="validate">
<form enctype="multipart/form-data" method="post" action="admin/validate/">
    <div class="title">
        Validation de : {$valid->type}
    </div>
    
    <table>
    <tr>
        <td width=20%>
            Demandeur :
        </td>
        <td>
            {$valid->user->displayName()}
        </td>
    </tr>
    
    <tr>
        <td>
            Date de demande :
        </td>
        <td>
            {$valid->created}
        </td>
    </tr>
    </table>
    
    <div class="click subtitle">
        Informations
    </div>
    <table class="hide show">
        {include file=$valid->item->show()|rel}
    </table>
    
    
    {if $valid->item->editor()}
        <div class="click subtitle">
            Editer
        </div>
        <table class="hide">
            {include file=$valid->item->editor()|rel}
            <tr>
                <td width=20%></td>
                <td>
                    <input type="submit" name="edit"   value="Éditer" />
                </td>
            </tr>
        </table>
    {/if}
    
    <div class="click subtitle">
        Commentaires (entre administrateurs)
    </div>
    <table class="hide">
        {foreach from=$valid->item->comments item=c}
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
            <td width=20%> </td>
            <td>
                <div>
                    <textarea name="comm"></textarea>
                </div>
                <input type="submit" name="add_comm"   value="Ajouter un commentaire" />
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td width=20%>
                Réponse :
            </td>
            <td>
                <textarea name="ans"></textarea>
            </td>
        </tr>
    
        <tr>
            <td> </td>
            <td>
                <input type="hidden" name="id"    value="{$valid->id()}" />
                <input type="submit" name="refuse"   value="Refuser" />
                <input type="submit" name="accept"   value="Valider" />
            </td>
        </tr>
    </table>
</form>
</div>
{/foreach}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
