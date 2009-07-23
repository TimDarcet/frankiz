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

{* {if not $page_raw} *}
<div class="fkz_divers_1">
  <div class="fkz_divers_2">
    <div class="fkz_divers_3">
      <div class="fkz_divers_4">
        <div class="fkz_divers_5">
	  <div class="fkz_divers_6">
	    <div class="fkz_page_divers">
	      <div class="fkz_page_titre">
	        {$title}
	      </div>
	      <div class="fkz_page_corps">
{* {/if} *}
		{if (isset($pl_no_errors|smarty:nodefaults) && !$pl_no_errors) || $pl_failure || $pl_errors}
		{include file="skin/common.errors.tpl"}
		{/if}
		
		{include file=$pl_tpl}
{* {if not $page_raw} *}
	      </div>
	    </div>
	  </div>
	</div>
      </div>
    </div>
  </div>
</div>
{* {/if} *}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
