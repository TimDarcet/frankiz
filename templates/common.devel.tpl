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

{if #globals.debug#}
<div id="debug">
    <div class="platal">
        <span id="debug_hook">
            @HOOK@
        </span>

        @@BACKTRACE@@
        {literal}
        <script>
            $("#debug_hook .erreur").hide();
        </script>
        {/literal}
    </div>

    <div class="fkz">

        <div style="font-weight:bold" onclick="$('#debug_rights').toggle()">Rights</div>
        <div id="debug_rights" style="display: none">
            MEMBER
            {include file="groups_shower.tpl"|rel id="debug_member"|cat:$user->id() json=$user->groups('member')|smarty:nodefaults}

            ADMIN
            {include file="groups_shower.tpl"|rel id="debug_admin"|cat:$user->id() json=$user->groups('admin')|smarty:nodefaults}
        </div>

        <div style="font-weight:bold" onclick="$('#debug_debug').toggle()">{php}count(Debug::$postflush);{/php} Debug(s)</div>
        <div id="debug_debug">
            {php}
            if (class_exists('Debug')) {
                $debugs = Debug::$postflush;
                foreach ($debugs as $debug) {
                    $id = uniqid();
                    echo '<pre onclick="$(\'#'. $id . '\').toggle()" >' . $debug['var'] . '</pre>';
                    echo '<pre id="' . $id . '" style="display:none">' . $debug['trace'] . '</pre>';
                    echo '<br />';
                }
            }
            {/php}
        </div>
    </div>

</div>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}

