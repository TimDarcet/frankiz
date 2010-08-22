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

<div style="position:absolute; right:0; width:60%; z-index:666">
    {if #globals.debug#}
    @@BACKTRACE@@

      <div id="dev">
        @HOOK@
        User->groups() - 
        {php}
            echo implode(array_keys(S::user()->groups()), " ");
        {/php}
        <br />
        Group::groups() - 
        {php}
            foreach (Group::groups() as $key => $g)
            {
                if (Group::isGid($key)) {
                    echo $g->name().'('.$key.') ';
                }
            }
        {/php}
      </div>
    {/if}
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}

