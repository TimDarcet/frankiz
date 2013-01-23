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

<div class="formulaire">
    <form enctype="multipart/form-data" method="post" action="tol/">
        <input type="text" name="free" value="{if t($smarty.request.free)}{$smarty.request.free}{/if}" />
        <input name="go" value="Consulter" type="hidden">
        <input name="search" value="" type="hidden">
        <input type="hidden" name="search_line">
        <input type="hidden" name="type" value="all">

        <div>
            <input name="tol" value="TOL" type="submit">
            <input name="ok" value="Wikix" type="submit"
                   onclick="var $form = $(this).closest('form');$form.find('[name=search]').val($form.find('[name=free]').val());$form.attr('action', 'http://wikix.polytechnique.org/eleves/wikix/Sp%C3%A9cial:Recherche')">
            {if $smarty.session.auth >= AUTH_COOKIE && $remip->has_x_student()}
              <input name="ok" value="Fruit" type="submit"
                   onclick="var $form = $(this).closest('form');$form.find('[name=search_line]').val($form.find('[name=free]').val());$form.attr('action', 'http://fruit/index.php');$form.attr('method', 'get')">
            {/if}
        </div>
    </form>
</div>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
