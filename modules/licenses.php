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

class licensesModule extends PLModule
{
    function handlers()
    {
        return array('licenses'                => $this->make_hook('licenses',                AUTH_MDP),
                     'licenses/cluf'           => $this->make_hook('licenses_CLUF',           AUTH_MDP),
                     'licenses/reason'          => $this->make_hook('licenses_reason',          AUTH_MDP),
                     'licenses/final'          => $this->make_hook('licenses_final',          AUTH_MDP),
                    );
    }


    public function handler_licenses($page)
    {
        $page->changeTpl('licenses/licenses.tpl');
        $page->assign('title', "Demande de license MSDNAA");
        $softwares = License::getSoftwares();
        $page->assign('softwares', $softwares);

        // list owned licenses
        $licenses = License::fetchCurrentUser();
        $page->assign('owned_licenses', $licenses);
    }

    public function handler_licenses_CLUF($page)
    {
        $softwares = License::getSoftwares();

        //User asked for a license, let's print the user's contract
        if(Post::has('disagree') || !Post::has('software') || !in_array(Post::s('software'), array_keys($softwares))) {
            $this->handler_licenses($page);
        } else {
            $page->changeTpl('licenses/licenses_CLUF.tpl');
            $page->assign('title', "Demande de licence pour {$softwares[Post::s('software')]} : Contrat utilisateur");
            $page->assign('software', Post::s('software'));
            $page->assign('software_name', $softwares[Post::s('software')]);
        }
    }

    public function handler_licenses_reason($page)
    {
        $softwares = License::getSoftwares();

        if(Post::has('disagree') || !Post::has('agree') || !Post::has('software') || !in_array(Post::s('software'), array_keys($softwares))) {
            $this->handler_licenses($page);
        } else {
            $already_has = License::givenKeys(Post::s('software'), S::user()->id());
            $software_rare = in_array(Post::v('software'), License::getRareSoftwares());
            if(S::user()->hasRights(Group::from('on_platal'), Rights::member()) && !$already_has && !$software_rare) {
                $this->handler_licenses_final($page, true);
            } else {
                $page->changeTpl('licenses/licenses_reason.tpl');
                $page->assign('title', "Demande de licence pour {$softwares[Post::v('software')]} : raison");
                $page->assign('software', Post::v('software'));
                $page->assign('software_name', $softwares[Post::v('software')]);
                $page->assign('software_rare', $software_rare);
                $page->assign('already_has', $already_has);
            }
        }
    }

    public function handler_licenses_final($page, $no_reason=false)
    {
        $softwares = License::getSoftwares();
        $keys = array();
        
        if(Post::has('disagree') || (!$no_reason && (!Post::has('reason') || Post::v('reason')=="")) || !Post::has('software') || !in_array(Post::v('software'), array_keys($softwares))){
            $this->handler_licenses($page);
        } else {
            $page->changeTpl('licenses/licenses_final.tpl');
            $page->assign('title', "Demande de licence pour {$softwares[Post::v('software')]}");
            $page->assign('software', Post::s('software'));
            $page->assign('software_name', $softwares[Post::s('software')]);  
            
            if($key = License::adminKey(Post::s('software')))
            {
                $key->give(S::user());
                $page->assign('direct', true);
            } 
            elseif(Post::has('resend'))
            {
                License::send(License::givenKeys(Post::s('software'), S::user()->id()));
                $page->assign('direct', true);
            } else {
                $lv = new LicensesValidate(Post::s('software'), Post::s('reason'));
                $v = new Validate(array(
                    'writer'    => S::user(),
                    'group'     => Group::from('licenses'),
                    'item'      => $lv,
                    'type'      => 'licenses'));
                $v->insert();
                $page->assign('direct', false);
            }
        }
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
