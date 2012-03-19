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

<div class="formulaire">
    <form enctype="multipart/form-data" method="post" action="tol/">
        <input type="text" name="free" value="{if t($smarty.request.free)}{$smarty.request.free}{/if}" />
        <input name="go" value="Consulter" type="hidden">
        <input name="search" value="" type="hidden">
        <input name="terms" id="terms" value="" type="hidden">
        <input type="hidden" name="submitted" id="submitted" value="1" />
        <input type="hidden" name="method" id="method" value="name" />
        <input type="hidden" name="page" id="page" value="1" />

        <div>
            <input name="tol" value="TOL" type="submit">
            <input name="ok" value="Wikix" type="submit"
                   onclick="var $form = $(this).closest('form');$form.find('[name=search]').val($form.find('[name=free]').val());$form.attr('action', 'http://wikix.polytechnique.org/eleves/wikix/Sp%C3%A9cial:Recherche')">
            {if $smarty.session.auth >= AUTH_COOKIE && IP::is_internal()}
              <input name="ok" value="Fruit" type="submit"
                   onclick="var $form = $(this).closest('form');$form.find('[name=terms]').val($form.find('[name=free]').val());$form.attr('action', 'http://fruit/search.php');$form.attr('method', 'get')">
            {/if}
        </div>
    </form>
</div>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
