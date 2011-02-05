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

{*if $admin*}
    <div class="partner center">
        <a href="links/admin">Administrer les liens</a> <br/>
        <a href="links/new">Créer un nouveau lien</a>
    </div>
{*/if*}

{if $type=='usefuls'}
    <div class="module partner">
        <div class="head">
            Liens Utiles
        </div>
        <div class="body">
            <table>
                <tr>
                    <th>
                        Lien
                    </th>
                    <th>
                        Description
                    </th>
                </tr>
                {foreach from=$links|order:'rank':false item=link}
                    <tr>
                        <td width="20%">
                            <a href="{$link->link()}"> {$link->label()} </a>
                        </td>
                        <td>
                            {$link->description()}
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    </div>

{elseif $type=='partners'}
    <div class="module partner">
        <div class="head">
            Partenaires
        </div>
        <div class="body">
            <table>
                {foreach from=$links|order:'rank':false item=link}
                    <tr>
                        <td width="20%">
                            {if $link->image()}
                                <a href="{$link->link()}"> {$link->image()|image:'full'|smarty:nodefaults} </a>
                            {/if}

                        </td>
                        <td>
                            <div class="label">
                                <a href="{$link->link()}"> {$link->label()} </a>
                            </div>
                            <div class="subsection">
                                {$link->description()}
                            </div>
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    </div>

{else}
    <div class="error">
        La page demandée n'existe pas.<br />
        <a href="links/usefuls">Liens utiles</a> <br/>
        <a href="links/partners">Partenaires</a>
    </div>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
