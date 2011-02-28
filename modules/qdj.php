<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                       *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                     *
 *                                                                         *
 *  This program is free software; you can redistribute it and/or modify   *
 *  it under the terms of the GNU General Public License as published by   *
 *  the Free Software Foundation; either version 2 of the License, or      *
 *  (at your option) any later version.                                    *
 *                                                                         *
 *  This program is distributed in the hope that it will be useful,        *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 *  GNU General Public License for more details.                           *
 *                                                                         *
 *  You should have received a copy of the GNU General Public License      *
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/


class QDJModule extends PLModule
{
    public function handlers()
    {
        return array(
            'qdj'               => $this->make_hook('qdj',          AUTH_INTERNAL),
            'qdj/admin'         => $this->make_hook('admin',        AUTH_MDP),
            'qdj/historic'      => $this->make_hook('historic',     AUTH_INTERNAL),
            'qdj/ajax/ranking'  => $this->make_hook('ajax_ranking', AUTH_INTERNAL),
            'qdj/ajax/modify'   => $this->make_hook('ajax_modify',  AUTH_MDP),
            'qdj/ajax/get'      => $this->make_hook('ajax_get',     AUTH_COOKIE),
            'qdj/ajax/vote'     => $this->make_hook('ajax_vote',    AUTH_COOKIE),
        );
    }

    public function handler_qdj($page)
    {
        $int = QDJ::interval();
        $date_min = mktime(1,0,0,floor(($int['date_min']->format('n') -1) / 2) * 2 + 1, 1, $int['date_min']->format('Y'));
        $date_max = mktime(1,0,0,floor(($int['date_max']->format('n') +1) / 2) * 2 + 1, 1, $int['date_max']->format('Y'));
        $date = $date_min;
        $begin_dates = array();
        $end_dates = array();
        while ($date < $date_max)
        {
            $begin_dates[] = $date;
            $date = strtotime('+2 months', $date);
            $end_dates[] = strtotime('-1 day', $date);
        }

        $page->assign('results', $this->fetch_scores($begin_dates, $end_dates, Env::t('period', count($begin_dates)-1)));
        $page->assign('end_date', $end_dates);
        $page->assign('begin_date', $begin_dates);
        $page->assign('group_tol', Group::from('tol'));

        $page->addCssLink('visualize.css');
        $page->addCssLink('qdj.css');
        $page->assign('title', "Classement QDJ");
        $page->changeTpl('qdj/qdj.tpl');
    }

    public function handler_admin($page)
    {
        if (!S::user()->hasRights(Group::from('qdj'), Rights::admin())) {
            return PL_FORBIDDEN;
        }

        $qdjs = QDJ::waiting();
        $qdjs->select(QDJSelect::all());
        $qdjs->order('date', false);

        $page->assign('qdjs', $qdjs);
        $page->addCssLink('qdj.css');
        $page->assign('title', "Planification des QDJ");
        $page->changeTpl('qdj/admin.tpl');
    }


    public function handler_historic($page)
    {
        $qdjs = QDJ::all();

        $page->assign('qdjs', $qdjs);
        $page->addCssLink('qdj.css');
        $page->assign('title', "Historique des QDJ");
        $page->changeTpl('qdj/historic.tpl');
    }

    function handler_ajax_modify($page)
    {
        S::assert_xsrf_token();
        if (!S::user()->hasRights(Group::from('qdj'), Rights::admin())) {
            return PL_FORBIDDEN;
        }

        $qdj = new QDJ(Json::i('id'));

        $page->jsonAssign('success', false);
        if (Json::has('date')) {
            $date = Json::t('date');
            if (!$date) {
                $qdj->date(false);
                $page->jsonAssign('success', true);
            } else {
                try {
                    $qdj->date(new FrankizDateTime($date));
                    $page->jsonAssign('success', true);
                } catch (Exception $e) {
                }
            }
        } else if (Json::has('delete')) {
            if (Json::b('delete')) {
                $qdj->delete();
                $page->jsonAssign('success', true);
            }
        }

        return PL_JSON;
    }

    public function handler_ajax_get($page)
    {
        $qdj = QDJ::last(Json::i('daysShift', 0));

        if ($qdj === false) {
            $page->jsonAssign('success', false);
            return PL_JSON;
        }
        $array_qdj = $qdj->export();

        if ($qdj->date()->format('Y-m-d') == date('Y-m-d'))
        {
            $voted = $qdj->hasVoted();
        } else {
            $voted = true;
        }

        $page->jsonAssign('success', true);
        $page->jsonAssign('voted', $voted);
        $page->jsonAssign('qdj', $array_qdj);
        $page->jsonAssign('votes', $qdj->last_votes());
        return PL_JSON;
    }

    public function handler_ajax_vote($page)
    {
        $json = json_decode(Env::v('json'));

        $vote = intval($json->{'vote'});
        $qdj = QDJ::last(0);

        $already_voted = $qdj->hasVoted(S::user()->id());

        if (!$already_voted)
        {
            $qdj->vote($vote);
        } else {
            $page->jsonAssign('error', 'Tu as déjà voté');
        }
        $page->jsonAssign('success', !$already_voted);
        return PL_JSON;
    }

    function handler_ajax_ranking($page)
    {
        $json = json_decode(Env::v('json'));
        $period = $json->period;
        $int = QDJ::interval();
        $date_min = mktime(1,0,0,floor(($int['date_min']->format('n') -1) / 2) * 2 + 1, 1, $int['date_min']->format('Y'));
        $date_max = mktime(1,0,0,floor(($int['date_max']->format('n') +1) / 2) * 2 + 1, 1, $int['date_max']->format('Y'));
        $date = $date_min;
        $begin_dates = array();
        $end_dates = array();
        while ($date < $date_max)
        {
            $begin_dates[] = $date;
            $date = strtotime('+2 months', $date);
            $end_dates[] = strtotime('-1 day', $date);
        }

        $page->assign('results', $this->fetch_scores($begin_dates, $end_dates, $period));
        $result = $page->fetch(FrankizPage::getTplPath('qdj/ranking.tpl'));
        $page->jsonAssign('success', true);
        $page->jsonAssign('result', $result);
        return PL_JSON;
    }

    function fetch_scores($begin_date, $end_date, $period)
    {
        if ($period === 'now')
        {
            $show_min = $begin_date[count($begin_date)-1];
            $show_max = $end_date[count($end_date)-1];
        }
        else if ($period === 'all')
        {
            $show_min = $begin_date[0];
            $show_max = $end_date[count($end_date)-1];
        }
        else
        {
            $show_min = $begin_date[$period];
            $show_max = $end_date[$period];
        }

        return QDJ::points(new FrankizDateTime(date('Y-m-d', $show_min)), new FrankizDateTime(date('Y-m-d', $show_max)));
    }
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
