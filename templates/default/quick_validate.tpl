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
{*  This program is distributed in the hope tha0t it will be useful,       *}
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

<div>
{if $quick_validate|@count > 0}
    <div class="validate">
        À valider:
        <ul>
            {foreach from=$quick_validate item='validates'}
                {assign var='first' value=$validates->first()}
                {assign var='grp' value=$first->group()}
                {assign var='castes' value=$smarty.session.user->castes()}
                {assign var='groups' value=$castes->groups()}
                {assign var='group' value=$groups->get($grp)}
                <li>
                    Groupe {$group->label()} :
                    <a href="admin/validate/{$group->id()}">
                        {$validates->count()} requête{if $validates->count() > 1}s{/if}
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
{/if}
{if $quick_requests|@count > 0}
    <div class="requests">
        En attente:
        <ul>
            {foreach from=$quick_requests item='validate'}
                <li>Requête de type "{$validate->label()}" depuis {$validate->created()|age}</li>
            {/foreach}
        </ul>
    </div>
{/if}
</div>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
