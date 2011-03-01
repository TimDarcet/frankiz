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

{js src="plugins/datepicker_lang_FR.js"}
{js src="plugins/wdCalendar_lang_FR.js"}

{js src="plugins/Common.js"}
{js src="plugins/jquery.datepicker.js"}
{js src="plugins/jquery.alert.js"}
{js src="plugins/jquery.ifrmdailog.js"}
{js src="plugins/jquery.calendar.js"}

{if isset($date|smarty:nodefaults)}
    <script>
        var date_cal = new Date('{$date|datetime:'m/d/Y H:i'}');
    </script>
{/if}

{if isset($visibility|smarty:nodefaults)}
    <script>
        var url_cal = 'activity/ajax/timetable/{$visibility}';
    </script>
{/if}

{js src="activities.js"}
{js src="wdcalendar.js"}

<div class="module" id="calendar">
    <div class="head">
        Calendrier des activités
        <div class="options">
            <div id="calhead" style="padding-left:1px;padding-right:1px;">
                <div id="loadingpannel" class="ptogtitle loadicon" style="display: none;">
                    Chargement des données...
                </div>
                <div id="errorpannel" class="ptogtitle loaderror" style="display: none;">
                    Désolé, les données n'ont pu être chargées, merci de réessayer plus tard
                </div>
            </div>
            <div id="caltoolbar" class="ctoolbar">
                <div id="showtodaybtn" class="fbutton">
                    <div>
                        <span title='Click to back to today ' class="showtoday">
                            Aujourd'hui
                        </span>
                    </div>
                </div>
                <div class="btnseparator">
                </div>
                <div id="showdaybtn" class="fbutton">
                    <div>
                        <span title='Day' class="showdayview">
                            Jour
                        </span>
                    </div>
                </div>
                <div  id="showweekbtn" class="fbutton fcurrent">
                    <div>
                        <span title='Week' class="showweekview">
                            Semaine
                        </span>
                    </div>
                </div>
                <div  id="showmonthbtn" class="fbutton">
                    <div>
                        <span title='Month' class="showmonthview">
                            Mois
                        </span>
                    </div>
                </div>
                <div class="btnseparator">
                </div>
                <div  id="showreflashbtn" class="fbutton">
                    <div>
                        <span title='Refresh view' class="showdayflash">
                            Rafraîchir
                        </span>
                    </div>
                </div>
                <div class="btnseparator">
                </div>
                <div id="sfprevbtn" title="Prev"  class="fbutton">
                    <span class="fprev">
                    </span>
                </div>
                <div id="sfnextbtn" title="Next" class="fbutton">
                    <span class="fnext">
                    </span>
                </div>
                <div class="fshowdatep fbutton">
                    <div>
                        <input type="hidden" name="txtshow" id="hdtxtshow" />
                        <span id="txtdatetimeshow">
                            Choisir une date
                        </span>
                    </div>
                </div>
            </div>
            <div class="ctoolbar">
                <div class="right">
                    <div class="new fbutton">
                        <div onclick="change_view_cal('all')">
                            <span class="world"></span>
                            Toutes les activités
                        </div>
                    </div>
                    <div class="btnseparator">
                    </div>

                    <div class="new fbutton">
                        <div onclick="change_view_cal('friends')">
                            <span class="group_ico"></span>
                            Mes groupes
                        </div>
                    </div>
                    <div class="btnseparator">
                    </div>

                    <div class="new fbutton">
                        <div onclick="change_view_cal('participate')">
                            <span class="alone"></span>
                            Mes activités
                        </div>
                    </div>
                    <div class="btnseparator">
                    </div>

                    <div class="new fbutton">
                        <div>
                            <a href="proposal/activity">
                                <span class="new_element"></span>
                                Nouvelle activité
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="body">
        <div>
            <div id="dvCalMain" class="calmain printborder">
                <div id="gridcontainer" style="overflow-y: visible;">
                </div>
            </div>
        </div>
    </div>
</div>

<div id="activity_show" class="module timetable">
    <div class="head">
        <table><tr>
        <td class="origin">
        </td>
        <td class="title">
        </td>
        <td class="close_show">
        </td>
        </tr></table>
    </div>
    <div class="padd body">
        <div class="one_day">
            <div class="date">
            </div>
            <div class="time">
                de
                <span class="hour_begin">
                </span>
                à
                <span class="hour_end">
                </span>
            </div>
        </div>
        <div class="several_days">
            <div class="begin">
                du
                <span class="date">
                </span>
                à
                <span class="hour">
                </span>
            </div>
            <div class="end">
                au
                <span class="date">
                </span>
                à
                <span class="hour">
                </span>
            </div>
        </div>
        <div class="msg">
        </div>
        <div class="present">
            <a onclick=""><span class="add_participant"></span>S'inscrire</a>
        </div>
        <div class="out">
            <a onclick=""><span class="remove_participant"></span>Se désinscrire</a>
        </div>
        <div class="section">
            Description :
        </div>
        <div class="description">
        </div>
        <div class="comment">
        </div>
        <div class="section participants_list">
            Participants :
            <span class="number">
            </span>
        </div>
        <div class="participants">
        </div>
        <div class="misc">
            <div class="mail">
                <a href=""><div class="mail_ico"></div>Mail</a>
            </div>
            <div class="participants_link">
                <a href=""><div class="group_ico"></div>Participants</a>
            </div>
            <div class="admin">
                <a href=""><div class="edit"></div>Modifier</a>
            </div>
        </div>
    </div>
</div>

{if isset($id|smarty:nodefaults)}
    <script>
        $(function() {literal}{{/literal}
            load([{$id}], true);
            $('#activity_show').show();
        {literal}}{/literal});
    </script>
{/if}

{* vim:set et sw=2 sts=2 sws=2 enc=utf-8: *}
