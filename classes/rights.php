<?php
/***************************************************************************
 *  Copyright (C) 2009 Binet Réseau                                        *
 *  http://www.polytechnique.fr/eleves/binets/reseau/                      *
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

class InsufficientRightsException extends Exception
{
    private $group;
    private $actual_rights;
    private $needed_rights;

    public function group($group = null)
    {
        if ($group !== null)
            $this->group = $group;

        return $this->group;
    }

    public function actualRights($rights = null)
    {
        if ($rights !== null)
            $this->actual_rights = $rights;

        return $this->actual_rights;
    }

    public function neededRights($rights = null)
    {
        if ($rights !== null)
            $this->needed_rights = $rights;

        return $this->needed_rights;
    }
}

class Rights
{
    protected $rights = null;

    public function __construct($rights = 'member')
    {
        if (in_array($rights, self::rights()))
            $this->rights = $rights;
        else
            throw new Exception("Rights $rights doesn't exist");
    }

    public static function __callStatic($name, $arguments)
    {
        return new self(strtolower($name));
    }

    public static function rights()
    {
        return array('super', 'admin', 'logic', 'member', 'friend', 'restricted', 'everybody');
    }

    public function __toString()
    {
    	return $this->rights;
    }

    public function isMe(Rights $rights)
    {
        return $this->rights == $rights->rights;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
