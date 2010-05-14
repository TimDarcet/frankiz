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

class TrombinoModule extends PLModule
{
    function handlers()
    {
        return array(
            'tol' => $this->make_hook('tol', 0)   // TODO : fix AUTH levels
            );
    }

    function handler_tol(&$page) 
    {
    
        // $cond = new PFC_And(array());
        
        //$cond = new UFC_Name(Env::t('name'), UFC_Name::LASTNAME&UFC_Name::FIRSTNAME&UFC_Name::NICKNAME, UFC_Name::CONTAINS);

        $cond =  new UFC_Hruid('henri.jouhaud@polytechnique.edu');
        
        $uf = new UserFilter($cond);
//        
        $users = $uf->getUsers(new PlLimit(20,0));
//        
        //print_r($users);
//    
        $page->assign('title', 'Trombino On Line');
        $page->assign('results', $users);
        $page->changeTpl('trombino/tol.tpl');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
