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

{if $globals->debug > 0}
<div id="debug">
    <span id="debug_hook">
        @HOOK@ - <a href="debug/">$_SESSION</a> - <a onclick="$('#debug').hide()">Cacher</a>
    </span>

    @@BACKTRACE@@
    {js src="jquery.cookie.js"}
    {literal}
    <script>
        $(function() {
            $("#debug_hook .erreur").hide();
            $("#debug_hook br").hide();

            $('#debug').offset({left: $.cookie('debug_x'), top: $.cookie('debug_y')});

            $("#debug").draggable({
              handle: $("#debug_hook"),
                stop: function() {
                    var pos = $('#debug').offset();
                    $.cookie('debug_x', pos.left, {path: '/'});
                    $.cookie('debug_y', pos.top, {path: '/'});
                }
            });
        });
    </script>
    {/literal}
</div>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
