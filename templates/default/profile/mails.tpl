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


<h4>Compte Poly</h4>
    <p>
        {include file="wiki.tpl"|rel name='profile/mails/poly'}
        {if $user->poly()}
            {$user->poly()}@poly.polytechnique.fr<br />
            Gestion sur <a href="http://poly.polytechnique.fr/">poly.polytechnique.fr</a>
        {else}
            Ton adresse est inconnue.
        {/if}
    </p>

<h4>Compte polytechnique.edu</h4>
    <p>
        {include file="wiki.tpl"|rel name='profile/mails/edu'}
        TODO
    </p>

<h4>Compte polytechnique.org</h4>
    <p>
        {include file="wiki.tpl"|rel name='profile/mails/org'}
        {if $xorgRegistered}
            Gestion sur <a href="https://www.polytechnique.org/emails">www.polytechnique.org/emails</a>
        {else}
            Tu n'as pas de compte sur <a href="http://www.polytechnique.org">www.polytechnique.org</a>
        {/if}
    </p>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
