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

{if $software_rare}
<p>Nous ne disposons que d'un nombre de clés limité pour le logiciel que tu demandes. Il nous faut donc une raison valable pour t'attribuer une clé.</p>
{/if}

{if $already_has}
<p>Une clé t'a déjà été attribuée pour ce logiciel.
<form action="licenses/final" method="POST">
    <input type="hidden" name="software" value="{$software}" />
    <input type="submit" name="resend" value="Recevoir à nouveau ma clé" />
</form>
</p>
<p>Si tu désires obtenir une nouvelle clé, merci de préciser la raison de ta demande.</p>
{/if}

<p>Raison de ta demande :</p>
<form action="licenses/final" method="POST">
    <textarea name="reason"></textarea>
    {if $already_has}<input type="hidden" name="new_key" />{/if}
    <input type="hidden" name="software" value="{$software}" />
    <input type="submit" name="final" value="Envoyer la demande" />
    <input type="submit" name="disagree" value="Tout annuler" />
</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
