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

{if $isHTML}

<b>Bonjour,</b>
<br />
<br />
Pour te connecter sur Frankiz, il te suffit de cliquer sur le lien ci-dessous:
<br />
<a href="{$globals->baseurl}/profile/recovery?uid={$uid}&hash={$hash}">{$globals->baseurl}/profile/recovery?uid={$uid}&hash={$hash}</a><br />
<br />
N'oublie pas ensuite de modifier ton mot de passe.
<br />
<br />
Très cordialement,
<br />
Le BR.
    
{else}

Bonjour,

Pour te connecter sur Frankiz, il te suffit de te rendre à l'adresse suivante :
{$globals->baseurl}/profile/recovery?uid={$uid}&hash={$hash}

N'oublie pas ensuite de modifier ton mot de passe.

Très cordialement,
Le BR.

{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
