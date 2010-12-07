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

class Group extends Meta
{
    const SELECT_BASE        = 0x01;
    const SELECT_CASTES      = 0x02;
    const SELECT_DESCRIPTION = 0x04;
    const SELECT_COMMENTS    = 0x08;

    const NS_USER        = 'user';         // User groups
    const NS_FREE        = 'free';         // Non-Validated group
    const NS_BINET       = 'binet';        // Validated group
    const NS_STUDY       = 'study';
    const NS_SPORT       = 'sport';
    const NS_NATIONALITY = 'nationality';

    protected $image = null;
    protected $ns    = null;
    protected $name  = null;
    protected $label = null;
    protected $score = null;

    protected $enter = null; // If true, you become a member when you join the group
    protected $leave = null; // If true, you can't leave a group
    protected $visibility = null; // If true, the groups is invisible

    protected $description = null;

    protected $castes = null;

    public function name()
    {
        return $this->name;
    }

    public function ns()
    {
        return $this->ns;
    }

    public function label($label = null)
    {
        if ($label != null)
        {
            $this->label = $label;
            XDB::execute('UPDATE groups SET label = {?} WHERE gid = {?}', $label, $this->id);
        }
        return $this->label;
    }

    public function score()
    {
        return $this->score;
    }

    public function enter()
    {
        return $this->enter;
    }

    public function leave()
    {
        return $this->leave;
    }

    public function visibility()
    {
        return $this->visibility;
    }

    public function description()
    {
        return $this->description;
    }

    public function caste(Rights $rights = null)
    {
        if ($rights === null)
            return $this->castes;

        /* /!\ Don't forget to select Castes before you try to access them
         * Because if this method doesn't find the wanted caste, it will create it
         * and you will get an error from the DB
        */

        if ($this->castes === null)
            $this->castes = new Collection('Caste');

        // Find the caste corresponding to the specified $rights
        $caste = $this->castes->filter('rights', $rights)->first();

        if ($caste === false) {
            $caste = new Caste(array('group' => $this, 'rights' => $rights));
            $caste->insert();
            $this->castes->add($caste);
        }

        return $caste;
    }

    public function toJson($stringify = false)
    {
        $json = array("id"    => $this->id(),
                      "name"  => $this->name(),
                      "label" => $this->label());

        return ($stringify) ? json_encode($json) : $json;
    }

    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/

    public function insert()
    {
        XDB::execute('INSERT INTO groups SET gid = NULL');
        $this->id = XDB::insertId();
    }

    public static function batchFrom(array $mixed)
    {
        $collec = new Collection();
        if (!empty($mixed)) {
            $iter = XDB::iterator('SELECT  gid AS id, name
                                     FROM  groups
                                    WHERE  name IN {?}', $mixed);
            while ($g = $iter->next())
                $collec->add(new self($g));
        }

        return $collec;
    }

    public static function batchSelect(array $groups, $options = null)
    {
        if (empty($groups))
            return;

        if (empty($options)) {
            $options = array(self::SELECT_BASE => null);
            $options[self::SELECT_CASTES] = Caste::SELECT_BASE;
        }

        $bits = self::optionsToBits($options);
        $groups = array_combine(self::toIds($groups), $groups);

        $joins = array();
        $cols = array();
        if ($bits & self::SELECT_BASE)
            $cols['g']   = array('ns', 'name', 'label', 'score', 'image', 'enter', 'leave', 'visibility');
        if ($bits & self::SELECT_DESCRIPTION)
            $cols['g'][] = 'description';

        if (!empty($cols)) {
            $iter = XDB::iterator('SELECT  g.gid AS id, ' . self::arrayToSqlCols($cols) . '
                                     FROM  groups AS g
                                     ' . PlSqlJoin::formatJoins($joins, array()) . '
                                    WHERE  g.gid IN {?}
                                 GROUP BY  g.gid', self::toIds($groups));

            while ($datas = $iter->next()) {
                if (isset($datas['name']))
                    $datas['name'] = ($datas['name'] === null) ? false : $datas['name'];

                $groups[$datas['id']]->fillFromArray($datas);
            }
        }

        if ($bits & self::SELECT_CASTES)
        {
            foreach($groups as $group)
                $group->castes = new Collection('Caste');

            $iter = XDB::iterRow("SELECT  cid, gid
                                    FROM  castes
                                   WHERE  gid IN {?}", self::toIds($groups));

            $castes = new Collection('Caste');
            while (list($cid, $gid) = $iter->next()) {
                $caste = new Caste(array('id' => $cid, 'group' => $groups[$gid]));

                $castes->add($caste);
                $groups[$gid]->castes->add($caste);
            }

            if (!empty($options[self::SELECT_CASTES]))
                $castes->select($options[self::SELECT_CASTES]);
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
