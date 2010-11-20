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
<div class="box_proposal">
    <div class="title">
       Création de nouvelles instances
    </div>
    
    <table>
        {foreach from=$days item=next_dates key=weekday}
            <tr>
                <td width=20%>
                    {if $weekday == Monday}Lundi :
                    {elseif $weekday == Tuesday}Mardi :
                    {elseif $weekday == Wednesday}Mercredi :
                    {elseif $weekday == Thursday}Jeudi :
                    {elseif $weekday == Friday}Vendredi :
                    {elseif $weekday == Saturday}Samedi :
                    {elseif $weekday == Sunday}Dimanche :
                    {/if}
                </td>
                <td>
                    {foreach from=$next_dates item=day}
                        <span class="margin_right">
                            <input type="checkbox" name="{$day}_regular_proposal"/>
                            {$day}
                        </span>
                    {/foreach}
                </td>
            </tr>
        {/foreach}
    
        <tr>
            <td width=20%>
                autre :
            </td>
            <td>
                <input type="checkbox" name="other_regular_proposal"/>
                {valid_date name="date" value=$date to=15}
            </td>
        </tr>
        
        <tr>
            <td></td>
            <td>
                <input type="submit" name="send_reg" value="Valider" onClick="return window.confirm('Voulez vous vraiment proposer cette activité ?')"/>
            </td>
        </tr>
    </table>
</div>

<div class="box_proposal">
    <div class="title">
       Informations utilisées
    </div>
    
    <table>
        <tr>
            <td width=20%>
                Heure de début :
            </td>
            <td>
                <input type='text' name='begin' value="{$activity->default_begin()}"/>
            </td>
        </tr>
        
        <tr>
            <td>
                Heure de fin :
            </td>
            <td>
                <input type='text' name='end' value="{$activity->default_end()}" />
            </td>
        </tr>
    
        <tr>
            <td>
                Commentaire :
            </td>
            <td>
                <textarea name='comment' rows=7 cols=50></textarea>
            </td>
        </tr>
        
        <tr>
            <td></td>
            <td>
                <input type="submit" name="send_reg" value="Valider" onClick="return window.confirm('Voulez vous vraiment proposer cette activité ?')"/>
            </td>
        </tr>
    </table>
    
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}