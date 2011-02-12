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

class QDJSchema extends Schema
{
    public function className() {
        return 'QDJ';
    }

    public function table() {
        return 'qdj';
    }

    public function id() {
        return 'id';
    }

    public function tableAs() {
        return 'qdj';
    }

    public function scalars() {
        return array('question', 'answer1', 'answer2', 'count1', 'count2');
    }

    public function objects() {
        return array('date'     => 'FrankizDateTime',
                     'writer'   => 'User');
    }
}

class QDJSelect extends Select
{
    protected static $natives = array('date', 'question', 'answer1', 'answer2', 'count1', 'count2', 'writer');

    public function className() {
        return 'QDJ';
    }

    protected function handlers() {
        return array('main' => self::$natives);
    }

    public static function all() {
        return new QDJSelect(self::$natives);
    }
}

class QDJ extends Meta
{
    protected $question = null;
    protected $answer1  = null;
    protected $answer2  = null;

    protected $count1   = null;
    protected $count2   = null;

    protected $date     = null;
    protected $writer   = null;

    public function insert()
    {
        XDB::execute('INSERT  qdj
                         SET  question = {?}, answer1 = {?}, answer2 = {?},
                              count1 = 0, count2 = 0, writer = {?}',
            $this->question, $this->answer1, $this->answer2,
            $this->writer->id());

        $this->id = XDB::insertId();
    }

    //percentages of answers 1 or 2
    public function percentage1() {
        if (($this->count1)+($this->count2) != 0)
            return round(100*((int)$this->count1)/(($this->count1)+($this->count2)));
        else
            return 0;
    }

    public function percentage2() {
        if (($this->count1)+($this->count2) != 0)
            return round(100*((int)$this->count2)/(($this->count1)+($this->count2)));
        else
            return 0;
    }

    // @param $id Id of the user
    public function hasVoted($id) {
        $res=XDB::query('SELECT  vote_id
                           FROM  qdj_votes
                          WHERE  qdj = {?} AND uid = {?} AND rule != 10',
                            $this->id(),
                            $id
                        )->fetchAllRow();
        trace($res);
        return count($res) >= 1;
    }

    public function export($bits = null) {
        $array = parent::export();
        $array['question'] = $this->question;
        $array['answer1'] = $this->answer1;
        $array['answer2'] = $this->answer2;
        $array['count1'] = $this->count1;
        $array['count2'] = $this->count2;
        $array['date'] = $this->date->format('Y-m-d');
        return $array;
    }

    // only works for the session's user because of the IP
    // @param : $answer Either 0 or 1
    public function vote($answer) {
        XDB::execute('INSERT INTO  qdj_votes
                              SET  qdj = {?}, uid = {?}, rank = 0, rule = "null"',
                            $this->id(), S::user()->id());
        $vote = XDB::insertID();

        // Get the rank
        $rank = XDB::query('SELECT  COUNT(*)
                              FROM  qdj_votes
                             WHERE  qdj = {?} AND vote_id <= {?}',
                             $this->id(), $vote
                        )->fetchOneCell();

        if ($rank == 1)
        {
            if ($this->writer === null)
                $this->select(QDJSelect::all());
            XDB::execute('INSERT INTO  qdj_votes
                                  SET  qdj = {?}, uid = {?}, rank = 0, rule = 10',
                            $this->id(), $this->writer->id());
        }

        $rule = null;
        switch($rank)
        {
            case 1:
                $rule = '1';
            break;

            case 2:
                $rule = '2';
            break;

            case 3:
                $rule = '3';
            break;

            case 13:
                $rule = '4';
            break;

            case 42:
                $rule = '5';
            break;

            case 69:
                $rule = '6';
            break;

            case 314:
                $rule = '7';
            break;

            case substr(IP::get(), -3):
                $rule = '8';
            break;
        }

        XDB::execute('UPDATE qdj_votes
                         SET rank = {?}, rule = {?}
                       WHERE vote_id = {?}',
                        $rank, $rule, $vote);

        if ($answer == 1) {
            XDB::execute('UPDATE qdj SET count1 = count1+1 WHERE id={?}',$this->id());
        } else {
            XDB::execute('UPDATE qdj SET count2 = count2+1 WHERE id={?}',$this->id());
        }
    }

    /*******************************************************************************
         Static functions

    *******************************************************************************/

    // returns all the qdjs
    public static function all() {
        $res=XDB::iterator('SELECT  id, date, question, answer1, answer2, count1, count2, writer
                              FROM  qdj
                             WHERE  date!="0000-00-00" AND date<NOW()
                             ORDER  BY date DESC');

        $qdjs = new Collection('QDJ');
        $users = new Collection('User');

        while ($datas = $res->next()) {
            $datas['writer'] = $users->addget($datas['writer']);
            $datas['date']  = new FrankizDateTime($datas['date']);
            $qdjs->addget($datas['id'])->fillFromArray($datas);
        }
        $users->select(UserSelect::base());
        return $qdjs;
    }

    // return the $shift+1th last QDJ
    // @param $shift Number of days to skip
    public static function last($shift = 0) {
        $res=XDB::query('SELECT  id, date, question, answer1, answer2, count1, count2, writer
                           FROM  qdj
                          WHERE  date <= NOW()
                          ORDER  BY date DESC
                          LIMIT  1 OFFSET {?}', $shift)->fetchOneAssoc();
        if ($res !== null) {
            $res['date'] = new FrankizDateTime($res['date']);
            $res['writer'] = new User($res['writer']);
            $qdj = new QDJ($res['id']);
            $qdj->fillFromArray($res);
        }
        else {
            $qdj = false;
        }
        return $qdj;
    }

    // Returns all the QDJ not passed
    public static function waiting() {
        $res=XDB::query('SELECT  id
                           FROM  qdj
                          WHERE  ISNULL(date) OR date > NOW()')->fetchColumn();

        $collec = new Collection('QDJ');

        foreach ($res as $id)
        {
            $collec->add($id);
        }
        
        return $collec;
    }

    public static function interval() {
        $res = XDB::query('SELECT  MIN(date) AS date_min, MAX(date) AS date_max
                             FROM  qdj
                            WHERE  (count1 > 0 OR count2 > 0) AND date != "0000-00-00"');
        $res = $res->fetchOneRow();
        return Array('date_min' => new FrankizDateTime($res[0]), 'date_max' => new FrankizDateTime($res[1]));
    }

    // Returns an array of arrays
    public static function points(FrankizDateTime $begin, FrankizDateTime $end) {
        $res = XDB::query('SELECT  uid,
                                   SUM( _vote1*5 + _vote2*2 + _vote3 - _vote4*13 + _vote5*4.2 +
                                        _vote6*6.9 + _vote7*3.14 + _vote8*3 + _vote9*7 + _vote10*7.1) as total,
                                   SUM(_vote1) as nb1,
                                   SUM(_vote2) as nb2,
                                   SUM(_vote3) as nb3,
                                   SUM(_vote4) as nb4,
                                   SUM(_vote5) as nb5,
                                   SUM(_vote6) as nb6,
                                   SUM(_vote7) as nb7,
                                   SUM(_vote8) as nb8,
                                   SUM(_vote9) as nb9,
                                   SUM(_vote10) as nb10
                             FROM  (
                                   SELECT  uid,
                                           if(rule = 1, count(*), 0) as _vote1,
                                           if(rule = 2, count(*), 0) as _vote2,
                                           if(rule = 3, count(*), 0) as _vote3,
                                           if(rule = 4, count(*), 0) as _vote4,
                                           if(rule = 5, count(*), 0) as _vote5,
                                          if(rule = 6, count(*), 0) as _vote6,
                                          if(rule = 7, count(*), 0) as _vote7,
                                          if(rule = 8, count(*), 0) as _vote8,
                                          if(rule = 9, count(*), 0) as _vote9,
                                          if(rule = 10, count(*), 0) as _vote10
                                    FROM  qdj_votes AS qv
                              INNER JOIN  qdj AS q
                                      ON  qv.qdj = q.id
                                   WHERE  qv.rule >0
                                     AND  q.date BETWEEN {?} AND {?}
                                GROUP BY  rule, uid
                                   ) AS aux
                         GROUP BY  uid
                         ORDER BY  total DESC',
                $begin->toDb(), $end->toDb())->fetchAllAssoc();

        
        $users = new collection('User');

        foreach ($res as $key => $e)
        {
            $res[$key]['average'] = ($e['nb1'] + $e['nb2'] + $e['nb3'] + $e['nb4'] + $e['nb5'] +
                    $e['nb6'] + $e['nb7'] + $e['nb8'] + $e['nb9'] + $e['nb10'])/10;

            $res[$key]['user'] = $users->addget($e['uid']);
            unset($res[$key]['uid']);
            
            $res[$key]['deviation'] = round(sqrt((pow($e['nb1'],2) + pow($e['nb2'],2) + pow($e['nb3'],2) +
                    pow($e['nb4'],2) + pow($e['nb5'],2) + pow($e['nb6'],2) + pow($e['nb7'],2) + pow($e['nb8'],2) +
                    pow($e['nb9'],2) + pow($e['nb10'],2))/10 - pow($res[$key]['average'],2)),2);
        }

        $users->select(UserSelect::base());

        return $res;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
