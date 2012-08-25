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

class RemoteSchema extends Schema
{
    public function className() {
        return 'Remote';
    }

    public function table() {
        return 'remote';
    }

    public function id() {
        return 'remid';
    }

    public function fromKey() {
        return 'site';
    }

    public function tableAs() {
        return 'rem';
    }

    public function scalars() {
        return array('site', 'privkey', 'label', 'rights');
    }

    public function objects() {
        return array('rights' => 'PlFlagSet');
    }

    public function collections() {
        return array('groups' => array('Group', 'remote_groups', 'gid'));
    }
}

class RemoteSelect extends Select
{
    protected static $natives = array('site', 'privkey', 'label', 'rights');

    public function className() {
        return 'Remote';
    }

    protected function handlers() {
        return array('main' => self::$natives,
              'collections' => array('groups'));
    }

    public static function base($subs = null) {
        return new RemoteSelect(array_merge(self::$natives, array('groups')), $subs);
    }

    public static function groups() {
        return new RemoteSelect(array_merge(self::$natives, array('groups')),
            array('groups' => GroupSelect::base()));
    }
}

class Remote extends Meta
{
    /*******************************************************************************
         Properties

    *******************************************************************************/

    // site URI
    protected $site = null;

    // the private key
    protected $privkey = null;

    // A nice label
    protected $label = null;

    // PlFlagSet of rights
    protected $rights = null;

    // Collection of groups associated with the site
    protected $groups = null;

    /*******************************************************************************
         Getters & Setters

    *******************************************************************************/

    /**
     * Synchronise $this->rights with the database
     */
    private function updateRights() {
        // Check rights
        $diff = array_diff($this->rights->export(), self::availableRights());
        if (!empty($diff)) {
            throw new Exception("Remote rights '" . implode("', '", $diff) . "' don't exist");
        }
        XDB::execute('UPDATE remote SET rights = {?} WHERE remid = {?}',
            $this->rights->flags(), $this->id());
    }

    public function rights(PlFlagSet $rights = null)
    {
        if ($rights != null) {
            $this->rights = $rights;
            $this->updateRights();
        }
        return $this->rights;
    }

    public function hasRight($right)
    {
        return $this->rights->hasFlag($right);
    }

    public function addRight($right)
    {
        if (!in_array($right, self::availableRights()))
            throw new Exception("Remote rights $right doesn't exist");
        $this->rights->addFlag($right);
        $this->updateRights();
    }

    public function removeRight($right)
    {
        $this->rights->removeFlag($right);
        $this->updateRights();
    }

    /*******************************************************************************
         Data fetcher

    *******************************************************************************/

    /**
     * List of available remote rights
     */
    public static function availableRights()
    {
        return array('names', 'email', 'photo', 'promo', 'sport', 'rights', 'binets_admin');
    }

    public function delete()
    {
        parent::delete();
        XDB::execute('DELETE FROM remote_groups WHERE remid = {?}', $this->id());
        XDB::execute('DELETE FROM remote WHERE remid = {?}', $this->id());
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
