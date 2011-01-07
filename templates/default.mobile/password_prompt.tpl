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

<div class="login">

    {if $platal->pl_self() != 'login'}
        <p>
            Authentification nécessaire.
        </p>
    {/if}
    
    <form enctype="multipart/form-data" method="post" nosolo action="{$globals->baseurl}/{$platal->pl_self()}">
        {xsrf_token_field}
        <div class="label">Adresse e-mail</div>

        <div class="field">
            <input type="text" name="username" value="{get_forlife_from_cookie}" />
            <br />
            &#064;
            <select name="domain">
                {select_domains}
            </select>
        </div>
        <div class="label">Mot de passe</div>
        <div class="field">
            <input type="password" name="password" value="" />
        </div>
        <div class="field">
            <input type="checkbox" checked="checked" name="remember" id="remember" />
            Se souvenir de moi
        </div>
        <input type="submit" name="start_connexion" value="Valider" class="btn"/>
    </form>
    
    {literal}
        <script>
        
        </script>
    {/literal}

</div>

{* vim:set et sw=4 sts=4 sws=4 enc=utf-8: *}
