{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009 Binet RÃ©seau                                       *}
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

<li uid="{$result->id()}" class="fiche">
    <div class="base">
        <div class="img"><a href="profile/photo/{$result->login()}"><img src="profile/photo/small/{$result->login()}" /></a></div>
        <div class="sports">
            {foreach from=$result->groups('sport') item=group}
                {$group->name()}
            {/foreach}
        </div>
        <div class="name">{$result->displayName()}</div>
        <div>


        </div>
        <hr />
    </div>
    <div class="more">
        <div class="associations">
            Binets:
            <ul>
                {assign var='binets' value=$result->groups('binet')}
                {assign var='dev_null' value=$binets->order('frequency')}
                {foreach from=$binets item='group'}
                    <li>{$group->frequency()} {$group->label()} {$result->rights($group)} {$result->comments($group)}</li>
                {/foreach}
            </ul>
        </div>
    </div>
</li>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
