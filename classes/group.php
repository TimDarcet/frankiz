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

class Group
{
    protected $gid;
    protected $type;
    protected $L;
    protected $R;
    protected $name;
    protected $long_name;
    protected $description;

    public function __construct($raw)
    {
        $this->fillFromArray($raw);
    }

    protected function fillFromArray(array $values)
    {
        foreach ($values as $key => $value) {
            if (property_exists($this, $key) && !isset($this->$key)) {
                $this->$key = $value;
            }
        }
    }

    public function gid()
    {
        return $this->gid;
    }

    public function type($default)
    {
        return (is_null($this->type)) ? $default : $this->type;
    }

    public function L()
    {
        return $this->L;
    }

    public function R()
    {
        return $this->R;
    }

    public function name()
    {
        return $this->name;
    }

    public function long_name($default)
    {
        return (is_null($this->long_name)) ? $default : $this->long_name;
    }

    public function description($default)
    {
        return (is_null($this->description)) ? $default : $this->description;
    }

    public function addTo($parent)
    {
        $parent = GroupFactory::gf()->group(self::toGid($parent));

        XDB::execute('LOCK TABLES groups WRITE');

        $parent->refresh();

        $this->L = $parent->R();
        $this->R = $this->L + 1;

        XDB::execute('UPDATE  groups
                         SET  R = R + 2
                       WHERE  R >= {?}', $parent->R());

        XDB::execute('UPDATE  groups
                         SET  L = L + 2
                       WHERE  L >= {?}', $parent->R());

        XDB::execute('INSERT INTO  groups
                              SET  type = {?}, L = {?}, R = {?},
                                   name = {?}, long_name = {?}, description = {?}',
                                $this->type('open'), $this->L, $this->R,
                                $this->name(), $this->long_name(''), $this->description(''));

        $gid = XDB::insertId();

        XDB::execute('UNLOCK TABLES');

        GroupFactory::gf()->feed($this);
    }

    public function remove()
    {
        XDB::execute('LOCK TABLES groups WRITE');

        $this->refresh();

        XDB::execute('DELETE FROM  groups
                            WHERE  gid = {?}', $this->gid);

        XDB::execute('UPDATE  groups
                         SET  L = L - 2
                       WHERE  L >= {?}', $this->L);

        XDB::execute('UPDATE  groups
                         SET  R = R - 2
                       WHERE  R >= {?}', $this->L);

        XDB::execute('UNLOCK TABLES');

        GroupFactory::gf()->unfeed($this->gid);
    }

    public function refresh()
    {
        $res = XDB::query('SELECT  gid, type, L, R, name, long_name
                             FROM  groups
                            WHERE  gid = {?}', $this->gid());
        $this->fillFromArray($res->fetchOneAssoc());
    }

    static function toGid($group)
    {
        if ($group instanceof Group)
            return $group->gid();
        else if (is_numeric($group))
            return $group;
        else
            return false;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
