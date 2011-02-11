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

<div class="threecols">
    <div class="module">
        <div class="head">Les binets<span class="helper" target="groups/binet" /></div>
        <div class="body">
            {include file="groups/ns_groups.tpl"|rel ns="binet" groups=$binet user_groups=$user_binet}
        </div>
    </div>

    <div class="module">
        <div class="head">Les études<span class="helper" target="groups/study" /></div>
        <div class="body">
            {include file="groups/ns_groups.tpl"|rel ns="study" groups=$study user_groups=$user_study}
        </div>
    </div>

    <div class="module">
        <div class="head">Divers<span class="helper" target="groups/free" /></div>
        <div class="body">
            {include file="groups/ns_groups.tpl"|rel ns="free" groups=$free user_groups=$user_free}
        </div>
    </div>
</div>

{js src="groups.js"}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
