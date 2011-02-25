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
        return array('uid' => 'User', 'gid' => 'Group');
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
    protected $gid = null;
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
                     'win2k'        => 'Windows 2000 Professionnel',
                     'win7'         => 'Windows 7'
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
    
    public function software($software = null)
    {
        if($software != null){
            $this->software = $software;
            XDB::query("UPDATE  msdnaa_keys
                           SET  software = {?}
                         WHERE  id = {?}", $software, $this->id());
        }
        return $this->software;
    }
    
    public function softwareName()
    {
        $s = self::getSoftwares();
        return $s[$this->software];
    }

    public function key($key = null)
    {
        if($key != null){
            $this->key = $key;
            XDB::query("UPDATE  msdnaa_keys
                           SET  `key` = {?}
                         WHERE  id = {?}", $key, $this->id());
        }
        return $this->key;
    }
    
    public function uid($uid = null)
    {
        if($uid != null){
            $this->uid = $uid;
            $this->user = null;
            XDB::query("UPDATE  msdnaa_keys
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
        if($comments != null){
            $this->comments = $comments;
            XDB::query("UPDATE  msdnaa_keys
                           SET  comments = {?}
                         WHERE  id = {?}", $comments, $this->id());
        }
        return $this->comments;
    }
    
    public function admin($admin = null)
    {
        if($admin != null){
            $this->admin = $admin;
            XDB::query("UPDATE  msdnaa_keys
                           SET  admin = {?}
                         WHERE  id = {?}", $admin, $this->id());
        }
        return $this->admin;
    }
    
    public function gid($gid = null)
    {
        if($gid != null){
            $this->gid = $gid;
            XDB::query("UPDATE  msdnaa_keys
                           SET  gid = {?}
                         WHERE  id = {?}", $gid, $this->id());
        }
        return $this->gid;
    }
    
    public function give($user)
    {
        if(!$this->admin())
        {
            $this->uid($user->id());
        }
        if (is_null($user->bestEmail())){
            $user->select(User::SELECT_BASE);
        }
        self::send(array($this), $user);
    }

    public static function send($keys, $user = null)
    {
        if($user == null){
            $user = S::user();
        }

        $mail = new FrankizMailer('licenses/licenses_key.mail.tpl');
        $mail->assign('keys', $keys);
        $mail->assign('multiple', count($keys) > 1);
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
            if($value != null) {
                $req[] = XDB::format($key . ' = {?}', $value);
            } else {
                $req[] = XDB::format('ISNULL(' . $key . ')');
            }
            
        }
        $keys = XDB::query('SELECT * FROM msdnaa_keys WHERE ' . implode(' AND ', $req))->fetchAllAssoc();
        foreach($keys as $key => $value)
        {
            $keys[$key] = new License($keys[$key]);
        }
        return $keys;
    }
    
    public static function fetchCurrentUser(){
        return self::fetch(array('uid' => S::user()->id(), 'gid' => null));
    }
    
    public static function adminKey($software){
        $keys = self::fetch(array('software' => $software, 'admin' => true));
        if(count($keys) >= 1) {
            return array_pop($keys);
        }
        return false;
    }
    
    public static function givenKeys($software, $uid){
        $keys = self::fetch(array('software' => $software, 'uid' => $uid, 'gid' => null));
        if(count($keys) >= 1) {
            return $keys;
        }
        return false;
    }
    
    public static function fetchFreeKeys($software){
        self::fetch(array('software' => $software, 'uid' => null, 'gid' => null, 'admin' => false));
    }        
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>
