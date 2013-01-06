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

{if $step == 'ask'}

    <form enctype='multipart/form-data' method='post' action='profile/recovery'>
        {if $error != null}
            <div class="error">
                L'email donné est incorrect.
            </div>
        {/if}
        <fieldset class="recovery profile">
            Si tu souhaites créer ton compte Frankiz, ou si tu as perdu ton mot de passe, entre l'adresse e-mail
            donnée par ton école (par exemple jean.dupont@polytechnique.edu) dans le champ ci-dessous. <br/>
            Tu recevras dans les minutes qui suivent un courriel te permettant d'aller changer ton mot de passe.
            <table>
                <tr>
                    <td width="40%" class="right">
                        Adresse e-mail :
                    </td>
                    <td>
                        <input type='text' name='mail' value='' />
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type='submit' name='valider' value='Valider' />
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
{elseif $step == 'mail'}

    <div class="recovery note">
        Le mail a été envoyé avec succès à l'adresse {$email}. Il te permettra de te connecter une fois au site
        web Frankiz pour changer ton mot de passe ou choisir ton mot de passe si tu n'en as pas encore défini un.
    </div>

{elseif $step == 'expired'}

    <div class="error">
      Ce code d'ouverture de session par mail est expiré.
    </div>

{elseif $step == 'password'}

    <div class="recovery note">
        Un nouveau mot de passe temporaire vous a été envoyé par mail.
    </div>

{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
