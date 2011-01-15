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

        <input type="text" autocorrect="off" autocapitalize="off" placeholder="login" name="username" value="{get_forlife_from_cookie}" />
        <br>
        &#64;
        <select name="domain">
            {select_domains}
        </select>
        <br>
        <input type="password" autocorrect="off" autocapitalize="off" placeholder="mot de passe" name="password" value="" />
        <br>
        <input type="checkbox" {if $smarty.session.auth >= AUTH_STUDENT}checked="checked"{/if} name="remember" id="remember" />
        Se souvenir de moi
        <br>
        <input type="submit" class="submit" value="Se connecter !">
    </form>
</div>

{* vim:set et sw=4 sts=4 sws=4 enc=utf-8: *}
