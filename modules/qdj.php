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
            'qdj'           => $this->make_hook('qdj', AUTH_COOKIE),
            'qdj/ajax/get'  => $this->make_hook('ajax_get', AUTH_COOKIE),
            'qdj/ajax/vote' => $this->make_hook('ajax_vote', AUTH_COOKIE),
        );
    }

    public function handler_qdj(&$page)
    {
        $page->assign('title', "Classement QDJ");
        $page->changeTpl('qdj/qdj.tpl');
    }

    public static function assocToJSON($assoc)
    {
        $temp = array();

        foreach ($assoc as $key => $value)
        {
            $temp[] = '"'.$key.'": '.((is_numeric($value)) ? $value : json_encode($value)).'';
        }
        return implode(', ', $temp);
    }

    public function handler_ajax_get(&$page, $daysShift = 0)
    {
        $daysShift = intval($daysShift);
        $res=XDB::query('SELECT qdj_id, date, question, answer1, answer2, count1, count2
                           FROM qdj
                          ORDER BY date DESC
                          LIMIT {?}, 1', $daysShift);
        $array_qdj = $res->fetchOneAssoc();

        if ($daysShift == 0)
        {
            $resv=XDB::query('SELECT qv.rank
                                FROM qdj_votes AS qv
                               WHERE qv.qdj_id = {?} AND qv.uid = {?}
                               LIMIT 1',
                                $array_qdj['qdj_id'],
                                S::user()->id()
                            );
            $voted = $resv->numRows();
        } else {
            $voted = 1;
        }

        if ($voted == 1)
        {
            $voted = 'true';
        } else {
            $array_qdj['count1'] = -1;
            $array_qdj['count2'] = -1;
            $voted = 'false';
        }

        $qdj = '{'.self::assocToJSON($array_qdj).'}';

        $page->assign('qdj', $qdj);
        $page->assign('voted', $voted);
        pl_content_headers("text/javascript", "utf-8");
        $page->changeTpl('qdj/get.js.tpl', NO_SKIN);
    }

    public function handler_ajax_vote(&$page, $answer = 0)
    {
        // Get the id of the last QDJ
        $res=XDB::query('SELECT qdj_id
                           FROM qdj
                       ORDER BY date DESC
                          LIMIT 1');
        $qdj_id = $res->fetchOneCell();

        // Already voted ?
        $res=XDB::query('SELECT vote_id
                           FROM qdj_votes
                          WHERE qdj_id = {?} AND uid = {?}
                          LIMIT 1',
                            $qdj_id,
                            S::user()->id()
                        );
        $already_voted = ($res->fetchOneCell() == 1) ? true : false;

        if (!$already_voted)
        {
            // Let's vote
            XDB::execute('INSERT INTO qdj_votes
                                  SET qdj_id = {?},
                                         uid = {?},
                                        rank = 0,
                                        rule = "null"',
                            $qdj_id,
                            S::user()->id()
                        );
            $vote_id = XDB::insertID();

            // Get the rank
            $res=XDB::query('SELECT COUNT(*)
                               FROM qdj_votes
                              WHERE qdj_id = {?} AND vote_id <= {?}',
                                $qdj_id,
                                $vote_id
                            );
            $rank = $res->fetchOneCell();

            $rule = null;
            $points = 0;
            switch($rank)
            {
                case 1:
                    $rule = '1';
                    $points = 5;
                break;

                case 2:
                    $rule = '2';
                    $points = 2;
                break;

                case 3:
                    $rule = '3';
                    $points = 1;
                break;

                case 13:
                    $rule = '13';
                    $points = -13;
                break;

                case 42:
                    $rule = '42';
                    $points = 4.2;
                break;

                case 69:
                    $rule = '69';
                    $points = 6.9;
                break;

                case 314:
                    $rule = '314';
                    $points = 3.14;
                break;

                case substr(IP::get(), -3):
                    $rule = 'ip';
                    $points = 3;
                break;
            }

            XDB::execute('UPDATE qdj_votes
                             SET rank = {?},
                                 rule = {?}
                           WHERE vote_id = {?}',
                            $rank,
                            $rule,
                            $vote_id
                        );

            if ($answer == 1) {
                XDB::execute('UPDATE qdj SET count1 = count1+1 WHERE qdj_id={?}',$qdj_id);
            } else {
                XDB::execute('UPDATE qdj SET count2 = count2+1 WHERE qdj_id={?}',$qdj_id);
            }

            XDB::execute('INSERT INTO qdj_scores
                                  SET uid = {?}, total = {?}, bonus = 0
              ON DUPLICATE KEY UPDATE total = total+{?}',
                        S::user()->id(),
                        $points,
                        $points
                        );
        }

        $page->assign('already_voted', json_encode($already_voted));
        pl_content_headers("text/javascript", "utf-8");
        $page->changeTpl('qdj/vote.js.tpl', NO_SKIN);
    }
}


// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
