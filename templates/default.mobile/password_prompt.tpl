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

<div class="login">

    {if $platal->pl_self() != 'login'}
        Authentification nécessaire.
    {/if}

    <form enctype="multipart/form-data" method="post" nosolo action="{$globals->baseurl}/{$platal->pl_self()}">
        {xsrf_token_field}
        <div class="wide-field-wrap">
            <input type="text" autocorrect="off" autocapitalize="off" placeholder="login" name="username" value="{get_forlife_from_cookie}" class="wide-field" />
        </div>
        &#64;
        <select name="domain">
            {select_domains}
        </select>
        <br>
        <div class="wide-field-wrap">
            <input type="password" autocorrect="off" autocapitalize="off" placeholder="mot de passe" name="password" value="" class="wide-field"/>
        </div>
        {if !t($remote_site)}{* No cookie for remote auth *}
            <input type="checkbox" {if $remip->is_student()}checked="checked"{/if} name="remember" id="remember" />
            Se souvenir de moi
            <br>
        {/if}
        <input type="submit" class="submit" value="Se connecter !">
    </form>
</div>

{* vim:set et sw=4 sts=4 sws=4 enc=utf-8: *}
