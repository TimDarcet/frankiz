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
        $this->assign('title', '');
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

    public function filteredFetch($skin, array& $infos = null)
    {
        global $globals, $platal;

        $this->register_prefilter('trimwhitespace');
        $this->register_prefilter('form_force_encodings');
        $this->register_prefilter('wiki_include');
        $this->register_prefilter('core_include');
        $this->register_prefilter('if_rewrites');

        $this->assign_by_ref('platal', $platal);
        $this->assign_by_ref('globals', $globals);

        $this->register_modifier('escape_html', 'escape_html');
        $this->default_modifiers = Array('@escape_html');

        if (S::i('auth') <= AUTH_PUBLIC) {
            $this->register_outputfilter('hide_emails');
        }

        if ($infos !== null) {
            $START_SMARTY = microtime(true);
        }

        $result = $this->fetch($skin);

        if ($infos !== null) {
            $infos['time'] = microtime(true) - $START_SMARTY;
        }

        return $result;
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
            $this->assign('quick_validate', array());
            if (S::user()->castes(Rights::admin())->count() > 0) {
                $validate_filter = new ValidateFilter(new VFC_User(S::user()));
                $validates = $validate_filter->get()->select(ValidateSelect::quick());
                $quick_validate = $validates->split('group');
                $this->assign('quick_validate', $quick_validate);
            }

            $request_filter = new ValidateFilter(new VFC_Writer(S::user()));
            $requests = $request_filter->get()->select(ValidateSelect::quick());
            $this->assign('quick_requests', $requests);

            $this->_run(self::getTplPath('frankiz.tpl'));
        }
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
