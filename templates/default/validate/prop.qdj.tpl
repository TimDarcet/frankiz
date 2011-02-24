{**************************************************************************}
{*                                                                        *}
{*  Copyright (C) 2010 Binet Réseau                                       *}
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

{if isset($envoye|smarty:nodefaults)}

    <div class="msg_proposal"> Merci d'avoir proposé une qdj. <br />
    Le QDJmestre va la prendre en compte. </div>

{else}

<form enctype="multipart/form-data" method="post" action="proposal/qdj">
    {if t($msg) && $msg}
        <div class="msg_proposal">
            {$msg}
        </div>
    {/if}

    <div class="module">
        <div class="head">
            Aperçu
        </div>
        <div class="body qdj">
            <div class="question"></div>
            <table>
            <tr class="answers">
                <td class="answer1" width=50%></td>
                <td class="answer2"></td>
            </tr>
            </table>
        </div>
    </div>

    <div class="module">
        <div class="head">
            <span class="helper" target="proposal/qdj"></span>
            QDJ
        </div>
        <div class="body">
            <table>
                <tr>
                    <td width=20%>
                        Question :
                    </td>
                    <td>
                        <input type="text" name="quest" value="" id="quest_qdj_proposal"/>
                    </td>
                </tr>

                <tr>
                    <td>
                        Réponse 1 :
                    </td>
                    <td>
                        <input type="text" name="ans1" value="" id="ans1_qdj_proposal"/>
                    </td>
                </tr>

                <tr>
                    <td>
                        Réponse 2 :
                    </td>
                    <td>
                        <input type="text" name="ans2" value="" id="ans2_qdj_proposal"/>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="send" value="Envoyer" onClick="return window.confirm('Voulez vous vraiment proposer cette QDJ ?')"/>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>

{js src="prop.qdj.js"}

{/if}


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
