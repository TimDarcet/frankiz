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

{if $group}

{$group->id()}
{$group->name()}
<ul>
{foreach from=$group->caste() item='caste'}
    <li>
    {$caste->id()}
    {assign var='uf' value=$caste->userfilter()}
    {if $uf}
        {$uf->export()|@json_encode}
    {/if}
    <ul>
        {foreach from=$caste->users() item='user'}
            {$user->id()}
        {/foreach}
    </ul>
    </li>
{/foreach}
</ul>

{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
