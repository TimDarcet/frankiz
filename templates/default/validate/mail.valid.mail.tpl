{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010-2013 Binet Réseau                                  *}
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

{if $text == null}
Ton mail vient d'être accepté. Il a va bientôt être envoyé.
{$comm|smarty:nodefaults}


Merci de ta participation
{else}
Ton mail n'a pas été accepté pour la raison suivante :
{$comm|smarty:nodefaults}


Voici le mail que tu avais proposé :
{$text|smarty:nodefaults}


Désolé
{/if}

Cordialement,
Les administrateurs du groupe "{$targetGroup->label()}"

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
