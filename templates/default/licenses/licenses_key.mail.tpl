{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2011-2013 Binet Réseau                                  *}
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


Voici {if !$multiple}ta clé{/if}{if $multiple}tes clés{/if} de licence{if !$multiple} pour {$keys[0]->softwareName()}{/if} :

-------------------------------------------------------------------------
{foreach from=$keys item=k}
{$k->key()}{if $multiple} ({$k->softwareName()}){/if}
{/foreach}

-------------------------------------------------------------------------

Tu peux télécharger {$keys[0]->softwareName()} sur ftp://miroir/windows/msdnaa/

Cordialement,
Le BR

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
