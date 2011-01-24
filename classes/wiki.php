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

    /**
    * Get or set the name of the Wiki
    *
    * @param $name  If specified, updates the name of the Wiki
    */
    public function name($name = null)
    {
        if ($name !== null) {
            $this->name = $name;
            XDB::execute('UPDATE wiki SET name = {?} WHERE wid = {?}',
                                               $this->name, $this->id());
        }
        return $this->name;
    }

    /**
    * Get the number of versions
    */
    public function count()
    {
        return $this->count;
    }

    private function _highest()
    {
        $highest = 1;
        foreach ($this->versions as $key => $val)
            if ($key > $highest)
                $highest = $key;
        return $highest;
    }

    /**
    * Get the content of the specified version
    *
    * @param $version  If null, returns the content of the highest version loaded
    */
    public function content($version = null)
    {
        if ($version == null)
            $version = $this->_highest();
        return $this->versions[$version]['content'];
    }

    /**
    * Get the Html content of the specified version
    *
    * @param $version  If null, return the Html of the highest version loaded
    */
    public function html($version = null)
    {
        if ($version == null)
            $version = $this->_highest();
        return MiniWiki::WikiToHTML($this->versions[$version]['content']);
    }

    /**
    * Get the User who wrote the specified version
    *
    * @param $version  If null, return the User of the highest version loaded
    */
    public function writer($version = null)
    {
        if ($version == null)
            $version = $this->_highest();
        return $this->versions[$version]['writer'];
    }

    /**
    * Get the date-time where the specified version was writen
    *
    * @param $version  If null, return the date-time of the highest version loaded
    */
    public function wrote($version = null)
    {
        if ($version == null)
            $version = $this->_highest();
        return $this->versions[$version]['wrote'];
    }

    /**
    * Get or Set the comments of the wiki
    *
    * @param $comments If specified, update the comments
    */
    public function comments($comments = null)
    {
        if ($comments !== null) {
            $this->comments = $comments;
            XDB::execute('UPDATE wiki SET comments = {?} WHERE wid = {?}',
                                                $this->comments, $this->id());
        }
        return $this->comments;
    }

    /**
    * Add a version to the Wiki
    *
    * @param $content  The content of the new version
    * @param $writer   The User/uid who wrote the new version
    */
    public function update($content, $writer = null)
    {
        $writer = ($writer === null) ? S::user()->id() : User::toId($writer);
        XDB::execute('INSERT INTO  wiki_version
                              SET  wid = {?}, wrote = NOW(), writer = {?}, content = {?},
                                   version = (SELECT (IFNULL(MAX(t.version), 0) + 1) FROM wiki_version AS t WHERE t.wid = {?})',
                              $this->id(), $writer, $content, $this->id());
    }

    /**
    * Delete the Wiki and all its versions
    */
    public function delete()
    {  
	    parent::delete();
	    XDB::execute('DELETE FROM wiki_version WHERE wid = {?}', $this->id());
	    XDB::execute('DELETE FROM wiki WHERE wid = {?}', $this->id());
    }

    /**
    * Insert a new Wiki in the DB
    */
    public function insert()
    {
        XDB::execute('INSERT INTO wiki SET name = {?}', $this->name);
        $this->id = XDB::insertId();
    }

    public function export($bits = null)
    {
        $export = parent::export();

        if ($this->name !== null)
            $export['name'] = $this->name;

        if ($this->versions !== null)
            foreach ($this->versions as $key => $version) {
                $export['versions'][$key] = array('wrote'   => $version['wrote'],
                                                  'writer'  => $version['writer']->export(),
                                                  'content' => $version['content'],
                                                  'html'    => MiniWiki::WikiToHTML($version['content']));
            }

        if ($this->comments !== null)
            $export['comments'] = $this->comments;

        return $export;
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

        if ($collec->count() != count($mixed))
            throw new ItemNotFoundException('The identifier can\'t be associated to an object in the DB');

        return $collec;
    }

    public static function batchSelect(array $wikis, $options = null)
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

        if (($bits & self::SELECT_COUNT) || ($bits & self::SELECT_VERSION)) {
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
            if (!isset($options[self::SELECT_VERSION]))
                $opts = array('versions' => array('last'));
            else
                $opts = $options[self::SELECT_VERSION];

            $conds = array();
            foreach ($wikis as $w)
            {
                if ($w->versions == null)
                    $w->versions = array();

                $versions = array();
                if (in_array('last', $opts['versions']))
                    $versions[] = $w->count();

                foreach ($opts['versions'] as $version)
                    if ($version != 'last')
                        $versions[] = $version;

                if (!empty($versions))
                    $conds[] = XDB::format('( wid = {?} AND version IN {?} )', $w->id(), $versions);
            }

            $iter = XDB::iterator('SELECT  wid AS id, version, wrote, writer, content
                                     FROM  wiki_version
                                    WHERE  ' . implode(' OR ', $conds));

            $writers = new Collection('User');
            while ($datas = $iter->next()) {
                $writer = $writers->get($datas['writer']);
                if ($writer === false)
                     $writer = $writers->add($datas['writer'])->get($datas['writer']);

                $wikis[$datas['id']]->versions[$datas['version']] = array('wrote' => $datas['wrote'],
                                                                         'writer' => $writer,
                                                                        'content' => $datas['content']);
            }

            if (isset($opts['options']))
                $writers->select($opts['options']);

        }
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
