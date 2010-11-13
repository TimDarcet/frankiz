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

class Wiki extends Meta
{    
    const SELECT_BASE    = 0x01;
    const SELECT_COUNT   = 0x02;
    const SELECT_VERSION = 0x04;

    protected $name     = null;
    protected $count    = null;
    protected $versions = null;
    protected $comments = null;
    
    public function name()
    {
        return $this->name;
    }

    public function count()
    {
        return $this->count;
    }

    public function content($version = null)
    {
        $version = ($version == null) ? reset($this->versions) : $this->versions[$version];
        return $version['content'];
    }

    public function html($version = null)
    {
        $version = ($version == null) ? reset($this->versions) : $this->versions[$version];
        return MiniWiki::WikiToHTML($version['content']);
    }

    public function writer($version = null)
    {
        $version = ($version == null) ? reset($this->versions) : $this->versions[$version];
        return $version['writer'];
    }

    public function wrote($version = null)
    {
        $version = ($version == null) ? reset($this->versions) : $this->versions[$version];
        return $version['wrote'];
    }

    public function comments($comments = null)
    {
        if ($comments !== null) {
            $this->comments = $comments;
            XDB::execute('UPDATE wiki SET comments = {?} WHERE wid = {?}',
                                                $this->comments, $this->id);
        }
        return $this->comments;
    }

    public function update($content, $writer)
    {
        XDB::execute('INSERT INTO  wiki_version
                              SET  wid = {?}, wrote = NOW(), writer = {?}, content = {?},
                                   version = (SELECT (IFNULL(MAX(t.version), 0) + 1) FROM wiki_version AS t WHERE t.wid = {?})',
                              $this->id, User::toId($writer), $content, $this->id);
    }

    public function delete()
    {  
	    parent::delete();
	    XDB::execute('DELETE FROM wiki_version WHERE wid = {?}', $this->id);
	    XDB::execute('DELETE FROM wiki WHERE wid = {?}', $this->id);
    }

    public function insert()
    {
        XDB::execute('INSERT INTO wiki SET name = {?}', $this->name);
    }

    public static function batchFrom(array $mixed)
    {
        $collec = new Collection();
        if (!empty($mixed)) {
            $iter = XDB::iterator('SELECT  wid AS id, name
                                     FROM  wiki
                                    WHERE  name IN {?}', $mixed);
            while ($g = $iter->next())
                $collec->add(new self($g));
        }

        return $collec;
    }

    public static function batchSelect(array $wikis, $options)
    {
        if (empty($wikis))
            return;

        $bits = self::optionsToBits($options);
        $wikis = array_combine(self::toIds($wikis), $wikis);

        $joins = array();
        $cols = array();
        if ($bits & self::SELECT_BASE) {
            $cols['w'] = array('name', 'comments');
        }

        if ($bits & self::SELECT_COUNT || $bits & self::SELECT_VERSION) {
            $cols[-1] = array('COUNT(wv.version) AS count');
            $joins['wv'] = PlSqlJoin::left('wiki_version', '$ME.wid = w.wid');
        }

        if (!empty($cols)) {
            $iter = XDB::iterator('SELECT  w.wid AS id, ' . self::arrayToSqlCols($cols) . '
                                     FROM  wiki AS w
                                           ' . PlSqlJoin::formatJoins($joins, array()) . '
                                    WHERE  w.wid IN {?}', array_keys($wikis));

            while ($datas = $iter->next())
                $wikis[$datas['id']]->fillFromArray($datas);
        }

        // Load last version
        if ($bits & self::SELECT_VERSION)
        {
            $conds = array();
            foreach ($wikis as $w)
            {
                $w->versions = array();
                $conds[] = XDB::format('( wid = {?} AND version = {?} )', $w->id(), $w->count());
            }

            $iter = XDB::iterator('SELECT  wid AS id, version, wrote, writer, content
                                     FROM  wiki_version
                                    WHERE  ' . implode(' OR ', $conds));

            while ($datas = $iter->next())
                $wikis[$datas['id']]->versions[$datas['version']] = array('wrote' => $datas['wrote'],
                                                                         'writer' => $datas['writer'],
                                                                        'content' => $datas['content']);
        }
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
