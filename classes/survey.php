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

abstract class SurveyQuestion extends Meta
{
    const SELECT_BASE      = 0x01;
    const SELECT_ANSWERS   = 0x02;

    protected $rank        = null;
    protected $label       = null;
    protected $description = null;
    protected $mandatory   = null;
    protected $type        = null;

    protected $answers     = null;

    public function rank() {
        return $this->rank;
    }

    public function label() {
        return $this->label;
    }

    public function description() {
        return $this->description;
    }

    public function mandatory() {
        return $this->mandatory;
    }

    public function type() {
        return $this->type;
    }

    public function answers() {
        return $this->answers;
    }

    public abstract function check($answers);

    public abstract function submit($ssid, $answers);

    protected abstract function import($datas);

    protected abstract function export();

    protected abstract function decodeAnswer($ssid, $answers);

    public function insert($ssid) {
        XDB::execute('INSERT INTO  surveys_questions
                              SET  survey = {?}, rank = {?}, label = {?},
                                   label = {?}, description = {?}, mandatory = {?},
                                   type = {?}, datas = {?}',
                                   $ssid, $this->rank, $this->label,
                                   $this->label, $this->description, $this->mandatory,
                                   $this->type, json_encode($this->export()));
    }

    public static function batchSelect(array $questions, $options = null)
    {
        if (empty($questions)) {
            return;
        }

        if (empty($options)) {
            $options =self::SELECT_BASE;
        }

        $bits = self::optionsToBits($options);

        $questions = array_combine(self::toIds($questions), $questions);

        if ($bits & self::SELECT_BASE) {
            $iter = XDB::iterator('SELECT  qid AS id, rank, label, description,
                                           mandatory, type, datas
                                     FROM  surveys_questions
                                    WHERE  qid IN {?}
                                 GROUP BY  qid', self::toIds($questions));
    
            while ($datas = $iter->next()) {
                $questions[$datas['id']]->decodeDatas(json_decode($datas['datas']));unset($datas['datas']);
                $questions[$datas['id']]->fillFromArray($datas);
            }
        }

        if ($bits & self::SELECT_ANSWERS) {
            $iter = XDB::iterRow('SELECT  ssid, qid, datas
                                    FROM  surveys_answers
                                   WHERE  qid IN {?}
                                GROUP BY  qid', self::toIds($questions));
    
            while (list($ssid, $qid, $datas) = $iter->next()) {
                $questions[$qid]->decodeAnswer($ssid, json_decode($datas));
            }
        }
    }
}

class SurveyQuestionText extends SurveyQuestion
{
    protected $text = null;

    public function text() {
        return $this->text;
    }

    protected function import($datas) {
        $this->text = $datas->text;
    }

    protected function export() {
        return array('text' => $this->text);
    }

    protected function decodeAnswer($ssid, $datas) {
        $this->answers[$ssid] = $datas->text;
    }

    public function check($answers) {
        return true;
    }

    public function submit($ssid, $answers) {
        $datas = array('text' => $answers['q_' . $this->id()]);
        XDB::execute('INSERT INTO  surveys_answers
                              SET  ssid = {?}, qid = {?}, datas = {?}',
                                   $ssid, $this->id(), json_encode($datas));
    }
}

class SurveyQuestionChoices extends SurveyQuestion
{
    protected $choices = null;
    protected $max     = null;

    public function choices() {
        return $this->choices;
    }

    public function max() {
        return $this->max;
    }

    public function counts() {
        $counts = array();
        foreach ($this->choices as $i => $choice) {
            $counts[$choice] = 0;
            foreach ($this->answers as $answer) {
                foreach ($answer as $a) {
                    if ($a == $i) {
                        $counts[$choice]++;
                    }
                }
            }
        }
        return $counts;
    }

    protected function import($datas) {
        $this->choices = $datas->choices;
        $this->max = $datas->max;
    }

    protected function export() {
        return array('choices' => $this->choices,
                         'max' => $this->max);
    }

    protected function decodeAnswer($ssid, $datas) {
        $this->answers[$ssid] = $datas->choices;
    }

    public function check($answers) {
        if (is_array($answers['q_' . $this->id()])) {
            return (count($answers['q_' . $this->id()]) <= $this->max);
        }
        return true;
    }

    public function submit($ssid, $answers) {
        $datas = array('choices' => $answers['q_' . $this->id()]);
        XDB::execute('INSERT INTO  surveys_answers
                              SET  ssid = {?}, qid = {?}, datas = {?}',
                                   $ssid, $this->id(), json_encode($datas));
    }
}

class Survey extends Meta
{
    const SELECT_BASE        = 0x01;
    const SELECT_DESCRIPTION = 0x02;
    const SELECT_DATAS       = 0x04;

    protected $writer      = null;
    protected $origin      = null;
    protected $target      = null;
    protected $title       = null;
    protected $description = null;
    protected $begin       = null;
    protected $end         = null;
    protected $anonymous   = null;

    protected $ssid        = null;

    protected $questions   = null;

    public function writer()
    {
        return $this->writer;
    }

    public function origin()
    {
        return $this->origin;
    }

    public function target()
    {
        return $this->target;
    }

    public function title()
    {
        return $this->title;
    }

    public function description()
    {
        return $this->description;
    }

    public function begin()
    {
        return $this->begin;
    }

    public function end()
    {
        return $this->end;
    }

    public function anonymous()
    {
        return $this->anonymous;
    }

    public function questions()
    {
        return $this->questions;
    }

    public function alreadyTaken() {
        if ($this->ssid === null) {
            throw new DataNotFetchedException("Survey's datas haven't been fetched");
        }
        return ($this->ssid !== false);
    }

    /*******************************************************************************
         Survey managing functions

    *******************************************************************************/

    protected static function extract($qid) {
        $keys = preg_grep("/^q_$qid.*$/", array_keys($_REQUEST));
        $extraction = array();
        foreach ($keys as $key){
            $extraction[$key] = $_REQUEST[$key];
        }
        return $extraction;
    }

    public function check() {
        foreach ($this->questions as $qid => $question) {
            if (!$question->check(self::extract($qid))) {
                return false;
            }
        }

        return true;
    }

    public function submit() {
        if (!$this->check()) {
            throw new Exception('Answers to the survey are not consistents');
        }

        $ssid = uniqid('', true);

        XDB::startTransaction();

        foreach ($this->questions as $question) {
            $question->submit($ssid, self::extract($qid));
        }

        if ($this->anonymous()) {
            XDB::execute('INSERT INTO  surveys_sessions
                                  SET  uid = {?}, sid = {?}, ssid = 0',
                                        S::user()->id(), $this->id());
        } else {
            XDB::execute('INSERT INTO  surveys_sessions
                                  SET  uid = {?}, sid = {?}, ssid = {?}',
                                        S::user()->id(), $this->id(), $ssid);
        }

        XDB::commit();
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/

    public function insert()
    {
        XDB::startTransaction();

        XDB::execute('INSERT INTO surveys SET writer = {?}', S::user()->id());
        $this->id = XDB::insertId();

        foreach ($this->questions as $question) {
            $question->insert($ssid);
        }

        XDB::commit();
    }

    public static function batchSelect(array $surveys, $options = null)
    {
        if (empty($surveys))
            return;

        if (empty($options)) {
            $options =self::SELECT_BASE;
        }

        $bits = self::optionsToBits($options);
        $surveys = array_combine(self::toIds($surveys), $surveys);

        $joins = array();
        $cols = array();
        if ($bits & self::SELECT_BASE) {
            $cols['s']   = array('writer', 'origin', 'target', 'title');
            $joins['ss'] = PlSqlJoin::left('surveys_sessions', '$ME.sid = s.sid AND $ME.uid = {?}', S::user()->id());
            $cols['ss']  = array('ssid');
        }
        if ($bits & self::SELECT_DESCRIPTION) {
            $cols['s'] = array_merge($cols['s'], array('description', 'begin', 'end', 'anonymous'));
        }

        if (!empty($cols)) {
            $iter = XDB::iterator('SELECT  s.sid AS id, ' . self::arrayToSqlCols($cols) . '
                                     FROM  surveys AS s
                                     ' . PlSqlJoin::formatJoins($joins, array()) . '
                                    WHERE  s.sid IN {?}
                                 GROUP BY  s.sid', self::toIds($surveys));

            $groups = new Collection('Group');
            $users  = new Collection('User');
            while ($datas = $iter->next()) {
                if ($bits & self::SELECT_BASE) {
                    $datas['writer'] = $groups->addget($datas['writer']);
    
                    $datas['origin'] = $groups->addget($datas['origin']);
                    $datas['target'] = $groups->addget($datas['target']);
                }

                $surveys[$datas['id']]->fillFromArray($datas);
            }

            if (!empty($options[self::SELECT_BASE]))
                    $groups->select($options[self::SELECT_BASE]);
        }

        if ($bits & self::SELECT_DATAS)
        {
            foreach($surveys as $survey) {
                $survey->questions = new Collection('SurveyQuestion');
                $survey->questions->order('rank', false);
            }

            $iter = XDB::iterRow("SELECT  qid, survey, type
                                    FROM  surveys_questions
                                   WHERE  survey IN {?}", self::toIds($surveys));

            $questions = new Collection('SurveyQuestion');
            while (list($qid, $sid, $type) = $iter->next()) {
                $className = 'SurveyQuestion' . $type;
                $question = new $className($qid);
                $questions->add($question);
                $surveys[$sid]->questions->add($question);
            }

            if (!empty($options[self::SELECT_DATAS]))
                $questions->select($options[self::SELECT_DATAS]);
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
