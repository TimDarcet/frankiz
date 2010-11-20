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
    const SELECT_LOGIC       = 0x02;
    const SELECT_DESCRIPTION = 0x04;
    const SELECT_USERS       = 0x08;
    const SELECT_FREQUENCY   = 0x10;
    const SELECT_COMMENTS    = 0x20;

    const NS_GENERATED   = 'generated';    // Generated on the fly when writing a news or a survey
    const NS_FREE        = 'free';         // Non-Validated group
    const NS_BINET       = 'binet';        // Validated group
    const NS_STUDY       = 'study';
    const NS_SPORT       = 'sport';
    const NS_NATIONALITY = 'nationality';

    protected $image = null;
    protected $ns    = null;
    protected $name  = null;
    protected $label = null;
    protected $description = null;

    protected $users = array();
    protected $frequency = null;

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

    public function description()
    {
        return $this->description;
    }

    public function frequency()
    {
        return $this->frequency;
    }

    public function fillFromArray(array $values)
    {
        if (isset($values['image'])) {
            $this->image = new FrankizImage($values['image']);
            unset($values['image']);
        }

        if (isset($values['logic'])) {
            $this->logic = unserialize($values['logic']);
            unset($values['logic']);
        }

        parent::fillFromArray($values);
    }

    public function users()
    {
        return $this->users;
    }

    public function addUser($u, Rights $rights)
    {
        $uid = User::toId($u);

        // IMPROVE: can be done in one request
        foreach ($rights as $right)
            XDB::execute('DELETE FROM groups_userfilters WHERE gid = {?} AND FIND_IN_SET({?}, rights)', $gid, $right);

        XDB::execute('INSERT INTO  users_groups
                              SET  uid = {?}, gid = {?}, rights = {?}, comments = ""
          ON DUPLICATE KEY UPDATE  rights = CONCAT_WS(",", rights, {?})',
                                   $uid, $this->id(), $rights->flags(), $rights->flags());
    }

    public function removeUser($u, Rights $rights = null)
    {
        $uid = User::toId($u);

        if ($rights === null) {
            XDB::execute('DELETE FROM  users_groups
                                WHERE  uid = {?} AND gid = {?}',
                                         $uid, $this->id());
        } else {
            XDB::execute('UPDATE  users_groups
                             SET  rights = REPLACE(rights , {?}, "")
                           WHERE  uid = {?} AND gid = {?}',
                                  $uid, $this->id());
            XDB::execute('DELETE FROM  users_groups
                                WHERE  uid = {?} AND gid = {?} AND ',
                                         $uid, $this->id());
        }
    }

    public function userfilters(UserFilter $uf, Rights $rights)
    {
        $uf->insert();

        if ($rights === null) {
            XDB::execute('DELETE FROM  users_groups
                                WHERE  uid = {?} AND gid = {?}',
                                         $uid, $this->id());
        } else {
            XDB::execute('UPDATE  users_groups
                             SET  rights = REPLACE(rights , {?}, "")
                           WHERE  uid = {?} AND gid = {?}',
                                  $uid, $this->id());
            XDB::execute('DELETE FROM  users_groups
                                WHERE  uid = {?} AND gid = {?} AND ',
                                         $uid, $this->id());
        }
    }

    public function insert(Node $parent)
    {
        XDB::execute('UPDATE  groups
                         SET  name = {?}, label = {?}, description = {?}
                       WHERE  gid = {?}',
                   $this->name(), $this->label(), $this->description(), $this->id);
        $this->id = XDB::insertId();
    }

    public function toJson($stringify = false)
    {
        $json = array("id"    => $this->id(),
                      "name"  => $this->name(),
                      "label" => $this->label());

        if ($this->frequency !== null)
            $json['frequency'] = $this->frequency;

        return ($stringify) ? json_encode($json) : $json;
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

    public static function batchSelect(array $groups, $bits = null)
    {
        if (empty($groups))
            return;

        $groups = array_combine(self::toIds($groups), $groups);

        $joins = array();
        $cols = array();
        if ($bits & self::SELECT_BASE)
            $cols['g']   = array('ns', 'name', 'label', 'image');
        if ($bits & self::SELECT_LOGIC)
            $cols['g'][] = 'logic';
        if ($bits & self::SELECT_DESCRIPTION)
            $cols['g'][] = 'description';
        if ($bits & self::SELECT_FREQUENCY) {
            $cols[-1]    = array('COUNT(ug.uid) AS frequency');
            $joins['ug'] = PlSqlJoin::left('users_groups', '$ME.gid = g.gid');
        }

        if (!empty($cols)) {
            $iter = XDB::iterator('SELECT  g.gid AS id, ' . self::arrayToSqlCols($cols) . '
                                     FROM  groups AS g
                                     ' . PlSqlJoin::formatJoins($joins, array()) . '
                                    WHERE  g.gid IN {?}
                                 GROUP BY  g.gid', self::toIds($groups));

            while ($datas = $iter->next())
                $groups[$datas['id']]->fillFromArray($datas);
        }

//        if ($bits & self::SELECT_USERS)
//        {
//            foreach($groups as $group)
//                $group->users = new ACollection('User', 'Rights');
//
//            $iter = XDB::iterator("SELECT  gid, uid, rights
//                                     FROM  users_groups
//                                    WHERE  gid IN {?}", self::toIds($groups));
//
//            while ($datas = $iter->next())
//                $groups[$datas['gid']]->users->add($datas['uid'], $datas['rights']);
//
//        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
