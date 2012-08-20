{**************************************************************************}
{*  Copyright (C) 2004-2012 Binet Réseau                                  *}
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

{assign var=ips value=$room->ips()}
{assign var=groups value=$room->groups()}
{assign var=users value=$room->users()}
<div class="threecols">
    <div class="module">
        <div class="head">
            {$room->comment()} {$room->id()}
        </div>
        <div class="body">
            <div class="phone">Téléphone : {$room->phone()}</div>
            {if $ips->count() > 0}
                <div class="ips">
                    Addresses IP
                    <ul>
                        {foreach from=$ips item=ip}
                            <li>{$ip->id()}{if $ip->comment()} ({$ip->comment()}){/if}</li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
            {if $groups->count() > 0}
                <div class="door">Porte : {if $room->open()}ouverte{else}fermée{/if}</div>
            {/if}
        </div>
    </div>
    {if $groups->count() > 0}
        <div class="module">
            <div class="head">
               {if $groups->count() >= 2}Groupes{else}Groupe{/if}
            </div>
            <div class="body">
                {foreach from=$groups item=g}
                    <div class="group">{$g|group:both}</div>
                {/foreach}
            </div>
        </div>
    {/if}
    {if $users->count() > 0}
        <div class="module">
            <div class="head">
                {if $users->count() >= 2}Élèves{else}Élève{/if}
            </div>
            <div class="body">
                {foreach from=$users item=u}
                    <div class="group">{$u|user:both}</div>
                {/foreach}
            </div>
        </div>
    {/if}
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
