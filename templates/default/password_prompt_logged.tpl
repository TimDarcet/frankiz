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

<h1>Page sécurisée</h1>

<div>
  La page que tu as demandée est classée comme sensible. Il est nécessaire de taper ton mot de passe
  pour y accéder, même avec l'accès permanent activé.
</div>
<br />

<form enctype='multipart/form-data' method='post' action='{$smarty.server.REQUEST_URI}'>
{xsrf_token_field}
  <input type="hidden" name="username" value="{$smarty.session.uid}" />
  <h2><span>Connexion</span></h2>
  <div class='formulaire'>
    <div>
      <span class='gauche'>Identifiant:</span>
      <span class='droite'>{$smarty.session.user->bestalias}</span>
    </div>
    <div>
      <span class='gauche'>Mot de passe:</span>
      <span class='droite'><input type='password' name='password' value='' /></span>
    </div>
    <div>
      <span class='boutons'><input type='submit' name='start_connexion' value='Connexion' /></span>
    </div>
  </div>
</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
