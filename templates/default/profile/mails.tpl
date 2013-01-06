{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2009-2013 Binet Réseau                                  *}
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

<div class="module">
    <div class="head">
        Compte Poly
    </div>
    <div class="body">
        {include file="wiki.tpl"|rel name='profile/mails/poly'}
        {if $user->poly()}
            Tu disposes de l'adresse {$user->poly()}@poly.polytechnique.fr <br />
            Sa gestion se fait sur <a href="http://poly.polytechnique.fr/">poly.polytechnique.fr</a>
        {else}
            Ton adresse poly est inconnue.
        {/if}
    </div>
</div>

<div class="module">
    <div class="head">
        Compte polytechnique.edu
    </div>
    <div class="body">
        {include file="wiki.tpl"|rel name='profile/mails/edu'}
        TODO
    </div>
</div>

<div class="module">
    <div class="head">
        Compte polytechnique.org
    </div>
    <div class="body">
        {include file="wiki.tpl"|rel name='profile/mails/org'}
        {if $xorgRegistered}
            Gestion sur <a href="https://www.polytechnique.org/emails">www.polytechnique.org/emails</a>
        {else}
            Tu n'as pas de compte sur <a href="http://www.polytechnique.org">www.polytechnique.org</a>
        {/if}
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
