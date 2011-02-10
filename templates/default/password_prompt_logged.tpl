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

<div class="module login">
    <div class="head">
        Page sécurisée
     </div>
     <div class="body">
        <div class="comments">
            La page que tu as demandée est classée comme sensible.<br />
            Il est nécessaire de taper ton mot de passe pour y accéder,
            même avec l'accès permanent activé.
        </div>
        <br />

        <form enctype='multipart/form-data' method='post' action='{$smarty.server.REQUEST_URI}'>
            {xsrf_token_field}
            <input type="hidden" name="username" value="{$smarty.session.uid}" />
            <table>
                <tr>
                  <td>Connecté en tant que:</td>
                  <td>{$smarty.session.user->displayName()}</td>
                </tr>
                <tr>
                  <td>Mot de passe:</td>
                  <td><input type='password' name='password' value='' /></td>
                </tr>
                <tr>
                    <td colspan="2" class="send">
                        <input type="submit" name="start_connexion" value="Connexion" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
