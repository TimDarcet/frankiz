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

<img src="http://jtx/vdj/vdj.png" />

<script>
    var video_path = "{$globals->video->url}";

    {literal}
    $("#minimodule_jtx img").click(function() {
        $("#minimodule_jtx .body").html(
        '<video controls="controls" autoplay="autoplay" poster="' + video_path + '/vdj.png">' +
            '<source src="' + video_path + '/vdj.webm" type="video/webm" />' +
            '<source src="' + video_path + '/vdj.ogv" type="video/ogg" />' +
            '<p>Pour pouvoir profiter de cette vidéo, il faut installer un navigateur récent : Firefox ou chrome.</p>' +
        '</video>');
    });
    {/literal}
</script>


{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
