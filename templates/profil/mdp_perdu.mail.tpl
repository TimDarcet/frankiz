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

{config_load file="mails.conf" section="mdp_perdu"}
{if $mail_part eq "head"}
  {from full=#from#}
  {subject text="[Frankiz] Création de compte / Perte de mot de passe"}
{/if}
{if $mail_part eq "html"}
<b>Bonjour,</b><br />
<br />
Pour te connecter sur Frankiz, il te suffit de cliquer sur le lien ci-dessous: <br />
<a href='{$globals->baseurl}/profil/fkz?uid={$uid}&hash={$hash}'>{$globals->baseurl}/profil/fkz?uid={$uid}&hash={$hash}</a><br />
<br />
N'oublie pas ensuite de modifier ton mot de passe.<br />
<br />
Très cordialement,<br />
Le BR.
{/if}
{if $mail_part eq "text"}
Bonjour,

Pour te connecter sur Frankiz, il te suffit de te rendre à l'adresse suivante :
{$globals->baseurl}/profil/fkz?uid={$uid}&hash={$hash}

N'oublie pas ensuite de modifier ton mot de passe.

Très cordialement,
Le BR.
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
