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

{if $demande}
<span class='note'>
  Le mail a été envoyé avec succès à l'adresse {$email}. Il te permettra de te connecter une fois au site
  web Frankiz pour changer ton mot de passe ou choisir ton mot de passe si tu n'en as pas encore défini un.
</span>
{/if}
<form enctype='multipart/form-data' method='post' action='profil/mdp_perdu'>
  <span class='note'>
    Si tu souhaites créer ton compte Frankiz, ou si tu as perdu ton mot de passe, entre ton loginpoly.promo
    (par exemple dupont.2002) dans le champ ci-dessous. Tu recevras dans les minutes qui suivent un courriel
    te permettant d'accéder à la partie réservée de Frankiz. Une fois authentifié grâce au lien contenu dans
    le courriel, n'oublie pas de changer ton mot de passe.
  </span>
  <div class='formulaire'>
    <div>
      <span class='gauche'>login.promo :</span>
      <span class='droite'><input type='text' name='loginpoly' value='' /></span>
    </div>
    <div>
      <span class='boutons'>
        <input type='submit' name='valider' value='Valider' />
      </span>
    </div>
  </div>
</form>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
