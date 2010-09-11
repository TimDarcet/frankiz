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

/**
 * Base class for Frankiz MiniModules (these are the small boxes displayed on the left and right column 
 * of the website)
 */

abstract class FrankizMiniModule
{
    const MAIN_LEFT = 1;
    const MAIN_MIDDLE = 2;
    const MAIN_RIGHT = 3;
    const FLOAT_RIGHT = 4;

    const auth  = AUTH_PUBLIC;
    const perms = '';
    const js    = '';

    protected $tpl = null;
    protected $header_tpl = null;
    protected $titre = "Not Defined!";

    private $params = array();

    public function get_params()
    {
        return $this->params;
    }

    /**
     * Returns the title of the module
     * This is different from the identifier.
     * @return Title of the module
     */
    public function get_titre()
    {
        return $this->titre;
    }

    public function get_template()
    {
        return FrankizPage::getTplPath($this->tpl);
    }

    /**
     * Assigne une variable pour la template du minimodule uniquement. Ces variables seront accessibles dans 
     * $minimodule.var_name à l'intérieur des template.
     */
    protected function assign($key, $value)
    {
        $this->params[$key] = $value;
    }

    /* static stuff */
    private static $minimodules = array();
    private static $minimodules_layout = array(self::MAIN_LEFT=>array(), self::MAIN_MIDDLE=>array(), self::MAIN_RIGHT=>array(), self::FLOAT_RIGHT=>array());
    private static $cols;
    private static $oneShotName = '';

    //stores the name of the module being executed
    private static $curr_name;

    /**
     * preload the list of minimodules
     */
    public static function preload($_cols)
    {
         if (is_array($_cols)) {
            self::$cols = array_merge(self::$cols, $_cols);
         } else {
            self::$cols[] = $_cols;
         }
    }

    public static function oneShot($name)
    {
        self::$oneShotName = $name;
        self::run();
    }

    public static function run($page)
    {
        if (self::$oneShotName == '')
        {
            if (S::logged())
            {
                $res = XDB::query('SELECT m.name name, um.col col, um.row row
                                     FROM users_minimodules AS um
                               INNER JOIN minimodules AS m
                                       ON m.name = um.name
                                    WHERE um.uid = {?} AND um.col IN '.XDB::formatArray(self::$cols).'
                                    ORDER BY um.col, um.row',
                                  S::user()->id());
            } else {
                $res = XDB::query('SELECT name, col, row
                                     FROM minimodules
                                    WHERE bydefault = 1 AND col IN '.XDB::formatArray(self::$cols).'
                                    ORDER BY col, row');
            }
        }
        else
        {
            $res = XDB::query('SELECT name, col, row
                                 FROM minimodules
                                WHERE name = {?}
                                ORDER BY col, row',
                              self::$oneShotName);
        }
        $minimodules_list = $res->fetchAllAssoc();

        foreach($minimodules_list as $minimodule)
        {
            $name = $minimodule['name'];

            if (!array_key_exists($name, self::$minimodules))
            {
                $localDatas = self::getlocalData($name);
                if (Platal::session()->checkAuthAndPerms($localDatas['auth'], $localDatas['perms']))
                {
                    $cls = $localDatas['name'];
                    $localDatas['object'] = new $cls();
                    self::$minimodules[$name] = $localDatas;

                    // Load the css file with the minimodule's name
                    try {
                        $page->addCssLink('minimodules/' . $name . '.css');
                    } catch (SkinFileNotFoundException $e) {
                    }

                    self::$minimodules_layout[$minimodule['col']][$minimodule['row']] = $name;
                }
            }
        }
    }

    // Load datas contained in the Minimodule (auth, perms, js), but doesn't instanciate them
    public static function getlocalData($name)
    {
        global $globals;
        $cls=ucfirst($name)."MiniModule";
        $path=$globals->spoolroot . "/minimodules/" . strtolower($name) . ".php";
        include_once $path;

        $auth  = constant($cls.'::auth');
        $perms = constant($cls.'::perms');
        $js    = constant($cls.'::js');

        return array(  'name' => $cls,
                       'auth' => $auth,
                      'perms' => $perms,
                         'js' => $js);
    }

    /**
     * Renvoie un tableau des descriptions des minimodules indexé par les
     * identifiants des minimodules.
     */
    public static function get_minimodules()
    {
        $res=array();
        foreach(self::$minimodules as $name => $data)
        {
            $res[$name] = $data['object'];
        }
        return $res;
    }

    public static function get_layout()
    {
        return self::$minimodules_layout;
    }

    public static function get_js()
    {
        $res=array();
        foreach(self::$minimodules as $name => $data)
        {
            $res[$name] = $data['js'];
        }
        return $res;
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
