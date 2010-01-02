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

<h1>
    Déconnexion effectuée
</h1>

{if $smarty.cookies.BRhash}

<p>Tu as activé les cookies, donc cette déconnexion ne t'empêche pas d'utiliser la plupart des fonctionnalités du site.</p>
<p>
Tu peux donc aussi te <a href='exit/forget'>déconnecter complètement</a>
</p>

<p>En outre, pour faciliter ta prochaine connexion, ton adresse email est mémorisée par ton navigateur. Si tu utilises un ordinateur public ou que tu désires l'utiliser, tu peux <a href='exit/forgetall'>supprimer cette information et te déconnecter complètement</a>
</p>

{elseif $smarty.cookies.BRuid}
<p>
Ton adresse email est toujours en mémoire dans ton navigateur afin de faciliter ta prochaine
connexion. Si tu utilises un ordinateur public ou que tu désires l'effacer, tu peux
<a href='exit/forgetuid'>supprimer cette information</a>.
</p>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
