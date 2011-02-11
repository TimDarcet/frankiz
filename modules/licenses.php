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
        $page->assign('title', "Les licences");
        $softwares = $this->get_softwares();
        $page->assign('softwares', $softwares);

        // list owned licenses
        $licences = License::fetchCurrentUser();
        $page->assign('owned_licenses', $licenses);
        
        // list pending requests
        //TODO
        $page->assign('requests', $requests);
    }

    public function handler_licenses_CLUF($page)
    {

        $softwares = License::getSoftwares();

        //User asked for a license, let's print the user's contract
        if(Post::has('refus') || !Post::has('software') || !in_array(Post::s('software'), array_keys($softwares))) {
            $this->handler_licenses_final($page);
        } else {
            $page->changeTpl('profil/licenses_CLUF.tpl');
            $page->assign('title', "Demande de licence pour {$softwares[Post::s('software')]} : Contrat utilisateur");
            $page->assign('software', Post::s('software'));
            $page->assign('software_name', $softwares[Post::s('software')]);
        }
    }

    public function handler_licenses_reason($page)
    {
        $softwares = License::getSoftwares();

        if(Post::has('refus') || !Post::has('accord') || !Post::has('software') || !in_array(Post::s('software'), array_keys($softwares))) {
            $this->handler_licenses($page);
        } else {
            if(S::user()->hasRights(Group::from('on_platal'), Rights::member())) {
                $this->handler_licences_final($page, true);
            } else {
                $page->changeTpl('profil/licenses_reason.tpl');
                $page->assign('title', "Demande de license pour {$softwares[Post::v('software')]} : reason");
                $page->assign('software', Post::v('software'));
                $page->assign('software_name', $softwares[Post::v('software')]);
                $page->assign('software_rare', in_array(Post::v('software'), array("2k3serv", "2k3access", "2k3onenote", "2k3visiopro")));
            }
        }
    }

    public function handler_licenses_final($page, $no_reason=false)
    {
        $softwares=$this->get_softwares();
        
        if(Post::has('refus') || (!$no_reason && (!Post::has('reason') || Post::v('reason')=="")) || !Post::has('software') || !in_array(Post::v('software'), array_keys($softwares))){
            $this->handler_licenses($page);
        } else {
            $page->changeTpl('profil/licenses_final.tpl');
            $page->assign('title', "Les licenses");
            
            $admin = XDB::query('SELECT key FROM msdnaa_keys WHERE software = {?} AND admin = 1 LIMIT 1', Post::s('software'));
            if($admin->numRows == 0){
                // On regarde s'il y a déjà une clé ou  une demande en attente pour le software en question
                $given = XDB::query("SELECT key FROM msdnaa_keys WHERE uid={?} AND given = 1 LIMIT 1");
                $already_has=($given->numRows() > 0);
                if($already_has) {
                    $key = $given->fetchOneCell();
                }

                $pending = XDB::query("SELECT 0 FROM msdnaa_requests WHERE uid={?} AND software={?}", S::user()->id(), Post::s('software'));
                $already_asked=($pending->numRows() > 0);
            } else {
                $already_has = false;
                $already_asked = false;
                $key = $admin->fetchOneCell();
            }

            $page->assign('already_asked', $already_asked);
            $page->assign('already_has', $already_has);
            $page->assign('software', Post::s('software'));
            $page->assign('software_name', $softwares[Post::s('software')]);

            $softwares_domain = array("winxp", "winvista", "win2k");

            $email = S::user()->bestEmail();

            if(isset($cle)){
                $mail = new PlMailer('profil/licenses_cle.mail.tpl');
                $mail->assign('nom', S::v('nom'));
                $mail->assign('prenom', S::v('prenom'));
                $mail->assign('cle', $cle);
                $mail->assign('pub_domaine', in_array(Post::v('software'), $softwares_domain));
                $mail->assign('software_name', $softwares[Post::v('software')]);
                $mail->addTo($email);
                $mail->send();

                $mail=new PlMailer('profil/licenses_cle_admin.mail.tpl');
                $mail->assign('nom', S::v('nom'));
                                $mail->assign('prenom', S::v('prenom'));
                $mail->assign('promo', S::v('promo'));
                $mail->assign('cle', $cle);
                $mail->assign('software_name', $softwares[Post::v('software')]);
                $mail->setFrom($email);
                $mail->send();
            } else {
    /*          $mail = new PlMailer('profil/licenses_nocle.mail.tpl');
                $mail->assign('nom', S::v('nom'));
                $mail->assign('prenom', S::v('prenom'));
                $mail->assign('pub_domaine', in_array(Post::v('software'), $softwares_domaine));
                $mail->assign('software_name', $softwares[Post::v('software']));
                $mail->addTo($mail);
                $mail->send();
    */
                $mail=new PlMailer('profil/licenses_nocle_admin.mail.tpl');
                $mail->assign('nom', S::v('nom'));
                $mail->assign('prenom', S::v('prenom'));
                $mail->assign('promo', S::v('promo'));
                $mail->assign('software_name', $softwares[Post::v('software')]);
                $mail->setFrom($email);
                $mail->send();

                //Insertion dans la DB
                XDB::query("INSERT INTO msdnaa_requests SET reason={?}, software={?}, uid={?}", Post::v('reason'), Post::v('software'), S::user()->id());

            }

        }
    }

}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
