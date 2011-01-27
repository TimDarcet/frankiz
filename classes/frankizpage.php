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

class SkinFileNotFoundException extends Exception
{
    protected $skin;
    protected $file;

    public function __construct($skin, $file)
    {
        $this->skin = unflatten($skin);
        $this->file = unflatten($file);
    }

    public static function merge($exceptions)
    {
        $exceptions = unflatten($exceptions);

        $skins = array();
        $files = array();
        foreach($exceptions as $e) {
            $skins = array_merge($skins, $e->skin);
            $files = array_merge($files, $e->file);
        }

        return new SkinFileNotFoundException($skins, $files);
    }

    public function __toString()
    {
        return 'SkinFileNotFoundException' . "\n" .
                implode(', ', $this->skin) . "\n" .
                implode(', ', $this->file);
    }
}

/**
 * Class for frankiz pages
 */

class FrankizPage extends PlPage
{
    public function __construct()
    {
        parent::__construct();
        // Set the default page
        $this->changeTpl('500.tpl');
    }
    
    private function load_skin()
    {
        global $globals;

        if(!S::has('skin') || S::v('skin') == ""){
            if (Cookie::has('skin')) {
                $skin = Cookie::v('skin');
            } else {
                $skin = (isSmartphone()) ? $globals->smartphone_skin : $globals->skin;
            }
            S::set('skin', $skin);
        } else {
            $skin = S::v('skin');
            if (S::v('auth') >= AUTH_COOKIE && Cookie::v('skin') != $skin){
                Cookie::set('skin', $skin, 300);
            }
        }
        return $skin;
    }

    // TODO: Might be necessary to cache the negative results
    // file_exists caches only positive results.
    private static function bestSkin($file, $folder)
    {
        global $globals;

        $parents = explode('.', S::v('skin', $globals->skin));

        /* Check if their is a skin-specific template/css,
         * otherwise fallback on parent skin while their is one
         */
        while (count($parents) > 0)
        {
            if (file_exists($folder . implode('.', $parents) . '/' . $file))
                return implode('.', $parents) . '/' . $file;

            array_pop($parents);
        }

        // We want to be warned if a template/css can't be loaded
        throw new SkinFileNotFoundException(S::v('skin', $globals->skin), $file);
    }

    public static function getTplPath($tpl)
    {
        return self::bestSkin($tpl, '../templates/');
    }

    public static function getCssPath($css)
    {
        return self::bestSkin($css, '../htdocs/css/');
    }

    public function changeTpl($tpl, $type = SKINNED)
    {
        parent::changeTpl(self::getTplPath($tpl), $type);
    }

    public function coreTpl($tpl, $type = SKINNED)
    {
        parent::changeTpl(self::getCoreTpl($tpl), $type);
    }

    public function addCssLink($css)
    {
        $csss = unflatten($css);
        $exceptions = array();
        foreach ($csss as $css)
            try {
            parent::addCssLink(self::getCssPath($css));
            } catch (SkinFileNotFoundException $e) {
                $exceptions[] = $e;
            }

        return SkinFileNotFoundException::merge($exceptions);
    }

    public function run()
    {
        $skin = $this->load_skin();
        $this->assign('skin', S::v('skin'));
        $this->assign('user', S::user());
        $this->assign('logged', S::logged());

        $this->assign('MiniModules_COL_FLOAT', FrankizMiniModule::get(S::user()->minimodules(FrankizMiniModule::COL_FLOAT)));

        $this->addCssLink(FrankizMiniModule::batchCss());

        // Enable JSON loading of the module only
        if (Env::has('solo')) {
            $this->jsonAssign('content', $this->raw());
            $this->jsonAssign('title'  , $this->get_template_vars('title'));
            $this->jsonAssign('pl_css' , $this->get_template_vars('pl_css'));
            $this->jsonAssign('pl_js'  , $this->get_template_vars('pl_js'));
            $this->runJSon();
        } else {
            $this->_run(self::getTplPath('frankiz.tpl'));
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
