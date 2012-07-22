<?php
/***************************************************************************
 *  Copyright (C) 2012 Binet Réseau                                        *
 *  http://br.binets.fr/                      *
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
        return array('groups' => 'Group');
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
                   'groups' => array('groups'));
    }

    protected function handler_groups(Collection $remotes, $fields) {
        $this->helper_collection($remotes, array('id' => 'gid',
                                                 'table' => 'remote_groups',
                                                 'field' => 'groups'));
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

    public function site($site = null)
    {
        if ($site != null) {
            $this->site = $site;
            XDB::execute('UPDATE remote SET site = {?} WHERE remid = {?}', $site, $this->id());
        }
        return $this->site;
    }

    public function privkey($privkey = null)
    {
        if ($privkey != null) {
            $this->privkey = $privkey;
            XDB::execute('UPDATE remote SET privkey = {?} WHERE remid = {?}', $privkey, $this->id());
        }
        return $this->privkey;
    }

    public function label($label = null)
    {
        if ($label != null) {
            $this->label = $label;
            XDB::execute('UPDATE remote SET label = {?} WHERE remid = {?}', $label, $this->id());
        }
        return $this->label;
    }

    public function rights(PlFlagSet $rights = null)
    {
        if ($rights != null) {
            // Check rights
            $diff = array_diff($rights->export(), self::availableRights());
            if (!empty($diff)) {
                throw new Exception("Remote rights '" . implode("', '", $diff) . "' don't exist");
            }
            XDB::execute('UPDATE remote SET rights = {?} WHERE remid = {?}',
                $rights->flags(), $this->id());
            $this->rights = $rights;
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
    }

    public function removeRight($right)
    {
        $this->rights->removeFlag($right);
    }

    public function groups(Collection $groups = null) {
        if ($groups != null) {
            $oldGids = $this->groups->ids();
            $newGids = $groups->ids();
            // Remove no longer used groups
            foreach (array_diff($oldGids, $newGids) as $gid) {
                $this->removeGroup($this->groups->get($gid));
            }
            // Add new groups
            foreach (array_diff($newGids, $oldGids) as $gid) {
                $this->addGroup($groups->get($gid));
            }
        }
        return $this->groups;
    }

    public function addGroup(Group $group) {
        XDB::execute('INSERT IGNORE INTO remote_groups SET remid={?}, gid={?}',
            $this->id(), $group->id());
        $this->groups->add($group);
    }

    public function removeGroup(Group $group) {
        XDB::execute('DELETE FROM remote_groups WHERE remid={?} AND gid={?}',
            $this->id(), $group->id());
        $this->groups->remove($group);
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

    public static function batchFrom(array $mixed)
    {
        $collec = new Collection();
        if (!empty($mixed)) {
            $iter = XDB::iterator('SELECT  remid AS id, site
                                     FROM  remote
                                    WHERE  site IN {?}', $mixed);
            while ($r = $iter->next())
                $collec->add(new self($r));
        }

        if (count($mixed) != $collec->count()) {
            throw new ItemNotFoundException('Asking for ' . implode(', ', $mixed) . ' but only found ' . implode(', ', $collec->ids()));
        }

        return $collec;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>