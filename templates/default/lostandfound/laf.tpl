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

{js src="laf.js"}

{if isset($message|smarty:nodefaults)}
    <div> {$message} </div>
{/if}

{if isset($not_logged|smarty:nodefaults)}
    <div> Tu dois t'identifier pour pouvoir faire cette action </div>
{/if}

<div class="module lostandfound">
    <div class="head">
        Objets trouvés
    </div>

    <div class="body">
        <div class="section">
            <div class="section_title">
                 Signaler
            </div>
            <div class="section_body">
                 {include file="lostandfound/signal.tpl"|rel}
            </div>
        </div>
	    
	    <div class="section">
	        <div class="section_title">
	            Objets perdus
            </div>
            <div class="section_body {if $query=='ping'}show{/if}">
                {include file="lostandfound/ping.tpl"|rel lost=$lost}
            </div>
        </div>

	    <div class="section">
            <div class="section_title" id="essai">
	            Objets trouvés
            </div>
            <div class="section_body {if $query=='pong'}show{/if}">
    	        {include file="lostandfound/pong.tpl"|rel found=$found}
    	    </div>
	    </div>
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
