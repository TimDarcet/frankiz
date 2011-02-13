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
class LicenseSchema extends Schema
{
    public function className() {
        return 'License';
    }

    public function table() {
        return 'msdnaa_keys';
    }

    public function id() {
        return 'id';
    }

    public function tableAs() {
        return 'a';
    }

    public function scalars() {
        return array('software', 'key', 'admin', 'comments');
    }

    public function objects() {
        return array('uid' => 'User');
    }

    public function collections() {
        return array();
    }
}

/*class LicenseSelect extends Select
{
    protected static $natives = array('software', 'key', 'admin', 'comments', 'uid');

    public function className() {
        return 'License';
    }
}*/

class License extends Meta
{
    /*******************************************************************************
         Constants

    *******************************************************************************/



    /*******************************************************************************
         Properties

    *******************************************************************************/
    
    protected $software = null;
    protected $key = null;
    protected $uid = null;
    protected $user = null;
    protected $comments = null;
    protected $admin = false;

    /*******************************************************************************
         Getters & Setters

    *******************************************************************************/

    static public function getSoftwares()
    {
        return array('visualstudio' => 'Visual Studio .NET',
                     'winxp'        => 'Windows XP Professionnel',
                     'winvista'     => 'Windows Vista Business',
                     '2k3serv'      => 'Windows Serveur 2003',
                     '2k3access'    => 'Access 2003',
                     '2k3onenote'   => 'One Note 2003',
                     '2k3visiopro'  => 'Visio Professionnel 2003',
                     'win2k'        => 'Windows 2000 Professionnel'
                    );
    }
    
    public static function getRareSoftwares()
    {
        return array("2k3serv", "2k3access", "2k3onenote", "2k3visiopro");
    }
    
    public static function getDomainSoftwares()
    {
        return array("winxp", "winvista", "win2k");
    }
    
    public function software()
    {
        return $this->software;
    }
    
    public function softwareName()
    {
        $s = self::getSoftwares();
        return $s[$this->software];
    }

    public function key()
    {
        return $this->key;
    }
    
    public function uid($uid = null)
    {
        if($uid != null){
            $this->uid = $uid;
            $this->user = null;
            XDB::request("UPDATE  msdna_keys
                             SET  uid = {?}
                           WHERE  id = {?}", $uid, $this->id());
        }
        return $this->uid;
    }
    
    public function user()
    {
        if($this->user == null && $this->uid != null)
        {
            $this->user = new User($this->uid);
        }
        return $this->user;
    }
    
    public function comments($comments = null)
    {
        if($set != null)
        {
            XDB::request('UPDATE  msdnaa_keys 
                             SET  comments = {?}
                           WHERE  key = {?} AND software = {?} AND admin = 0', $comments);
            $this->comments = $comments;
        }
        return $this->comments;
    }
    
    public function admin()
    {
        return $this->admin;
    }
    
    public function give($user)
    {
        $this->uid($user->id());
        if (is_null($this->user()->bestEmail())){
            $this->user()->select(User::SELECT_BASE);
        }
        self::send(array($this), $user);
    }

    public static function send($keys, $user = null)
    {
        if($user == null){
            $user = S::user();
        }
        
        $mail = new FrankizMailer('licenses/license_key.mail.tpl');
        $mail->assign('software_name', $this->softwareName());
        $mail->assign('keys', $keys);
        $mail->assign('pub_domain', in_array(Post::v('software'), License::getDomainSoftwares()));
        
        $mail->Subject = '[Frankiz] Ta licence MSDNAA';

        $mail->SetFrom('msdnaa-licences@frankiz.polytechnique.fr', 'admin@windows');
        $mail->AddAddress($user->bestEmail(), $user->displayName());
        $mail->AddCC('msdnaa-licences@frankiz.polytechnique.fr', 'admin@windows');
        $mail->Send(false);
    }
    
    /*******************************************************************************
         Data fetcher
             (batchFrom, batchSelect, fillFromArray, …)
    *******************************************************************************/
                                 
    //To be improved using Meta methods
    
    public static function fetch($conds = array())
    {
        $req = array();
        foreach($conds as $key => $value)
        {
            $req[] = XDB::format($key . ' = {?}', $value);
        }
        $keys = XDB::query('SELECT * FROM msdnaa_keys WHERE ' . implode(' AND ', $req))->fetchAllAssoc();
        foreach($keys as $key)
        {
            $keys[$key] = new License($keys[$key]);
        }
        return $keys;
    }
    
    public static function fetchCurrentUser(){
        return self::fetch(array('uid', S::user()->id()));
    }
    
    public static function adminKey($software){
        $keys = self::fetch(array('software' => $software, 'admin' => true));
        if(count($keys) == 1) {
            return array_pop($keys);
        }
        return false;
    }
    
    public static function givenKeys($software, $uid){
        $keys = self::fetch(array('software' => $software, 'uid' => $uid));
        if(count($keys) >= 1) {
            return $keys;
        }
        return false;
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
