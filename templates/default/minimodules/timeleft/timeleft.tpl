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

<script>
     var timeleft_future = new Date("{$minimodule.future->toJS()}");
</script>

<div class="timeleft" style="background:black">
    <div class="background">
        <img src="css/default/minimodules/timeleft/maskes.png" style="width:100%"/>
    </div>
    <div class="text" style="text-align:center;margin-top:2pxpx;z-index:666;width:100%;font-weight:bold;font-size:26px;color:white">
    </div>
</div>

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
