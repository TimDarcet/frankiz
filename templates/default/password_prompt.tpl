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
<p>
    La page que tu as demandée (<strong>{if $referer}{$smarty.server.HTTP_REFERER}{else}{$globals->baseurl}/{$platal->pl_self()}{/if}</strong>) nécessite une authentification.
</p>
{/if}

<form class="login" enctype='multipart/form-data' method='post' action='{$smarty.server.REQUEST_URI}'>
{xsrf_token_field}
    <fieldset>
        <legend>Connexion</legend>
        <table>
            <tr>
                <td><label for="username">Identifiant:</label></td>
                <td>
                    <input type="text" name="username" value="{get_forlife_from_cookie}" />
                    &nbsp;@&nbsp;
                    <select name="domain">
                        {select_domains}
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="password">Mot de passe:</label></td>
                <td>
                    <input type="password" name="password" value="" />
                </td>
            </tr>
        </table>

        <label class="remember">
            <input type="checkbox" {if $casertConnected}checked="checked"{/if} name="remember" id="remember" />
            Se souvenir de moi (Cookie)
        </label>
                    
        <div class="send">
            <input type="submit" name="start_connexion" value="Connexion" />
        </div>

        <p class="forgot">
          Si tu as oublié ton mot de passe ou que tu n'as pas encore de compte, <a href="profil/mdp_perdu">clique ici</a>.
        </p>
    </fieldset>
</form>

{literal}
<script type="text/javascript">
  <!--
  // Activate the appropriate input form field.
  if ($("form.login input[name='username']").val() == '') {
    $("form.login input[name='username']").focus();
  } else {
    $("form.login input[name='password']").focus();
  }
  // -->
</script>
{/literal}

{* vim:set et sw=4 sts=4 sws=4 enc=utf-8: *}
