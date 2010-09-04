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

{if $step == 'ask'}

    <form enctype='multipart/form-data' method='post' action='profile/recovery'>
      <span class='note'>
        Si tu souhaites créer ton compte Frankiz, ou si tu as perdu ton mot de passe, entre ton adresse e-mail 
        donnée par ton écoleé (par exemple jean.dupont@polytechnique.edu) dans le champ ci-dessous. 
        Tu recevras dans les minutes qui suivent un courriel te permettant d'aller changer ton mot de passe.
      </span>
      <div class='formulaire'>
        <div>
          <span class='gauche'>Adresse e-mail :</span>
          <span class='droite'><input type='text' name='mail' value='' /></span>
        </div>
        <div>
          <span class='boutons'>
            <input type='submit' name='valider' value='Valider' />
          </span>
        </div>
      </div>
    </form>

{elseif $step == 'mail'}

    <span class="note">
      Le mail a été envoyé avec succès à l'adresse {$email}. Il te permettra de te connecter une fois au site
      web Frankiz pour changer ton mot de passe ou choisir ton mot de passe si tu n'en as pas encore défini un.
    </span>

{elseif $step == 'expired'}

    <span class="error">
      Ce code d'ouverture de session par mail est expiré.
    </span>

{elseif $step == 'password'}

    <span class="error">
      Un nouveau mot de passe temporaire vous a été envoyé par mail.
    </span>

{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
