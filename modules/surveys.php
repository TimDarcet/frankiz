<?php
/***************************************************************************
 *  Copyright (C) 2004-2012 Binet Réseau                                   *
 *  http://br.binets.fr/                                                   *
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

class SurveysModule extends PlModule
{
    function handlers()
    {
        return array(
            'surveys/take'    => $this->make_hook('take',   AUTH_COOKIE),
            'surveys/submit'  => $this->make_hook('submit', AUTH_COOKIE),
            'surveys/see'     => $this->make_hook('see',    AUTH_COOKIE),
        );
    }

    function handler_take($page, $sid = null)
    {
        $survey = SurveyFilter::fromId($sid, false);
        if ($survey) {
            $survey->select(Survey::SELECT_BASE | Survey::SELECT_DESCRIPTION);
            if ($survey->alreadyTaken()) {
                pl_redirect('surveys/see/' . $sid);
                exit;
            }
            $survey->select(array(Survey::SELECT_DATAS => SurveyQuestion::SELECT_BASE));
        } else {
            throw new Exception("This survey doesn't exist");
        }

        $page->assign('survey', $survey);
        $page->assign('title', "Sondage");
        $page->addCssLink('surveys.css');
        $page->changeTpl('surveys/take.tpl');
    }

    function handler_submit($page, $sid = null)
    {
        $survey = SurveyFilter::fromId($sid, false);
        if ($survey) {
            $survey->select(array(Survey::SELECT_DATAS => SurveyQuestion::SELECT_BASE));
            $survey->submit();
        } else {
            throw new Exception("This survey doesn't exist");
        }

        $page->assign('title', "Sondage");
        $page->addCssLink('surveys.css');
        $page->changeTpl('surveys/submit.tpl');
    }

    function handler_see($page, $sid = null)
    {
        $survey = SurveyFilter::fromId($sid, false);
        if ($survey) {
            $survey->select(Survey::SELECT_BASE | Survey::SELECT_DESCRIPTION);
            $survey->select(array(Survey::SELECT_DATAS => SurveyQuestion::SELECT_BASE | SurveyQuestion::SELECT_ANSWERS));
        } else {
            throw new Exception("This survey doesn't exist");
        }

        $page->assign('survey', $survey);
        $page->assign('title', "Sondage");
        $page->addCssLink('surveys.css');
        $page->changeTpl('surveys/see.tpl');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
