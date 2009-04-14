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

{if isset($minimodules.$module_name|smarty:nodefaults)}
<div class="fkz_module_1">
  <div class="fkz_module_2">
    <div class="fkz_module_3">
      <div class="fkz_module_4">
        <div class="fkz_module_5">
          <div class="fkz_module_6">
            <div class="fkz_module" id="{$module_name}">
	      <div class="fkz_titre">
	        <span id="{$module_name}_logo"></span>
		{$minimodules.$module_name->get_titre()}
	      </div>
	      <div class="fkz_module_corps">
	        {include file=$minimodules.$module_name->get_template() minimodule=$minimodules.$module_name->get_params()}
	      </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
