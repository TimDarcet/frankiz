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

{if $referer || $platal->pl_self() neq 'login'}
<h1>
  Accès restreint
</h1>
<p>
  Bonjour,<br />
  la page que tu as demandée (<strong>{if $referer}{$smarty.server.HTTP_REFERER}{else}{$globals->baseurl}/{$platal->pl_self()}{/if}</strong>) nécessite une authentification.
</p>
{else}
<h1>
  Connexion
</h1>
{/if}

<form enctype='multipart/form-data' method='post' action='{$smarty.server.REQUEST_URI}'>
{xsrf_token_field}
  <h2><span>Connexion</span></h2>
  <div class='formulaire'>
    <div>
      <span class='gauche'>Identifiant:</span>
      <span class='droite'><input type='text' name='username' value='{get_forlife_from_cookie}' />&nbsp;@&nbsp;<select name="domain">
{select_domains}
</select></span>
    </div>
    <div>
      <span class='gauche'>Mot de passe:</span>
      <span class='droite'><input type='password' name='password' value='' /></span>
    </div>
    <div>
    <input type="checkbox" name="remember" id="remember" /><label for="remember">Se souvenir de moi (Cookie)</label>
    </div>
    </div>
  </div>
  <div>
    <span class='boutons'><input type='submit' name='start_connexion' value='Connexion' /></span>
  </div>
  <p>
    Si tu as oublié ton mot de passe ou que tu n'as pas encore de compte, clique
    <a href='profil/mdp_perdu'>ici</a>.
  </p>
</form>

{literal}
<script type="text/javascript">
  <!--
  // Activate the appropriate input form field.
  if (document.forms.login.username.value == '') {
    document.forms.login.username.focus();
  } else {
    document.forms.login.password.focus();
  }
  // -->
</script>
{/literal}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
